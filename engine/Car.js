class Car{
    constructor( wheel, wheelsPositions, carGeo ) {
      this.wheelGroup = new THREE.Group();
      if ( wheel instanceof Wheel ) {
        for ( var i = 0; i < wheelsPositions.length; i++ ) {
          wheel.group.position.x = wheelsPositions[i].x;
          wheel.group.position.z = wheelsPositions[i].y;
          this.wheelGroup.add(wheel.group.clone())
          wheel.group.rotation.y = Math.PI;
          wheel.group.position.z *= -1;
          this.wheelGroup.add(wheel.group.clone())
          wheel.group.rotation.y = 0;
        }
      }
      this.group = new THREE.Group();
      this.group.add(this.wheelGroup);
    }
}
