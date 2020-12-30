<template>
  <div id="container" @click="onSceneClick"></div>
</template>
<script>
import * as THREE from 'three'
import { log } from 'three'
// import 'three/examples/js/controls/OrbitControls';
import {OrbitControls} from "three/examples/jsm/controls/OrbitControls";

export default {
  name: 'ThreeTest',
  props: [
    'json'
  ],
  data() {
    return {
      cube: null,
      renderer: null,
      scene: null,
      camera: null,
      controls: null,
      cubePhys: {
        speed: 0,
        gravityForce: -9.81,
        acceleration: 0,
      },
      timestamp: 0,
      timestamp2: 0,
      timestep: 0,
    }
  },
  methods: {
    init: function() {
      this.scene = new THREE.Scene()
      this.camera = new THREE.PerspectiveCamera(
        75,
        window.innerWidth / window.innerHeight,
        0.1,
        1000
      )

      this.renderer = new THREE.WebGLRenderer()
      var scaler = 0.8;
      this.renderer.setSize(scaler*window.innerWidth, scaler*window.innerHeight)
      document.body.appendChild(this.renderer.domElement)

      const geometry = new THREE.BoxGeometry(1, 1, 1).translate(0, 0.5, 0);
      const planeGeometry = new THREE.PlaneGeometry(10, 10, 10).rotateX(-Math.PI / 2);
      const material = new THREE.MeshBasicMaterial({ color: 0xffff00 })
      const groundMaterial = new THREE.MeshBasicMaterial({ color: 0x00ff00 })
      // const material = new THREE.MeshPhongMaterial({ color: 0x00ff00, shininess: 100 })
      var x = 0;
      
      this.cube = new THREE.Mesh(geometry, material)
      this.ground = new THREE.Mesh(planeGeometry, groundMaterial)
      const axesHelper = new THREE.AxesHelper( 5 );
      this.scene.add( axesHelper );
      this.scene.add(this.cube)
      this.scene.add(this.ground)

      this.camera.position.z = 5
      this.camera.position.y = 5
      this.controls = new OrbitControls( this.camera, this.renderer.domElement )
      this.controls.target = new THREE.Vector3(0,0,0);
      const animate = function() {}
    },
    animate: function() {
      requestAnimationFrame(this.animate)
      this.timestamp2 = performance.now();
      this.timestep = this.timestamp2 - this.timestamp;
      // console.log(this.timestep);
      this.timestamp = this.timestamp2;
      // this.cube.rotation.x += 0.01
      // this.cube.rotation.y += 0.01
      if (this.cube.position.y <= 0) {
        // console.log(this.cube.position.y);
        this.cubePhys.groundCollisionForce = Math.pow(this.cube.position.y, 2) * 5000;
        this.cubePhys.speed *= 0.9;
        this.cubePhys.acceleration = this.cubePhys.gravityForce + this.cubePhys.groundCollisionForce;
      } else {
        this.cubePhys.acceleration = this.cubePhys.gravityForce;
      }
      this.cubePhys.speed += this.cubePhys.acceleration * 0.001 * this.timestep;
      this.cube.position.y += this.cubePhys.speed * 0.001 * this.timestep;
      // this.controls.target = this.cube.position.clone();
      this.controls.update();
      this.renderer.render(this.scene, this.camera)
    },
    onSceneClick() {
      this.cubePhys.speed = 10;
      // console.log(this.cubePhys.speed);
    }
  },
  mounted() {
    // console.log(this.json);
    this.timestamp = performance.now();
    this.init();
    this.cube.position.y = 5;
    // console.log(this.camera);
    this.animate();
  }
}
</script>