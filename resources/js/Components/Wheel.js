import * as THREE from 'three';
// import 'three';
import Tire from '../Models/Tire.js';
import Rim from '../Models/Rim.js';
// import Tire from '../Models/Tire.js';
// import Rim from '../Models/Rim.js';
class Wheel {
  constructor ( DO, DI, t, tireType, tireDims, rimType, rimDims, pressure, frictionOptions, meshMaterial ) {
    // console.log(Tire);
    this.tire = new Tire.Tire( tireType, tireDims, meshMaterial );
    this.rim = new Rim.Rim( rimType, rimDims, meshMaterial );
    this.R = DO / 2;
    this.group = new THREE.Group();
    this.group.add(this.rim.meshOut);
    this.group.add(this.tire.meshOut);
    this.group.position.set( 0, DO / 2, 0 );
  }
}
export default {Wheel};
