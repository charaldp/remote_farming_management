class Tire {
  constructor( tireType, tireDims, meshMaterial ) {

    switch( tireType ) {
      case 'Flat':
        this.points = [new THREE.Vector2( tireDims.DI / 2, 0 ), new THREE.Vector2( tireDims.DO / 2, 0 ), new THREE.Vector2( tireDims.DO / 2, tireDims.t ), new THREE.Vector2( tireDims.DI / 2, tireDims.t )]
        break;
      case 'Round':
        this.points = [new THREE.Vector2( tireDims.DI / 2, tireDims.intrWidth )]
        for ( var i = 0; i < 16; i++ )
          this.points.push();

    }
    this.tireGep = new THREE.LatheGeometry( this.points, 64 ).rotateX( Math.PI / 2 );
    this.meshOut = new THREE.Mesh( this.tireGep, meshMaterial.tire.clone() );
  }

}
