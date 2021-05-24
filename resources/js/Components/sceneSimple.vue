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
            wheels: {representation: [null, null, null, null], phys: [null, null, null, null]},
            raycaster: null,
            mouse: null,
        }
    },
    created() {
        window.addEventListener('mousemove',this.updateMouse);
        window.addEventListener('mousedown',this.onSceneClick);
    },
    destroyed: function() {
        window.removeEventListener('mousemove', this.updateMouse);
        window.removeEventListener('mousedown',this.onSceneClick);
    },
    methods: {
        init: function() {
            this.world = new CANNON.World();
            this.world.gravity.set(0, -9.82, 0); // m/s²
            var radius = 1; // m
            // this.wheels[0] = new CANNON.Body({
            //     mass: 5, // kg
            //     position: new CANNON.Vec3(0, 5, 0), // m
            //     shape: new CANNON.Cylinder(radius, radius, 0.2*radius, 64)
            // });
            var positions = [
                new CANNON.Vec3(3, 1, -2),
                new CANNON.Vec3(-3, 1, -2),
                new CANNON.Vec3(-3, 1, 2),
                new CANNON.Vec3(3, 1, 2)
            ];
            for (let i = 0;i < positions.length ;i++) {
                this.wheels.phys[i] = new CANNON.Body({
                    mass: 5, // kg
                    position: positions[i], // m
                    shape: new CANNON.Cylinder(radius, radius, 0.2*radius, 64)
                });
                this.world.addBody(this.wheels.phys[i]);
            }
            this.groundBody = new CANNON.Body({
                mass: 0 // mass == 0 makes the body static
            });
            var groundShape = new CANNON.Plane();
            this.groundBody.addShape(groundShape);
            var axis = new CANNON.Vec3(1,0,0);
            this.groundBody.quaternion.setFromAxisAngle(axis, - Math.PI / 2);
            this.world.addBody(this.groundBody);


            this.scene = new THREE.Scene()
            this.camera = new THREE.PerspectiveCamera(
                75,
                window.innerWidth / window.innerHeight,
                0.1,
                1000
            )

            this.renderer = new THREE.WebGLRenderer()
            var scaler = 1;
            this.renderer.setSize(scaler*window.innerWidth, scaler*window.innerHeight)
            document.body.appendChild(this.renderer.domElement)

            // const geometry = new THREE.CylinderGeometry(0.5, 0.5, 0.3, 64, 10).rotateX(Math.PI / 2).translate(0, 0.5, 0);
            const geometry = new THREE.CylinderGeometry(1, 1, 0.2, 32, 32).rotateX(-Math.PI / 2);
            // const geometry = new THREE.SphereGeometry(1, 32, 32)
            const planeGeometry = new THREE.PlaneGeometry(40, 40, 10, 10).rotateX(-Math.PI / 2);
            // const material = new THREE.MeshBasicMaterial({ color: 0xffff00 })
            const groundMaterial = new THREE.MeshBasicMaterial({ color: 0x44ff00, side: THREE.DoubleSide })
            const material = new THREE.MeshPhysicalMaterial({ color: 0xffff00, clearcoat: 0.1, clearcoatRoughness: 0.3, reflectivity: 0.9, metalness: 0.3 })
            // const material = new THREE.MeshPhongMaterial({ color: 0x00ff00, shininess: 100 })

            var x = 0;

            for (let i = 0;i < positions.length ;i++) {
                this.wheels.representation[i] = new THREE.Mesh(geometry, material.clone());
                this.scene.add(this.wheels.representation[i])
            }
            this.ground = new THREE.Mesh(planeGeometry, groundMaterial)

            const axesHelper = new THREE.AxesHelper( 50 );
            this.scene.add( axesHelper );
            // this.scene.add(this.cube)
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
            this.mouse = new THREE.Vector2();
            this.raycaster = new THREE.Raycaster();
        },
        animate: function() {
            requestAnimationFrame(this.animate);
            this.timestamp2 = performance.now();
            this.timestep = this.timestamp2 - this.timestamp;
            this.timestamp = this.timestamp2;

            this.updatePhysicsStep();
            // this.updateMouse();
            this.raycaster.setFromCamera( this.mouse, this.camera );
            // this.updatePhysics();
            // this.controls.target = this.cube.position.clone();
            this.controls.update();
            this.renderer.render(this.scene, this.camera)
        },
        onSceneClick() {
            // this.cubePhys.speed = 10;
            // this.world.bodies[0].velocity.y += 5
            const intersects = this.raycaster.intersectObjects( this.scene.children );
            if (intersects.length > 0) {
                console.log(this.camera, intersects);
                for (let i = 0; i < 4; i++) {
                    if (intersects[ 0 ].object.uuid == this.wheels.representation[ i ].uuid) {
                        // this.camera.getWorldDirection();
                        var vector = new THREE.Vector3();
                        vector.subVectors (intersects[0].point, this.camera.position);
                        this.wheels.phys[i].applyImpulse(new CANNON.Vec3(vector.x, vector.y, vector.z), new CANNON.Vec3(intersects[0].point.x, intersects[0].point.y, intersects[0].point.z));
                        break;
                        // this.wheels.representation[ i ].material.color.set( 0xff0000 );
                    }
                    // this.world.bodies[0].applyForce( new CANNON.Vec3(0, 10, 0), );
                }
            }
            // for ( let i = 0; i < intersects.length; i ++ ) {

            // }
            // console.log(this.world.bodies[0], this.world.bodies[0].velocity.y);
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
            // console.log(this.cubePhys.speed);
            this.cube.position.y += this.cubePhys.speed * 0.001 * this.timestep;
        },
        updatePhysicsStep() {
            // Canonjs
            this.world.step(1/60, 0.001 * this.timestep, 4);
            for (let i = 0;i < 4 ;i++) {
                this.wheels.representation[i].position.x = this.wheels.phys[i].position.x;
                this.wheels.representation[i].position.y = this.wheels.phys[i].position.y;
                this.wheels.representation[i].position.z = this.wheels.phys[i].position.z;
                this.wheels.representation[i].quaternion.x = this.wheels.phys[i].quaternion.x;
                this.wheels.representation[i].quaternion.y = this.wheels.phys[i].quaternion.y;
                this.wheels.representation[i].quaternion.z = this.wheels.phys[i].quaternion.z;
                this.wheels.representation[i].quaternion.w = this.wheels.phys[i].quaternion.w;
            }
        },
        updateMouse( event ) {
            // calculate mouse position in normalized device coordinates
            // (-1 to +1) for both components
            this.mouse.x = ( event.clientX / window.innerWidth ) * 2 - 1;
            this.mouse.y = - ( event.clientY / window.innerHeight ) * 2 + 1;
            // console.log('mouse', event.clientX, event.clientY);
        }
    },
    mounted() {
        // console.log(this.json);
        this.timestamp = performance.now();
        this.init();
        // this.cube.position.y = 5;
        console.log(this.controls);
        // console.log(this.camera);
        this.animate();
    }
}
</script>
