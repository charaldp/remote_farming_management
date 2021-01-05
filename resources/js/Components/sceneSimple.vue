<template>
    <div id="container" @click="onSceneClick"></div>
</template>
<script>
import * as THREE from 'three'
import * as CANNON from 'cannon'
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
            world: null,
            groundBody: null,
            sphereBody: null,
        }
    },
    methods: {
        init: function() {
            this.world = new CANNON.World();
            this.world.gravity.set(0, 0, -9.82); // m/s²
            var radius = 1; // m
            this.sphereBody = new CANNON.Body({
                mass: 5, // kg
                position: new CANNON.Vec3(0, 5, 0), // m
                shape: new CANNON.Sphere(radius)
            });
            this.world.addBody(this.sphereBody);
            this.groundBody = new CANNON.Body({
                mass: 0 // mass == 0 makes the body static
            });
            var groundShape = new CANNON.Plane();
            this.groundBody.addShape(groundShape);
            this.world.addBody(this.groundBody);


            this.scene = new THREE.Scene()
            this.camera = new THREE.PerspectiveCamera(
                75,
                window.innerWidth / window.innerHeight,
                0.1,
                1000
            )

            this.renderer = new THREE.WebGLRenderer()
            var scaler = 0.85;
            this.renderer.setSize(1.15*scaler*window.innerWidth, scaler*window.innerHeight)
            document.body.appendChild(this.renderer.domElement)

            // const geometry = new THREE.CylinderGeometry(0.5, 0.5, 0.3, 64, 10).rotateX(Math.PI / 2).translate(0, 0.5, 0);
            const geometry = new THREE.SphereGeometry(1, 32, 32)
            const planeGeometry = new THREE.PlaneGeometry(40, 40, 10, 10).rotateX(-Math.PI / 2);
            // const material = new THREE.MeshBasicMaterial({ color: 0xffff00 })
            const groundMaterial = new THREE.MeshBasicMaterial({ color: 0x44ff00, side: THREE.DoubleSide })
            const material = new THREE.MeshPhysicalMaterial({ color: 0xffff00, clearcoat: 0.1, clearcoatRoughness: 0.3, reflectivity: 0.9, metalness: 0.3 })
            // const material = new THREE.MeshPhongMaterial({ color: 0x00ff00, shininess: 100 })
            
            var x = 0;
            
            this.cube = new THREE.Mesh(geometry, material)
            this.ground = new THREE.Mesh(planeGeometry, groundMaterial)

            const axesHelper = new THREE.AxesHelper( 50 );
            this.scene.add( axesHelper );
            this.scene.add(this.cube)
            this.scene.add(this.ground)
            this.scene.add( new THREE.AmbientLight( 0x222222 ) );
            var light = new THREE.PointLight( 0xffffff, 1 );
            light.position.set(5, 5, -5)
            this.scene.add( light );

            this.camera.position.x = 25
            this.camera.position.z = 25
            this.camera.position.y = 25
            this.controls = new OrbitControls( this.camera, this.renderer.domElement )
            this.controls.target = new THREE.Vector3(0,0,0);
            const animate = function() {}
        },
        animate: function() {
            requestAnimationFrame(this.animate)
            this.timestamp2 = performance.now();
            this.timestep = this.timestamp2 - this.timestamp;
            this.timestamp = this.timestamp2;

            this.updatePhysicsStep();
            
            
            // this.updatePhysics();
            // this.controls.target = this.cube.position.clone();
            this.controls.update();
            this.renderer.render(this.scene, this.camera)
        },
        onSceneClick() {
            this.cubePhys.speed = 10;
            // console.log(this.cubePhys.speed);
        },
        updatePhysics() {
            if (this.cube.position.y <= 0) {
                this.cubePhys.groundCollisionForce = Math.pow(this.cube.position.y, 2) * 5000;
                this.cubePhys.speed *= 0.9;
                this.cubePhys.acceleration = this.cubePhys.gravityForce + this.cubePhys.groundCollisionForce;
            } else {
                this.cubePhys.acceleration = this.cubePhys.gravityForce;
            }
            this.cubePhys.speed += this.cubePhys.acceleration * 0.001 * this.timestep;
            this.cube.position.y += this.cubePhys.speed * 0.001 * this.timestep;
        },
        updatePhysicsStep() {
            // Canonjs
            this.world.step(1/60, 0.001 * this.timestep, 4);
            this.cube.position.x = this.sphereBody.position.x;
            this.cube.position.y = this.sphereBody.position.y;
            this.cube.position.z = this.sphereBody.position.z;
            this.cube.quaternion.x = this.sphereBody.quaternion.x;
            this.cube.quaternion.y = this.sphereBody.quaternion.y;
            this.cube.quaternion.z = this.sphereBody.quaternion.z;
            this.cube.quaternion.w = this.sphereBody.quaternion.w;
        },
    },
    mounted() {
        // console.log(this.json);
        this.timestamp = performance.now();
        this.init();
        this.cube.position.y = 5;
        console.log(this.controls);
        // console.log(this.camera);
        this.animate();
    }
}
</script>