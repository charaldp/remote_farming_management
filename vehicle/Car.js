class Car{
    constructor( wheel, wheelsPositions, engine, mass, transmission, carGeo, spawnPosition /*[Coordinate Vector, Spawn Rotation (Degrees)]*/, steeringCenter ) {
      this.wheelGroup = new THREE.Group();
      if ( wheel instanceof Wheel ) {
        var min = 0, max = 0;
        for ( var i = 0; i < wheelsPositions.length; i++ ) {
          wheel.group.position.x = wheelsPositions[i].x;
          wheel.group.position.z = wheelsPositions[i].y;
          if(wheelsPositions[i].x > 0) this.frontWheelsAxlesWidth = 2 * wheelsPositions[i].y;
          this.wheelGroup.add(wheel.group.clone())
          wheel.group.rotation.y = Math.PI;
          wheel.group.position.z *= -1;
          this.wheelGroup.add(wheel.group.clone())
          wheel.group.rotation.y = 0;
          if (wheel.group.position.x > max) max = wheel.group.position.x;
          if (wheel.group.position.x < min) min = wheel.group.position.x;

        }
        wheel.group.position.add(spawnPosition.position);
        wheel.group.rotation.y = spawnPosition.rotation * Math.PI / 180;
      }
      this.engine = engine;
      this.transmission = transmission;
      this._mass = mass;
      this.frontVector = new THREE.Vector3( 1, 0, 0 ).applyAxisAngle(new THREE.Vector3( 0, 1, 0 ), spawnPosition.rotation * Math.PI / 180 );
      this.speed = new THREE.Vector3( 0, 0, 0 );
      this.acceleration = new THREE.Vector3( 0, 0, 0 );
      this._differential_rot = 0;
      this.rotationalSpeed = new THREE.Euler( 0, 0, 0 );
      this.center = (new THREE.Vector3( 0, 2 * wheel.R, 0 )).add( spawnPosition.position );
      this.length = max - min;
      this.ackermanSteering = {
        maxWheelSteer: 50 * Math.PI / 180 /*50 degrees max*/,
        ackermanPoint: 'Nan' /* 'Nan': rotating around a point at infinite a.k.a. Going Straight*/,
        steeringWheelPosition: 0};
      this._wheel = wheel;
      this.group = new THREE.Group();
      this.group.add(this.wheelGroup);
      this.carMesh = new THREE.Mesh( carGeo, new THREE.MeshPhysicalMaterial(/*{wireframe : true}*/) );//new THREE.BoxGeometry( 30, 4, 18).translate( 0, 3, 0 ), new THREE.MeshPhysicalMaterial());

      this.group.add(this.carMesh);
    }
    rotateWheels( timestep ) {
      for ( var i = 0; i < this.wheelGroup.children.length; i++ ) {
        this.wheelGroup.children[i].rotation.z -= this.speed.x / ( this._wheel.R *  Math.PI / 2) * timestep * Math.sign(this.wheelGroup.children[i].position.z);
      }
    }
    moveCar( timestep ) {
      for ( var i = 0; i < this.wheelGroup.children.length; i++ ) {
        this.wheelGroup.children[i].position.x += this.speed.x * timestep;
        this.wheelGroup.children[i].position.y += this.speed.y * timestep;
        this.wheelGroup.children[i].position.z += this.speed.z * timestep;
      }
      this.carMesh.position.x += this.speed.x * timestep;
      this.carMesh.position.y += this.speed.y * timestep;
      this.carMesh.position.z += this.speed.z * timestep;
      this.center.add(this.speed.clone().multiplyScalar(timestep));
    }
    steerWheels( timestep, steerSpeed ) {
      this.ackermanSteering.steeringWheelPosition += steerSpeed * timestep;
      if (Math.abs(this.ackermanSteering.steeringWheelPosition) > this.ackermanSteering.maxWheelSteer ) this.ackermanSteering.steeringWheelPosition = Math.sign(this.ackermanSteering.steeringWheelPosition) * this.ackermanSteering.maxWheelSteer;
      // th_out = acot(cot(th_in) - d / l)
      let th_out = Math.PI / 2 - Math.atan( 1 / Math.tan( Math.abs(this.ackermanSteering.steeringWheelPosition) ) + this.frontWheelsAxlesWidth / this.length );
      this.wheelGroup.children[2].rotation.y = this.ackermanSteering.steeringWheelPosition < 0 ? this.ackermanSteering.steeringWheelPosition : th_out;
      this.wheelGroup.children[3].rotation.y = this.ackermanSteering.steeringWheelPosition > 0 ? - this.ackermanSteering.steeringWheelPosition : th_out;
      this.ackermanSteering.ackermanPoint = ( this.ackermanSteering.steeringWheelPosition > 0 ? 1 : -1 ) * ( this.length / Math.tan(th_out) - this.frontWheelsAxlesWidth / 2 );
    }

    updateLoad( ) {
      this.engine._load_inertia = /*Phys.activationFunction( this.transmission.clutch, 0.5, 15 ) */ this._mass * this._wheel.R * (this.transmission.gear === false ? 0 : Math.abs(this.transmission.gearbox[this.transmission.gear]) );
    }

    updateClutchConnection( timestep ) {
      // timestep /= 2;
      let transmission_rot = this.transmission.gear === false ? this.engine._rot : this._differential_rot / this.transmission.gearbox[this.transmission.gear];
      let totalInertia = this.engine._rot * this.engine._shaft_inertia + transmission_rot * this.engine._load_inertia;
      let targetRot = (this.engine._rot * this.engine._shaft_inertia + transmission_rot * this.engine._load_inertia) / (this.engine._shaft_inertia + this.engine._load_inertia);
      // console.log(this.engine._rot, this._differential_rot);
      // console.log(this.engine._shaft_inertia, this.engine._load_inertia);
      let synchronizationCoeff = transmission_rot / this.engine._rot;
      this.transmission.clutchFrictionCoeff = Phys.clutchSigmoidFrictionCoeff(this.transmission.clutch, 15, synchronizationCoeff );
      console.log(this.transmission.clutchFrictionCoeff);
      this.engine._rot += (targetRot - this.engine._rot) * Math.pow(0.98, timestep * this.transmission.clutchFrictionCoeff * this.engine._load_inertia / ( this.engine._load_inertia + this.engine._shaft_inertia ) );
      // console.log(this.engine._rot);
      let posterior_transmission_rot = transmission_rot + (targetRot - transmission_rot) * Math.pow( 0.98, timestep * this.transmission.clutchFrictionCoeff * this.engine._shaft_inertia / ( this.engine._load_inertia + this.engine._shaft_inertia ) );
      this._differential_rot = this.transmission.gear !== false ? posterior_transmission_rot * this.transmission.gearbox[this.transmission.gear] : this.speed.getComponent( 0 ) / this._wheel.R;
      let tempSpeed = this.speed.getComponent( 0 );
      this.speed.x = this._differential_rot * this._wheel.R;
      this.acceleration.x = ( this.speed.x - tempSpeed ) / ( timestep / 1000 );
    }

    applyTransformation( timestep ) {

    }

    static makeCarGeo( frontToRearPoints, wheelsCentersPositions, radius, width, bevelThickness) {
      var extrudeShape = new THREE.Shape();
			extrudeShape.moveTo( frontToRearPoints[0][0], frontToRearPoints[0][1] );
      for ( var i = 1; i < frontToRearPoints.length; i++ )
        extrudeShape.lineTo( frontToRearPoints[i][0], frontToRearPoints[i][1] );
      for ( var i = 0; i < wheelsCentersPositions.length; i++ )
        extrudeShape.absarc( wheelsCentersPositions[i], 0, radius + bevelThickness, Math.PI, 0, true );
			var extrudeSettings = {
				steps: 1,
				depth: width - 2 * bevelThickness,
				bevelEnabled: true,
				bevelThickness: bevelThickness,
				bevelSize: bevelThickness,
				bevelOffset: bevelThickness,
				bevelSegments: 10
			};
      var car_geo = new THREE.ExtrudeGeometry( extrudeShape, extrudeSettings );
      return (car_geo.translate( 0, radius, - width / 2 + bevelThickness));
    }
}
