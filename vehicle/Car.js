class Car{
    constructor( wheel, wheelsPositions, engine, mass, transmission, carGeo, spawnPosition /*[Coordinate Vector, Spawn Rotation (Degrees)]*/, camera, steeringCenter ) {
      this.wheelGroup = new THREE.Group();
      if ( wheel instanceof Wheel ) {
        this.min = 0;
        this.max = 0;
        for ( var i = 0; i < wheelsPositions.length; i++ ) {
          //wheel.group.position.x = wheelsPositions[i].x;
          //wheel.group.position.z = wheelsPositions[i].y;
          if( wheelsPositions[i].x > 0) this.frontWheelsAxlesWidth = 2 * wheelsPositions[i].y;
          this.wheelGroup.add(wheel.group.clone())
          //wheel.group.rotation.y = Math.PI;
          //wheel.group.position.z *= -1;
          this.wheelGroup.add(wheel.group.clone())
          //wheel.group.rotation.y = 0;
          if (wheelsPositions[i].x > this.max) this.max = wheelsPositions[i].x;
          if (wheelsPositions[i].x < this.min) this.min = wheelsPositions[i].x;

        }
      }
      this.upVector = new THREE.Vector3( 0, 1, 0 );
      this.frontVector = new THREE.Vector3( 1, 0, 0 ).applyAxisAngle( this.upVector, spawnPosition.rotation * Math.PI / 180 );
      this.camera = camera;
      this.wheelMatrices = [];
      for ( var i = 0; i < this.wheelGroup.children.length; i++ ) {
        this.wheelMatrices.push([new THREE.Matrix4(), new THREE.Matrix4()]);
        this.wheelMatrices[i][0].makeRotationAxis( this.upVector,  Math.PI * ( i % 2 == 0 ? 1 : 0 ) );
        this.wheelMatrices[i][0].setPosition( new THREE.Vector3(wheelsPositions[ Math.floor(i / 2)].x, wheel.R, wheelsPositions[ Math.floor(i / 2)].y * ( i % 2 == 0 ? -1 : 1 ) ));
        this.wheelMatrices[i][1].copy(this.wheelMatrices[i][0]);
        // this.wheelQuaternions[i];
        console.log(this.wheelMatrices[i], wheelsPositions[ Math.floor(i / 2)].y);
      }
      this.engine = engine;
      this.transmission = transmission;
      this._mass = mass;
      this.speed = 0;//new THREE.Vector3( 0, 0, 0 );
      this.acceleration = 0;//new THREE.Vector3( 0, 0, 0 );
      this._differential_rot = 0;
      this.rotationalSpeed = new THREE.Euler( 0, 0, 0 );
      this.centerTransformation = new THREE.Matrix4();
      this.centerTransformation.makeRotationAxis(  this.upVector, spawnPosition.rotation * Math.PI / 180 );
      this.centerTransformation.setPosition( spawnPosition.position );
      this.center = new THREE.Vector3( this.centerTransformation[12], this.centerTransformation[13], this.centerTransformation[14] );
      for ( var i = 0; i < this.wheelGroup.children.length; i++ ) {
        this.wheelGroup.children[i].matrixAutoUpdate = false;
        this.wheelGroup.children[i].matrix.copy( (this.centerTransformation.clone()).multiply(this.wheelMatrices[i][1]) );
        // if ( this.wheelGroup.children[i].matrixWorld.determinant() !== 0 )
          // this.wheelGroup.children[i].setRotationFromMatrix ( this.wheelGroup.children[i].matrix );
      }
      this.length = this.max - this.min;
      console.log(this.length);
      this.ackermanSteering = {
        maxWheelSteer: 50 * Math.PI / 180 /*50 degrees max*/,
        ackermanPoint: 'Nan' /* 'Nan': rotating around a point at infinite a.k.a. Going Straight*/,
        steeringWheelPosition: 0};
      this._wheel = wheel;
      this.group = new THREE.Group();
      this.group.add(this.wheelGroup);
      this.carMesh = new THREE.Mesh( carGeo, new THREE.MeshPhysicalMaterial(/*{wireframe : true}*/) );//new THREE.BoxGeometry( 30, 4, 18).translate( 0, 3, 0 ), new THREE.MeshPhysicalMaterial());
      this.carMesh.matrixAutoUpdate = false;
      this.carMesh.setRotationFromMatrix( this.centerTransformation );
      this.group.add(this.carMesh);
    }

    rotateWheels( timestep ) {

    }

    // moveCar( timestep ) {
    //   for ( var i = 0; i < this.wheelGroup.children.length; i++ ) {
    //     this.wheelGroup.children[i].position.x += this.speed.x * timestep;
    //     this.wheelGroup.children[i].position.y += this.speed.y * timestep;
    //     this.wheelGroup.children[i].position.z += this.speed.z * timestep;
    //   }
    //   this.carMesh.position.x += this.speed.x * timestep;
    //   this.carMesh.position.y += this.speed.y * timestep;
    //   this.carMesh.position.z += this.speed.z * timestep;
    //   this.center.add(this.speed.clone().multiplyScalar(timestep));
    // }
    updateWheelTransformation( timestep, steerSpeed ) {
      this.ackermanSteering.steeringWheelPosition += steerSpeed * timestep;
      if (Math.abs(this.ackermanSteering.steeringWheelPosition) > this.ackermanSteering.maxWheelSteer ) this.ackermanSteering.steeringWheelPosition = Math.sign(this.ackermanSteering.steeringWheelPosition) * this.ackermanSteering.maxWheelSteer;
      // th_out = acot(cot(th_in) - d / l)
      let th_out = Math.PI / 2 - Math.atan( 1 / Math.tan( Math.abs(this.ackermanSteering.steeringWheelPosition) ) + this.frontWheelsAxlesWidth / this.length );
      for ( var i = 0; i < this.wheelMatrices.length; i++ ) {
        if ( i == 2 || i == 3 )
          this.wheelMatrices[i][1].copy(this.wheelMatrices[i][0].clone().multiply((new THREE.Matrix4()).makeRotationY( (i - 2.5/* Index 2 and3*/) * this.ackermanSteering.steeringWheelPosition < 0 ? this.ackermanSteering.steeringWheelPosition : Math.sign(this.ackermanSteering.steeringWheelPosition) * th_out )).multiply((new THREE.Matrix4()).makeRotationZ( -this.speed / ( this._wheel.R *  Math.PI / 2) * timestep * ( i % 2 == 0 ? 1 : -1 ))));
        else
          this.wheelMatrices[i][1].copy(this.wheelMatrices[i][0].clone().multiply((new THREE.Matrix4()).makeRotationZ( -this.speed / ( this._wheel.R *  Math.PI / 2) * timestep * ( i % 2 == 0 ? 1 : -1 ))));
      }
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
      // console.log(this.transmission.clutchFrictionCoeff);
      this.engine._rot += (targetRot - this.engine._rot) * (0.98, timestep * this.transmission.clutchFrictionCoeff * this.engine._load_inertia / ( this.engine._load_inertia + this.engine._shaft_inertia ) );
      // console.log((0.98, timestep * this.transmission.clutchFrictionCoeff * this.engine._load_inertia / ( this.engine._load_inertia + this.engine._shaft_inertia )));
      let posterior_transmission_rot = transmission_rot + (targetRot - transmission_rot) * ( 0.98, timestep * this.transmission.clutchFrictionCoeff * this.engine._shaft_inertia / ( this.engine._load_inertia + this.engine._shaft_inertia ) );
      this._differential_rot = this.transmission.gear !== false ? posterior_transmission_rot * this.transmission.gearbox[this.transmission.gear] : this.speed / this._wheel.R;
      let tempSpeed = this.speed;
      this.speed = this._differential_rot * this._wheel.R;
      this.acceleration = ( this.speed - tempSpeed ) / ( timestep / 1000 );
    }

    applyTransformation( timestep ) {
      var transformation = new THREE.Matrix4();
      if ( isFinite( this.ackermanSteering.ackermanPoint )) {
        let theta = timestep * this.speed / ( 2 * Math.PI * this.ackermanSteering.ackermanPoint );
        transformation.makeRotationAxis( this.upVector, theta );
        let XO = (this.upVector.clone()).cross( this.frontVector.clone() ).multiplyScalar( this.ackermanSteering.ackermanPoint ).add( this.frontVector.clone().multiplyScalar(this.min) );
        let OX_dot = ((XO.clone()).negate()).applyAxisAngle( this.upVector, theta );
        let shift = XO.add( OX_dot )/*.add( (this.frontVector.clone()).multiplyScalar(this.min) )*/;
        transformation.setPosition( shift.clone() );
        this.centerTransformation.multiply( transformation );
        for ( var i = 0; i < this.wheelGroup.children.length; i++ )
          this.wheelGroup.children[i].matrix.copy( (this.centerTransformation.clone()).multiply(this.wheelMatrices[i][1]) );
        let quaternion = new THREE.Quaternion();
        this.centerTransformation.decompose ( [], quaternion, [] );
        this.frontVector.copy((new THREE.Vector3( 1, 0, 0 )).applyQuaternion(quaternion));
      } else {
        transformation.setPosition( this.frontVector.clone().multiplyScalar( this.speed * timestep ) );
        this.centerTransformation.multiply( transformation );
        for ( var i = 0; i < this.wheelGroup.children.length; i++ )
          this.wheelGroup.children[i].matrix.copy( (this.centerTransformation.clone()).multiply(this.wheelMatrices[i][1]) );
      }

      this.carMesh.matrix.copy(this.centerTransformation);
      console.log(this.frontVector.length());
      this.center.set( this.centerTransformation.elements[12], this.centerTransformation.elements[13], this.centerTransformation.elements[14] );
      this.camera.position.add( new THREE.Vector3( transformation.elements[12], transformation.elements[13], transformation.elements[14] ) );
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