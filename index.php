<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>iDEA Vehicle Simulation</title>
		<meta name="viewport" content="width=1920, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
		<style>
			body { margin: 0; }
			canvas { block };/*width: 100%; height: 100% };*/
		</style>
	</head>
	<body>

	<script src="js/three/csg.js"></script>
	<script src="js/three/ThreeCSG.js"></script>
	<script src="js/Utils.js"></script>

	<!-- <script src="js/processing.min.js"></script> -->
	<script src="js/three/three.min.js"></script>
	<script src="js/three/objects/Sky.js"></script>
	<script src="js/three/dat.gui.min.js"></script>
	<script src="js/three/controls/OrbitControls.js"></script>
	<script src="js/three/controls/FlyControls.js"></script>
	<script src="js/three/controls/DeviceOrientationControls.js"></script>
	<script src="js/three/controls/MapControls.js"></script>
	<script src="js/three/controls/FirstPersonControls.js"></script>
	<script src="js/three/controls/TransformControls.js"></script>
	<script src="vehicle/Wheel.js"></script>
	<script src="vehicle/Car.js"></script>
	<script src="vehicle/Engine.js"></script>
	<script src="vehicle/Phys.js"></script>
	<script src="Models/Tire.js"></script>
	<script src="Models/Rim.js"></script>

	<script>

	var group, camera, HUD, overlay, componentsInfo, sound, componentsUUID, scene, renderer, INTERSECTED, childs, car;
	var steerSpeed = 0;
	var timer1 = 0;
	var timer2 = 0;
	var frameBuffer = [ 0, 0, 0, 0, 0, 0, 0, 0, 0, 0 ];
	var timestep = 0.01;
	var throttle = 1;
	var brake = 0;
	var transmission = {clutch: 1, clutchFrictionCoeff: 0, gear: false, gearbox: [ -0.15, 0.163, 0.262, 0.38, 0.52, 0.68, 0.87 ] };
	// var transmission = {clutch: 0, gear: false, gearbox: [ -0.2, 0.48, 0.43, 0.37, 0.30, 0.22, 0.13 ] };
	var up = false,
    right = false,
    down = false,
    left = false,
		clutch = false;
	var clickInfo = {
	  x: 0,
	  y: 0,
		clickCounter: 0,
	  userHasClicked: false,
		userHasRightClicked: false,
		currentUUID: [ '', '' ]
	};
	var grav = 9.87;
	var cameraOffset = new THREE.Vector3();
	var raycaster = new THREE.Raycaster();
	var mouse = new THREE.Vector2();
	var components = [];
	componentsInfo = [];
	componentsUUID = [];
	var sceneGeometry = new THREE.Geometry();
	var totalLength = 0; // Total length is normalized and is used to preview the full !scaled! scene
	var sceneCenter = new THREE.Vector3();
	var componentIndices = [];
	var dimDiv = 1; // This variable divides all dimension data in order to provide a better visualization
	var utils = new Utils( dimDiv );
	var outsideMeshMaterial = new THREE.MeshLambertMaterial({
		color: 0xcccccc,
		opacity: 0.95,
		transparent: true
	});
	var insideMeshMaterial = new THREE.MeshLambertMaterial({
		color: 0x323232,
		opacity: 0.65,
		transparent: true
	});
	var intermidiateMeshMaterial = new THREE.MeshLambertMaterial({
		color: 0x3c9dd1,
		opacity: 0.35,
		transparent: true
	});

	var meshMaterial = {
		tire: new THREE.MeshPhongMaterial( { shininess: 50, color : 0x1b1b1b } ),
		rim: new THREE.MeshPhysicalMaterial( {color: 0xd7d7d7, roughness: 0.17, metalness: 0.47, reflectivity: 1, clearCoat: 0.64, clearCoatRoughness: 0.22 } ),
		inside: insideMeshMaterial.clone(),
		outside: outsideMeshMaterial.clone(),
		intermidiate: intermidiateMeshMaterial.clone()
	};
	// meshMaterial.rim.wireframe = true;
	// meshMaterial.tire.wireframe = true;

		init();
		animate();

		function init() {
			scene = new THREE.Scene();
			group = new THREE.Group();
			var wheel = new Wheel( 0.5, 0.43, 0.15, 'Flat', { DO: 0.5, DI:0.43, t: 0.15 }, 'Ribs', { DO: 0.43, DI: 0.4, t: 0.15, intrWidth:  0.022, numRibs: 12, tRib: 0.015,
				 dRib: 0.030, ribsPosition: 0.12, axleIntrWidth: -0.01, axleDI: 0.01 , axleDO: 0.08, tAxle: 0.02 }, 0.04, {}, meshMaterial);
			acceleration = 0;
			var engine = new Engine( 50, 7000 / 60, 1050 / 60, 390, transmission.clutch );//I = M / 2 * R ^ 2 [kg*m^2]
			var car_geo = Car.makeCarGeo( /*2D front to rear points*/[ [1.7, 0], [1.68, 0.05], [1.67, 0.19], [1.7, 0.25], [1.69, 0.32], [1.67, 0.34], [0.55, 0.47], [0.1, 0.65], [-0.7, 0.67],
				 [-1.3,  0.4], [-1.79, 0.41], [-1.8, 0.4], [-1.8, 0.09], [-1.85, 0.09], [-1.85, 0] ], [ -1, 1 ], 0.27, 1.9, 0.05 );
		  var spawnPosition = { position : new THREE.Vector3( 0, 0, 0 ), rotation : 0 };
			car = new Car( wheel, [new THREE.Vector2( - 1, 0.8 ), new THREE.Vector2( 1, 0.8 )], engine, 2000, transmission, car_geo, spawnPosition, camera, -1);
			var components = [car];
			var physics = new Phys( 9.81, 0.8, 1, [] );
			renderer = new THREE.WebGLRenderer( { antialias: true } );
			renderer.setPixelRatio( window.devicePixelRatio );
			renderer.setSize( window.innerWidth, window.innerHeight );
			document.body.appendChild( renderer.domElement );
			HUD = document.createElement('div');
			overlay = document.createElement('div');
			HUD.style.position = 'absolute';
			overlay.style.position = 'absolute';
			//HUD.style.zIndex = 1;    // if you still don't see the label, try uncommenting this
			HUD.style.width = 100;
			HUD.style.height = 100;
			overlay.style.width = 100;
			overlay.style.height = 100;
			// HUD.style.backgroundColor = "transparent";
			HUD.innerHTML = "";
			HUD.style.top = '97%';//200 + 'px';
			overlay.innerHTML = "";
			overlay.style.top = '0%';//200 + 'px';
			// HUD.style.left = 20 + 'px';
			document.body.appendChild(HUD);
			document.body.appendChild(overlay);
			var arr = [];
			for ( var i = 0; i < components.length; i++ ) {
				var geo = new THREE.Geometry();
				// Save root Group's transform in transformation array
				arr = [[components[i].group.rotation.x, components[i].group.rotation.y, components[i].group.rotation.z,
							components[i].group.position.x, components[i].group.position.y, components[i].group.position.z, components[i].group.rotation.order]];
				Utils.mergeGroupChildren( geo, components[i].group, arr );
				sceneGeometry.merge( geo.clone() );
			}
			sceneGeometry.computeBoundingSphere()
			totalLength = 1.65 * sceneGeometry.boundingSphere.radius;
			sceneCenter = sceneGeometry.boundingSphere.center.clone();
			console.log(sceneCenter);
			// camera
			cameraOffset.set( - 2 * totalLength, totalLength / 2, 0 );
			camera = new THREE.PerspectiveCamera( 40, window.innerWidth / window.innerHeight, 1, 1000 * totalLength);
			camera.position.copy( (car.center.clone()).add(cameraOffset.applyAxisAngle(new THREE.Vector3( 0, 1, 0 ), Math.atan(car.frontVector.z / car.frontVector.x))));
			// camera.matrixAutoUpdate = false;
			car.camera = camera;
			scene.add( camera );

			var listener = new THREE.AudioListener();
			camera.add( listener );

			sound = new THREE.Audio( listener );

			var audioLoader = new THREE.AudioLoader();
			audioLoader.load( 'sounds/engine.ogg', function( buffer ) {
				sound.setBuffer( buffer );
				sound.setLoop( true );
				sound.setVolume( 0.5 );
				sound.play();
			});
			// controls
			controls = new THREE.OrbitControls( camera, renderer.domElement );
			controls.minDistance = totalLength / 10;
			controls.maxDistance = 100 * totalLength;
			controls.target0 = car.center;
			controls.target = car.center;
			controls.maxPolarAngle = Math.PI * 2;
			controls.screenSpacePanning = true;
			controls.enableKeys = false;
			controls.update();
			// console.log(controls);

			scene.add( new THREE.AmbientLight( 0x222222 ) );

			var light = new THREE.PointLight( 0xffffff, 1 );
			camera.add( light );
			// Sky
			sky = new THREE.Sky();
			sky.scale.setScalar( 450000 );
			scene.add( sky );
			// Add Sun Helper
			sunSphere = new THREE.Mesh(
				new THREE.SphereBufferGeometry( 20000, 16, 8 ),
				new THREE.MeshBasicMaterial( { color: 0xffffff } )
			);
			sunSphere.position.y = - 700000;
			sunSphere.visible = true;
			scene.add( sunSphere );

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
			scene.add( new THREE.AxesHelper( 5000 ) );

			scene.add( group );

			// Preview Overall scene geometry
			// group.add(new THREE.Mesh(sceneGeometry.clone(), meshMaterial.outside.clone()));

			// Add ground plane
			var planeGeo = new THREE.PlaneGeometry( 1000 * totalLength, 1000 * totalLength );
			var material = new THREE.MeshBasicMaterial( { color: 0x77aa22, side: THREE.FrontSide, opacity: 0.65, transparent: true } );
			var plane = new THREE.Mesh( planeGeo, material );
			plane.rotation.x = - Math.PI / 2;
			plane.position.y = - 0.0001 * totalLength;
			scene.add(plane);
			var buildingsGeo = new THREE.Geometry();
			for ( i = 0; i < 2000 ; i++ ) {
				let height = Math.random() * 20 + 10;
				buildingsGeo.merge(new THREE.BoxGeometry(Math.random() * 20 + 10, height,Math.random() * 20 + 10 ).translate( (Math.random() * 1000 - 500) * totalLength, height / 2, (Math.random() * 1000 - 500) * totalLength ));
			}
			var buildingsMesh = new THREE.Mesh( buildingsGeo, meshMaterial.outside );
			scene.add(buildingsMesh);

			// Add components
			for (var i = 0; i < components.length;i++) {
				// group.add(components[i].finalGroup);
				// componentsUUID.push([ components[i].finalGroup.children[0].uuid, components[i].finalGroup.children[1].uuid ]);
				group.add(components[i].group);
				// console.log(componentsUUID[i]);
			}
			childs = [];
			console.log( group.children );
			for ( var i = 0; i < group.children.length; i++)
				for ( var j = 0; j < group.children[i].children.length; j++)
					if ( group.children[i].children[j].isMesh )
						childs.push( group.children[i].children[j] );
					else
						for ( var k = 0; k < group.children[i].children[j].children.length; k++)
							childs.push( group.children[i].children[j].children[k] );

			 scene.autoUpdate = false;
		}

		function onMouseMove( event ) {

			// calculate mouse position in normalized device coordinates
			// (-1 to +1) for both components
			// acceleration = 0;
			mouse.x = ( event.clientX / window.innerWidth ) * 2 - 1;
			mouse.y = - ( event.clientY / window.innerHeight ) * 2 + 1;

		}
		function onMouseClick ( event ) {
		  // The user has clicked; let's note this event
		  // and the click's coordinates so that we can
		  // react to it in the render loop
		  clickInfo.userHasClicked = true;
		  clickInfo.x = event.clientX;
		  clickInfo.y = event.clientY;
			acceleration += 10;
		}
		function onRightClick ( event ) {
			clickInfo.userHasRightClicked = true;
		  clickInfo.x = event.clientX;
		  clickInfo.y = event.clientY;
			acceleration -= 10;
		}
		document.addEventListener( 'keydown', press )
		function press(e){
		  if (e.keyCode === 38 /* up */ ){
		    up = true;
		  }
		  if (e.keyCode === 39 /* right */ ){
		    right = true;
		  }
		  if (e.keyCode === 40 /* down */ ){
		    down = true;
		  }
		  if (e.keyCode === 37 /* left */ ){
		    left = true;
		  }
			if (e.keyCode === 86 /* left */ ){
		    clutch = true;
		  }

			switch ( e.keyCode ) {
				case 81: /* q */
					car.transmission.gear = 1;
					break;
				case 65: /* a */
					car.transmission.gear = 2;
					break;
				case 87: /* w */
					car.transmission.gear = 3;
					break;
				case 83: /* s */
					car.transmission.gear = 4;
					break;
				case 69: /* e */
					car.transmission.gear = 5;
					break;
				case 68: /* d */
					car.transmission.gear = 6;
					break;
				case 82: /* r */
					car.transmission.gear = 0;
					break;
				// default:
				// 	car.transmission.gear = false; !!! Wrong! Key release will do the job
			}
			// console.log(e.keyCode);// space === 32

		}
		document.addEventListener( 'keyup', release )
		function release(e){
		  if (e.keyCode === 38 /* up */){
		    up = false
		  }
		  if (e.keyCode === 39 /* right */){
		    right = false
		  }
		  if (e.keyCode === 40 /* down */){
		    down = false
		  }
		  if (e.keyCode === 37 /* left */){
		    left = false
		  }
			if (e.keyCode === 86 /* left */){
		    clutch = false
		  }

			switch ( e.keyCode ) {
				case 81: /* q */
					if (car.transmission.gear === 1)
						car.transmission.gear = false;
					break;
				case 65: /* a */
					if (car.transmission.gear === 2)
						car.transmission.gear = false;
					break;
				case 87: /* w */
					if (car.transmission.gear === 3)
						car.transmission.gear = false;
					break;
				case 83: /* s */
					if (car.transmission.gear === 4)
						car.transmission.gear = false;
					break;
				case 69: /* e */
					if (car.transmission.gear === 5)
						car.transmission.gear = false;
					break;
				case 68: /* d */
					if (car.transmission.gear === 6)
						car.transmission.gear = false;
					break;
				case 82: /* r */
					if (car.transmission.gear === 0)
						car.transmission.gear = false;
				}
		}

		function animate(){

			requestAnimationFrame( animate );
			render();
		}

		function render() {
			// acceleration = (up ? 1 : 0) - (down ? 1 : 0) - (!(up || down) ? 0.5 * Math.sign(car.speed.x) : 0);

			timestep = timer2 - timer1;
			// console.log( car.engine._idle_rot);
			timer1 = performance.now();
			frameBuffer.push( 1000 / timestep );
			overlay.innerHTML = 'Framerate : ' + String( ( frameBuffer.reduce((a, b) => a + b, 0) / 10 ).toFixed() ) +  ' FPS';
			// console.log( frameBuffer.length );
			frameBuffer.shift();
			throttle += ( up ? ( throttle < 2 ? 0.05 * timestep : 0 ) : ( throttle > 1 ? - 0.1 * timestep * (throttle - 1) : 0 ) );
			brake += ( down ? ( brake < 1 ? 0.2 * timestep : 0 ) : ( brake > 0 ? - 0.4 * timestep * brake : 0 ) );
			steerSpeed = (left ? 0.6 * timestep : 0) - (right ? 0.6 * timestep : 0) - (!(left || right) ?  timestep * car.ackermanSteering.steeringWheelPosition : 0);
			car.transmission.clutch += !clutch ? (car.transmission.clutch < 1 ? 0.05 * timestep : 0 ) : (car.transmission.clutch > 0 ? - 0.05 * timestep * car.transmission.clutch : 0 );
			HUD.innerHTML = 'Engine RPM : ' + String( (car.engine._rot * 60).toFixed() ) +
				' , Speed : ' + String( ( car.speed * 3.6 ).toFixed(1) ) +
				' , Throttle : ' + String( throttle.toFixed(1) ) +
				' , Gear : ' + String( transmission.gear === false ? 'N' : (transmission.gear !== 0 ? transmission.gear : 'R') ) +
				' , Accelaration : ' + String( car.acceleration.toFixed(1) ) +
				' , Brake : ' + String( brake.toFixed(1) ) +
				' , Steering : ' +	String( (car.ackermanSteering.steeringWheelPosition).toFixed(1) ) +
				' , Clutch : ' + String( car.transmission.clutch.toFixed(1) ) +
				' , Power : ' + String( car.engine._currentPower.toFixed() ) +
				' , Torque : ' + String( car.engine._currentTorque.toFixed() );
			// sound.setVolume(Math.min(Math.abs(speed.length()) / 20, 0.5));
			// car.rotateWheels( timestep / 10 );
			// console.log(car.speed.length());
			// car.speed.x = car.engine._rot * (car.transmission.gear === false ? 0 : car.transmission.gearbox[car.transmission.gear] ) * car.transmission.clutch;
			car.engine.updateEngineState( throttle, timestep / 1 );
			car.updateLoad();
			car.updateClutchConnection( timestep / 10 );
			sound.setPlaybackRate( isNaN(car.engine._rot) ? 0 : car.engine._rot / car.engine._idle_rot * 0.9 );
			car.updateWheelTransformation( timestep / 10, steerSpeed );
			// car.moveCar( timestep / 1 );
			car.applyTransformation( timestep / 10 );
			scene.updateMatrixWorld();
			// console.log(car.centerTransformation);
			controls.target.copy(car.center);
			controls.update();
			// camera.position.copy( (car.center.clone()).sub(cameraOffset.applyAxisAngle(new THREE.Vector3( 0, 1, 0 ), Math.atan(car.frontVector.z / car.frontVector.x))));
			// camera.position.add((car.frontVector.clone()).multiplyScalar(car.speed * timestep / 10));
 			renderer.render( scene, camera );
			timer2 = performance.now();
		}
		window.addEventListener( 'mousemove', onMouseMove, false );
		window.addEventListener( 'click', onMouseClick, false );
		window.addEventListener( 'contextmenu', onRightClick, false );
		window.requestAnimationFrame(render);

	</script>

	</body>
</html>
