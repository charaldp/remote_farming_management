class Car{
    constructor( wheel, wheelsPositions, carGeo ) {
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
      this.frontVector = new THREE.Vector3( 1, 0, 0 );
      this.center = new THREE.Vector3( 0, 2 * wheel.R, 0 );
      this.length = max - min;
      this.maxWheelSteer = 50 * Math.PI / 180;
      this.ackermanPoint = 'NaN';     // 'Nan' Going Straight
      this.steeringWheelPosition = 0; // Steering Curvature Radius
      this.wheelR = wheel.R;
      this.group = new THREE.Group();
      this.group.add(this.wheelGroup);
      this.carMesh = new THREE.Mesh(new THREE.BoxGeometry( 30, 4, 18).translate( 0, 3, 0 ), new THREE.MeshPhysicalMaterial());
      this.group.add(this.carMesh);
    }
    rotateWheels( timestep, speed ) {
      for ( var i = 0; i < this.wheelGroup.children.length; i++ ) {
        this.wheelGroup.children[i].rotation.z -= speed * timestep * Math.sign(this.wheelGroup.children[i].position.z);
      }
    }
    moveCar( timestep, speed ) {
      for ( var i = 0; i < this.wheelGroup.children.length; i++ ) {
        this.wheelGroup.children[i].position.x += speed.x * timestep;
        this.wheelGroup.children[i].position.y += speed.y * timestep;
        this.wheelGroup.children[i].position.z += speed.z * timestep;
      }
      this.carMesh.position.x += speed.x * timestep;
      this.carMesh.position.y += speed.y * timestep;
      this.carMesh.position.z += speed.z * timestep;
      this.center.add(speed.clone().multiplyScalar(timestep));
    }
    steerWheels( timestep, speed ) {
      car.steeringWheelPosition += speed * timestep;
      if (Math.abs(car.steeringWheelPosition) > this.maxWheelSteer ) car.steeringWheelPosition = Math.sign(car.steeringWheelPosition) * this.maxWheelSteer;
      this.wheelGroup.children[2].rotation.y = car.steeringWheelPosition;
      this.wheelGroup.children[3].rotation.y = - car.steeringWheelPosition;
    }
}
