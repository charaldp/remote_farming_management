class Car{
    constructor( wheel, wheelsPositions, engine, mass, transmission, carGeo ) {
      this.wheelGroup = new THREE.Group();
      if ( wheel instanceof Wheel ) {
        var min = 0, max = 0;
        for ( var i = 0; i < wheelsPositions.length; i++ ) {
          wheel.group.position.x = wheelsPositions[i].x;
          wheel.group.position.z = wheelsPositions[i].y;
          this.wheelGroup.add(wheel.group.clone())
          wheel.group.rotation.y = Math.PI;
          wheel.group.position.z *= -1;
          this.wheelGroup.add(wheel.group.clone())
          wheel.group.rotation.y = 0;
          if (wheel.group.position.x > max) max = wheel.group.position.x;
          if (wheel.group.position.x < min) min = wheel.group.position.x;
        }
      }
      this.engine = engine;
      this.transmission = transmission;
      this._mass = mass;
      this.frontVector = new THREE.Vector3( 1, 0, 0 );
      this.speed = new THREE.Vector3( 0, 0, 0 );
      this.rotationalSpeed = new THREE.Euler( 0, 0, 0 );
      this.center = new THREE.Vector3( 0, 2 * wheel.R, 0 );
      this.length = max - min;
      this.ackermanSteering = {
        maxWheelSteer: 50 * Math.PI / 180 /*50 degrees max*/,
        ackermanPoint: 'Nan' /* 'Nan': rotating araound a point at infinite a.k.a. Going Straight*/,
        steeringWheelPosition: 0};
      this._wheel = wheel;
      this.group = new THREE.Group();
      this.group.add(this.wheelGroup);
      this.carMesh = new THREE.Mesh( carGeo, new THREE.MeshPhysicalMaterial() );//new THREE.BoxGeometry( 30, 4, 18).translate( 0, 3, 0 ), new THREE.MeshPhysicalMaterial());
      this.group.add(this.carMesh);
    }
    rotateWheels( timestep ) {
      for ( var i = 0; i < this.wheelGroup.children.length; i++ ) {
        this.wheelGroup.children[i].rotation.z -= this.speed.length() / ( this._wheel.R *  Math.PI / 2) * timestep * Math.sign(this.wheelGroup.children[i].position.z);
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
      this.wheelGroup.children[2].rotation.y = this.ackermanSteering.steeringWheelPosition * (this.ackermanSteering.steeringWheelPosition < 0 ? 1 : 0.7);
      this.wheelGroup.children[3].rotation.y = - this.ackermanSteering.steeringWheelPosition * (this.ackermanSteering.steeringWheelPosition > 0 ? 1 : 0.7);
    }
    updateLoad( timestep ) {
      this.engine._currentTorque = 4;
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
