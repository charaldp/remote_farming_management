<template>
	<div id="container"></div>
</template>
<script>
	import * as Three from 'three';
	// const THREE = Three;
    // import Sky from 'three';
    import '../prev/three/csg.js';
    import '../prev/three/ThreeCSG.js';
    import Utils from '../prev/Utils.js';
    // import ammo from '../prev/ammo.js';
    import '../prev/jquery-1.9.1.js';
    import '../prev/jquery-migrate-1.2.1.min.js';
    import '../prev/three/three.min.js';
	import '../prev/three/dat.gui.min.js';
	// import 'three/examples/js/controls/OrbitControls';
    // import '../prev/three/controls/OrbitControls.js';
    // import '../prev/three/controls/FlyControls.js';
    // import '../prev/three/controls/DeviceOrientationControls.js';
    // import '../prev/three/controls/MapControls.js';
    // import '../prev/three/controls/FirstPersonControls.js';
	// import '../prev/three/controls/TransformControls.js';
	import { Sky } from 'three/examples/jsm/objects/Sky.js';
    import Wheel from './Wheel.js';
    import Car from './Car.js';
    import Engine from './Engine.js';
    import Phys from './Phys.js';
    import Tire from '../Models/Tire.js';
    import Rim from '../Models/Rim.js';
	import { log } from 'three';
    export default {
        nane: 'scene-component',
        props: [
            'json'
        ],
        data: function() {
            return {
                group: '',
                camera: {},
                HUD: '',
                overlay: '',
                sound: '',
                scene: '',
                renderer: '',
                childs: '',
                car: {},
                steerSpeed: 0,
                timer1: 0,
                timer2: 0,
                frameBuffer: [ 0, 0, 0, 0, 0, 0, 0, 0, 0, 0 ],
                timestep: 0.01,
                throttle: 1,
                brake: 0,
                up: false,
                right: false,
                down: false,
                left: false,
                clutch: false,
                cameraOffset: {},
                components: [],
                sceneGeometry: {},
                totalLength: 0, // Total length is normalized and is used to preview the full !scaled! scene
                sceneCenter: new Three.Vector3(),
                dimDiv: 1, // This variable divides all dimension data in order to provide a better visualization
                utils: {},
                meshMaterial: {
                    // tire: new Three.MeshPhongMaterial( { shininess: 50, color : 0x1b1b1b } ),
                    // rim: new Three.MeshPhysicalMaterial( { color: 0xd7d7d7, roughness: 0.17, metalness: 0.47, reflectivity: 1, clearCoat: 0.64, clearCoatRoughness: 0.22 } ),
                    // building: new Three.MeshLambertMaterial( { color: 0xcccccc, opacity: 0.95, transparent: true } ),
                    // ground: new Three.MeshBasicMaterial( { color: 0x77aa22, side: Three.FrontSide, opacity: 0.65, transparent: true } )
                },
            }
        },
        created() {
		},
        mounted() {
			console.log(Three);
			this.utils = new Utils.Utils( this.dimDiv );
            console.log(this.json);
            this.scene = new Three.Scene();
			this.group = new Three.Group();
			this.sceneGeometry = new Three.Geometry();
			var components = [];
			this.json.vehicles.forEach( vehicle => {
				// var wheelMaterials = {rim : new Three.MeshPhongMaterial(vehicle.components.wheel[0].meshMaterial.rim), tire : new Three.MeshPhysicalMaterial(vehicle.components.wheel[0].meshMaterial.tire) };
				// wheelMaterials.rim.color = parseInt(vehicle.components.wheel[0].meshMaterial.rim.colour);
				// wheelMaterials.tire.color = parseInt(vehicle.components.wheel[0].meshMaterial.tire.colour);
				this.meshMaterial = {
                    tire: new Three.MeshPhongMaterial( { shininess: 50, color : 0x1b1b1b } ),
                    rim: new Three.MeshPhysicalMaterial( { color: 0xd7d7d7, roughness: 0.17, metalness: 0.47, reflectivity: 1, clearCoat: 0.64, clearCoatRoughness: 0.22 } ),
                    building: new Three.MeshLambertMaterial( { color: 0xcccccc, opacity: 0.95, transparent: true } ),
                    ground: new Three.MeshBasicMaterial( { color: 0x77aa22, side: Three.FrontSide, opacity: 0.65, transparent: true } )
                };
				var wheel = new Wheel.Wheel( vehicle.components.wheel[0].DI, vehicle.components.wheel[0].DO, vehicle.components.wheel[0].t, vehicle.components.wheel[0].tireType, vehicle.components.wheel[0].tireDims, vehicle.components.wheel[0].rimType, vehicle.components.wheel[0].rimDims,
					vehicle.components.wheel[0].pressure, vehicle.components.wheel[0].frictionOptions, this.meshMaterial );
				var transmission = {clutch: 1, clutchFrictionCoeff: vehicle.components.clutch.clutchFrictionCoeff, gear: false, gearbox: vehicle.components.transmission.gearbox };
				console.log(vehicle.components.wheel[0].DI, vehicle.components.wheel[0].DO, vehicle.components.wheel[0].t, vehicle.components.wheel[0].tireType, vehicle.components.wheel[0].tireDims, vehicle.components.wheel[0].rimType, vehicle.components.wheel[0].rimDims,
					vehicle.components.wheel[0].pressure, vehicle.components.wheel[0].frictionOptions);
				var engine = new Engine.Engine( vehicle.components.engine.shaft_inertia, vehicle.components.engine.rev_limit, vehicle.components.engine.idle_rot, vehicle.components.engine.maximum_hp );//I = M / 2 * R ^ 2 [kg*m^2]
				if ( vehicle.type === "Car" ) {
					var car_geo = Car.Car.makeCarGeo( /*2D front to rear points*/vehicle.geometry.pointArray, vehicle.geometry.wheelsCentersPositions, vehicle.geometry.radius, vehicle.geometry.width, vehicle.geometry.bevelThickness );
					this.car = new Car.Car( wheel, vehicle.geometry.wheelsCentersPositions, engine, vehicle.mass, transmission, car_geo, vehicle.spawnPosition, this.camera, -1);
					components.push( this.car );
				}
			});

			// var physics = new Phys( 9.81, 0.8, 1, [] );
			let container = document.getElementById('container');
			// let container = this.$refs.scene_container;
			// console.log(this.$refs);
			this.renderer = new Three.WebGLRenderer()
      		this.renderer.setSize(window.innerWidth, window.innerHeight)
      		document.body.appendChild(this.renderer.domElement)
			// var renderer = new Three.WebGLRenderer( { antialias: true } );
			// renderer.setPixelRatio( container.devicePixelRatio );
			// renderer.setSize( container.innerWidth, container.innerHeight );
			// document.body.appendChild( renderer.domElement );
			// this.HUD = document.createElement('div');
			// this.overlay = document.createElement('div');
			// this.HUD.style.position = 'absolute';
			// this.overlay.style.position = 'absolute';
			//this.HUD.style.zIndex = 1;    // if you still don't see the label, try uncommenting this
			// this.HUD.style.width = 100;
			// this.HUD.style.height = 100;
			// this.overlay.style.width = 100;
			// this.overlay.style.height = 100;
			// this.HUD.style.backgroundColor = "transparent";
			// this.HUD.innerHTML = "";
			// this.HUD.style.top = '97%';//200 + 'px';
			// this.overlay.innerHTML = "";
			// this.overlay.style.top = '0%';//200 + 'px';
			// this.HUD.style.left = 20 + 'px';
			// document.body.appendChild(this.HUD);
			// document.body.appendChild(this.overlay);
			var arr = [];
			for ( var i = 0; i < components.length; i++ ) {
				var geo = new Three.Geometry();
				// Save root Group's transform in transformation array
				arr = [[components[i].group.rotation.x, components[i].group.rotation.y, components[i].group.rotation.z,
							components[i].group.position.x, components[i].group.position.y, components[i].group.position.z, components[i].group.rotation.order]];
				Utils.Utils.mergeGroupChildren( geo, components[i].group, arr );
				this.sceneGeometry.merge( geo.clone() );
			}
			this.sceneGeometry.computeBoundingSphere();
			this.totalLength = 1.65 * this.sceneGeometry.boundingSphere.radius;
			this.sceneCenter = this.sceneGeometry.boundingSphere.center.clone();
			console.log(this.sceneCenter);
            // camera
            this.cameraOffset = new Three.Vector3()
			this.cameraOffset.set( - 2 * this.totalLength, this.totalLength / 2, 0 );
			this.camera = new Three.PerspectiveCamera( 40, container.innerWidth / container.innerHeight, 1, 1000 * this.totalLength);
			this.camera.position.copy( (this.car.center.clone()).add(this.cameraOffset.applyAxisAngle(new Three.Vector3( 0, 1, 0 ), Math.atan(this.car.frontVector.z / this.car.frontVector.x))));
			// camera.matrixAutoUpdate = false;
			this.car.camera = this.camera;
			this.car.camera.cameraOffset = this.cameraOffset;
			// this.car.camera.theta = spawnPosition.rotation * 2 * Math.PI;
			this.scene.add( this.camera );

			var listener = new Three.AudioListener();
			this.camera.add( listener );

			this.sound = new Three.Audio( listener );

			var audioLoader = new Three.AudioLoader();
			audioLoader.load( 'sounds/engine.ogg', function( buffer ) {
				this.sound.setBuffer( buffer );
				this.sound.setLoop( true );
				this.sound.setVolume( 0.5 );
				this.sound.play();
			}.bind(this));
			// controls
			// this.controls = new OrbitControls( this.camera, renderer.domElement );
			// this.controls.minDistance = this.totalLength / 10;
			// this.controls.maxDistance = 100 * this.totalLength;
			// this.controls.target0 = this.car.center;
			// this.controls.target = this.car.center;
			// this.controls.maxPolarAngle = Math.PI * 2;
			// this.controls.screenSpacePanning = true;
			// this.controls.enableKeys = false;
			// this.controls.update();
			// console.log(controls);

			this.scene.add( new Three.AmbientLight( 0x222222 ) );

			var light = new Three.PointLight( 0xffffff, 1 );
			this.camera.add( light );
			// Sky
			var sky = new Sky();
			sky.scale.setScalar( 450000 );
			this.scene.add( sky );
			// Add Sun Helper
			var sunSphere = new Three.Mesh(
				new Three.SphereBufferGeometry( 20000, 16, 8 ),
				new Three.MeshBasicMaterial( { color: 0xffffff } )
			);
			sunSphere.position.y = - 700000;
			sunSphere.visible = true;
			this.scene.add( sunSphere );

			// var effectController = {
			// 	turbidity: 10,
			// 	rayleigh: 2,
			// 	mieCoefficient: 0.005,
			// 	mieDirectionalG: 0.8,
			// 	luminance: 1,
			// 	inclination: 0.49, // elevation / inclination
			// 	azimuth: 0.25, // Facing front,
			// 	sun: true
			// };
			var distance = 400000;
			var effectController = {
				turbidity: 6.1,
				rayleigh: 1.466,
				mieCoefficient: 0.016,
				mieDirectionalG: 0.475,
				luminance: 1.1,
				inclination: 0.0847, // elevation / inclination
				azimuth: 0.2029, // Facing front,
				sun: true
			};
			var uniforms = sky.material.uniforms;
			uniforms[ "turbidity" ].value = effectController.turbidity;
			uniforms[ "rayleigh" ].value = effectController.rayleigh;
			uniforms[ "luminance" ].value = effectController.luminance;
			uniforms[ "mieCoefficient" ].value = effectController.mieCoefficient;
			uniforms[ "mieDirectionalG" ].value = effectController.mieDirectionalG;

			var theta = Math.PI * ( effectController.inclination - 0.5 );
			var phi = 2 * Math.PI * ( effectController.azimuth - 0.5 );

			sunSphere.position.x = distance * Math.cos( phi );
			sunSphere.position.y = distance * Math.sin( phi ) * Math.sin( theta );
			sunSphere.position.z = distance * Math.sin( phi ) * Math.cos( theta );
			sunSphere.visible = effectController.sun;
			uniforms[ "sunPosition" ].value.copy( sunSphere.position );

			// Axes Helper
			this.scene.add( new Three.AxesHelper( 5000 ) );

			this.scene.add( this.group );

			// Preview Overall scene geometry
			// group.add(new Three.Mesh(sceneGeometry.clone(), meshMaterial.outside.clone()));

			// Add ground plane
			var planeGeo = new Three.PlaneGeometry( 1000 * this.totalLength, 1000 * this.totalLength );
			var plane = new Three.Mesh( planeGeo, this.meshMaterial.ground );
			plane.rotation.x = - Math.PI / 2;
			plane.position.y = - 0.0001 * this.totalLength;
			this.scene.add(plane);
			var buildingsGeo = new Three.Geometry();
			for ( i = 0; i < 2000 ; i++ ) {
				let height = Math.random() * 20 + 10;
				buildingsGeo.merge(new Three.BoxGeometry(Math.random() * 20 + 10, height,Math.random() * 20 + 10 ).translate( (Math.random() * 1000 - 500) * this.totalLength, height / 2, (Math.random() * 1000 - 500) * this.totalLength ));
			}
			var buildingsMesh = new Three.Mesh( buildingsGeo, this.meshMaterial.building );
			this.scene.add(buildingsMesh);

			// Add components
			for (var i = 0; i < components.length;i++)
				this.group.add(components[i].group);

			this.childs = [];
			console.log( this.group.children );
			for ( var i = 0; i < this.group.children.length; i++)
				for ( var j = 0; j < this.group.children[i].children.length; j++)
					if ( this.group.children[i].children[j].isMesh )
						this.childs.push( this.group.children[i].children[j] );
					else
						for ( var k = 0; k < this.group.children[i].children[j].children.length; k++)
							this.childs.push( this.group.children[i].children[j].children[k] );

            this.scene.autoUpdate = false;
            this.timer2 = performance.now();
			// this.frameBuffer = [ 0, 0, 0, 0, 0, 0, 0, 0, 0, 0 ];
            this.animate();
        },
        methods: {
            animate() {
                requestAnimationFrame( this.animate );
                this.render();
            },
            render() {
                this.timestep = this.timer2 - this.timer1;
                // timestep = clock.getDelta();
                this.timer1 = performance.now();
                // console.log(timer1);
                // this.frameBuffer.push( this.timestep );
                // this.overlay.innerHTML = 'Framerate : ' + String( 1000 / ( this.frameBuffer.reduce((a, b) => a + b, 0) / this.frameBuffer.length ).toFixed() ) + ' FPS';
                // this.frameBuffer.shift();
                this.throttle += ( this.up ? ( this.throttle < 2 ? 0.05 * this.timestep : 0 ) : ( this.throttle > 1 ? - 0.1 * this.timestep * (this.throttle - 1) : 0 ) );
                this.brake += ( this.down ? ( this.brake < 1 ? 0.2 * this.timestep : 0 ) : ( this.brake > 0 ? - 0.4 * this.timestep * this.brake : 0 ) );
                this.steerSpeed = Math.min( 0.05 * this.car.maxSpeed / Math.abs(this.car.speed), 1) * ( (this.left ? 0.6 * this.timestep : 0) - (this.right ? 0.6 * this.timestep : 0) ) - (!(this.left || this.right) ?  this.timestep * this.car.ackermanSteering.steeringWheelPosition : 0);
                console.log(this.steerSpeed, this.car.maxSpeed);
                this.car.transmission.clutch += !this.clutch ? (this.car.transmission.clutch < 1 ? 0.05 * this.timestep : 0 ) : (this.car.transmission.clutch > 0 ? - 0.05 * this.timestep * this.car.transmission.clutch : 0 );
                // if (this.car.transmission.clutch < 0) this.car.transmission.clutch = 0;
                // HUD.innerHTML = 'Engine RPM : ' + String( (this.car.engine._rot * 60).toFixed() ) +
                //     ' , Speed : ' + String( ( this.car.speed * 3.6 ).toFixed(1) ) +
                //     ' , Throttle : ' + String( this.throttle.toFixed(1) ) +
                //     ' , Gear : ' + String( this.car.transmission.gear === false ? 'N' : (this.car.transmission.gear !== 0 ? this.car.transmission.gear : 'R') ) +
                //     ' , Accelaration : ' + String( this.car.acceleration.toFixed(1) ) +
                //     ' , Brake : ' + String( brake.toFixed(1) ) +
                //     ' , Steering : ' +	String( (this.car.ackermanSteering.steeringWheelPosition).toFixed(1) ) +
                //     ' , Clutch : ' + String( this.car.transmission.clutch.toFixed(1) ) +
                //     ' , Power : ' + String( this.car.engine._currentPower.toFixed() ) +
                //     ' , Torque : ' + String( this.car.engine._currentTorque.toFixed() );
                // sound.setVolume(Math.min(Math.abs(speed.length()) / 20, 0.5));
                // console.log(this.car.speed.length());

                this.car.updateLoad();
				this.car.updateClutchConnection( this.throttle, this.brake, this.timestep / 5 );
				console.log(this.sound);
                this.sound.setPlaybackRate( isNaN(this.car.engine._rot) ? 0 : this.car.engine._rot / this.car.engine._idle_rot * 0.9 );
                this.car.updateWheelTransformation( this.timestep / 5, this.steerSpeed );
                // this.car.moveCar( timestep / 1 );
                this.car.applyTransformation( this.timestep / 5 );
                this.scene.updateMatrixWorld();
                // console.log(this.car.centerTransformation);
                // this.controls.target.copy(this.car.center);
                // this.controls.update();
                this.renderer.render( this.scene, this.camera );
                this.timer2 = performance.now();
            }
        }
    }
</script>