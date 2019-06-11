<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>Ajax JSON Input File Demo</title>
		<meta name="viewport" content="width=1920, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
		<style>
			body { margin: 0; }
			canvas { width: 100%; height: 100% }
		</style>
	</head>
	<body>

	<script src="js/three/csg.js"></script>
	<script src="js/three/ThreeCSG.js"></script>
	<script src="js/Utils.js"></script>

	<script src="js/three/three.min.js"></script>
	<script src="js/three/objects/Sky.js"></script>
	<script src="js/three/dat.gui.min.js"></script>
	<script src="js/three/controls/OrbitControls.js"></script>
	<script src="js/three/controls/FlyControls.js"></script>
	<script src="js/three/controls/DeviceOrientationControls.js"></script>
	<script src="js/three/controls/MapControls.js"></script>
	<script src="js/three/controls/FirstPersonControls.js"></script>
	<script src="js/three/controls/TransformControls.js"></script>
	<script src="engine/Wheel.js"></script>
	<script src="engine/Car.js"></script>
	<script src="engine/Engine.js"></script>
	<script src="Models/Tire.js"></script>
	<script src="Models/Rim.js"></script>

	<script>

	var group, camera, componentsInfo, sound, componentsUUID, scene, renderer, INTERSECTED, childs, car, acceleration, steerAcceleration;
	var steerSpeed = 0;
	var throttle = 1;
	var speed = new THREE.Vector3();
	var up = false,
    right = false,
    down = false,
    left = false;
		gear = false;
	var clickInfo = {
	  x: 0,
	  y: 0,
		clickCounter: 0,
	  userHasClicked: false,
		userHasRightClicked: false,
		currentUUID: [ '', '' ]
	};
	var grav = 9.87;
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

		init();
		animate();

		function init() {
			scene = new THREE.Scene();
			group = new THREE.Group();
			var wheel = new Wheel( 5, 4.3, 1.5, 'Flat', { DO: 5, DI: 4.3, t: 1.5 }, 'Ribs', { DO: 4.3, DI: 4, t: 1.5, intrWidth:  0.22, numRibs: 12, tRib: 0.15,
				 dRib: 0.30, ribsPosition: 1.2, axleIntrWidth: -0.1, axleDI: 0.1 , axleDO: 0.8, tAxle: 0.2 }, 0.4, {}, meshMaterial);
			acceleration = 0;
			var engine = new Engine( 500, 5000 / 60, 1050 / 60, 300 );
			car = new Car( wheel, [new THREE.Vector2( - 10, 9 ), new THREE.Vector2( 10, 9)], engine);
			var components = [car];
			renderer = new THREE.WebGLRenderer( { antialias: true } );
			renderer.setPixelRatio( window.devicePixelRatio );
			renderer.setSize( window.innerWidth, window.innerHeight );
			document.body.appendChild( renderer.domElement );

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
			camera = new THREE.PerspectiveCamera( 40, window.innerWidth / window.innerHeight, 1, 50 * totalLength);
			camera.position.set( car.center.x - totalLength, car.center.y + totalLength, 0 );
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
			controls.maxDistance = 10 * totalLength;
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

			var effectController = {
				turbidity: 10,
				rayleigh: 2,
				mieCoefficient: 0.005,
				mieDirectionalG: 0.8,
				luminance: 1,
				inclination: 0.49, // elevation / inclination
				azimuth: 0.25, // Facing front,
				sun: ! true
			};
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
			var planeGeo = new THREE.PlaneGeometry( 100 * totalLength, 100 * totalLength );
			var material = new THREE.MeshBasicMaterial( { color: 0x77aa22, side: THREE.FrontSide, opacity: 0.65, transparent: true } );
			var plane = new THREE.Mesh( planeGeo, material );
			plane.rotation.x = - Math.PI / 2;
			plane.position.y = - 0.0001 * totalLength;
			scene.add(plane);

			// Add components
			for (var i = 0; i < components.length;i++) {
				// group.add(components[i].finalGroup);
				// componentsUUID.push([ components[i].finalGroup.children[0].uuid, components[i].finalGroup.children[1].uuid ]);
				group.add(components[i].group);
				// console.log(componentsUUID[i]);
			}
			console.log(controls);
			childs = [];
			console.log( group.children );
			for ( var i = 0; i < group.children.length; i++)
				for ( var j = 0; j < group.children[i].children.length; j++)
					if ( group.children[i].children[j].isMesh )
						childs.push( group.children[i].children[j] );
					else
						for ( var k = 0; k < group.children[i].children[j].children.length; k++)
							childs.push( group.children[i].children[j].children[k] );

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
		document.addEventListener('keydown',press)
		function press(e){
		  if (e.keyCode === 38 /* up */ ){
		    up = true
		  }
		  if (e.keyCode === 39 /* right */ ){
		    right = true
		  }
		  if (e.keyCode === 40 /* down */ ){
		    down = true
		  }
		  if (e.keyCode === 37 /* left */ ){
		    left = true
		  }

			switch ( e.keyCode ) {
				case 81: /* q */
					gear = 1;
					break;
				case 65: /* a */
					gear = 2;
					break;
				case 87: /* w */
					gear = 3;
					break;
				case 83: /* s */
					gear = 4;
					break;
				case 69: /* e */
					gear = 5;
					break;
				case 68: /* d */
					gear = 6;
					break;
				case 82: /* r */
					gear = 0;
					break;
				default:
					gear = false;
			}

		}
		document.addEventListener('keyup',release)
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
			switch ( e.keyCode ) {
				case 81: /* q */
					if (gear === 1)
						gear = false;
					break;
				case 65: /* a */
					if (gear === 2)
						gear = false;
					break;
				case 87: /* w */
					if (gear === 3)
						gear = false;
					break;
				case 83: /* s */
					if (gear === 4)
						gear = false;
					break;
				case 69: /* e */
					if (gear === 5)
						gear = false;
					break;
				case 68: /* d */
					if (gear === 6)
						gear = false;
					break;
				case 82: /* r */
					if (gear === 0)
						gear = false;
				}
		}

		function animate(){

			requestAnimationFrame( animate );
			render();
		}

		function render() {
			acceleration = (up ? 1 : 0) - (down ? 1 : 0) - (!(up || down) ? 0.5 * Math.sign(speed.x) : 0);
			throttle += (up ? ( throttle < 10? 1 : 0) :( throttle > 1 ? -1 : 0)) * 0.05 ;
			steerSpeed = (left ? 0.8 : 0) - (right ? 0.8 : 0) - (!(left || right) ? 4 * car.steeringWheelPosition : 0);
			console.log( acceleration, speed, car.steeringWheelPosition, gear, car.engine._rot * 60, throttle );
			// steerSpeed += steerAcceleration;
			speed.x += acceleration;
			// sound.setVolume(Math.min(Math.abs(speed.length()) / 20, 0.5));

			car.rotateWheels( 0.05, speed.x / ( car.wheelR *  Math.PI / 2) );
			car.moveCar( 0.05, speed );
			car.engine.updateEngineState(throttle, 0.05);
			sound.setPlaybackRate( car.engine._rot / car.engine._idle_rot );
			car.steerWheels( 0.05, steerSpeed );
			camera.position.x += speed.x * 0.05;
			camera.position.y += speed.y * 0.05;
			camera.position.z += speed.z * 0.05;
			// ( car.center.x - car.length * car.frontVector.x, car.center.y + car.length, car.center.z - car.length * car.frontVector.z );
			renderer.render( scene, camera );
		}
		window.addEventListener( 'mousemove', onMouseMove, false );
		window.addEventListener( 'click', onMouseClick, false );
		window.addEventListener( 'contextmenu', onRightClick, false );
		window.requestAnimationFrame(render);

	</script>

	</body>
</html>
