<template>
    <div id="container" @click="onSceneClick"></div>
</template>

<script>
import * as THREE from 'three'
export default {
    props: [
        'json'
    ],
    data() {
        return {
            timestamp: 0,
            timestamp2: 0,
            timestep: 0,
            scene: null,
            camera: null,
            controls: null,
        }
    },
    mounted() {
        // console.log(this.json);
        this.timestamp = performance.now();
        this.init();
        this.cube.position.y = 5;
        console.log(this.controls);
        // console.log(this.camera);
        this.animate();
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
            const geometry = new THREE.CylinderGeometry(10, 10, 2, 32, 32);
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
            this.mouse = new THREE.Vector2();
            this.raycaster = new THREE.Raycaster();
        },
        animate: function() {
            requestAnimationFrame(this.animate);
            this.timestamp2 = performance.now();
            this.timestep = this.timestamp2 - this.timestamp;
            this.timestamp = this.timestamp2;
        },
        onSceneClick() {
        },
    }
}
</script>

<style>

</style>
