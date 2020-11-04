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

	<script src="resources/js/prev/three/csg.js"></script>
	<script src="resources/js/prev/three/ThreeCSG.js"></script>
	<script src="resources/js/prev/Utils.js"></script>
	<script src="resources/js/prev/ammo.js"></script>

	<script src="resources/js/prev/jquery-1.9.1.js"></script>
	<script src="resources/js/prev/jquery-migrate-1.2.1.min.js"></script>
	<!-- <script src="resources/js/prev/processing.min.js"></script> -->
	<script src="resources/js/prev/three/three.min.js"></script>
	<script src="resources/js/prev/three/objects/Sky.js"></script>
	<script src="resources/js/prev/three/dat.gui.min.js"></script>
	<script src="resources/js/prev/three/controls/OrbitControls.js"></script>
	<script src="resources/js/prev/three/controls/FlyControls.js"></script>
	<script src="resources/js/prev/three/controls/DeviceOrientationControls.js"></script>
	<script src="resources/js/prev/three/controls/MapControls.js"></script>
	<script src="resources/js/prev/three/controls/FirstPersonControls.js"></script>
	<script src="resources/js/prev/three/controls/TransformControls.js"></script>
	<script src="resources/js/Components/Wheel.js"></script>
	<script src="resources/js/Components/Car.js"></script>
	<script src="resources/js/Components/Engine.js"></script>
	<script src="resources/js/Components/Phys.js"></script>
	<script src="resources/js/Models/Tire.js"></script>
	<script src="resources/js/Models/Rim.js"></script>
	<?php
			$file = @$_GET[json];
			// a json file is provided as an argument at the php file as: ?json=<JSONFilename> (no filetype required)
			// echo $file;
		?>
		<script>
		// var file = './json/CylindricalShell.json';
		var file = "public/data/" + "<?php echo $file; ?>" + ".json";
		console.log(file);
		$.ajax({
			cache: 		false,
			type: 		"POST",
			url: 			file,
			dataType: "json"
	}).done(function(data){
	var group, camera, HUD, overlay, sound, scene, renderer, childs, car;
	var steerSpeed = 0;
	var timer1 = 0;
	var timer2 = 0;
	var frameBuffer = [ 0, 0, 0, 0, 0, 0, 0, 0, 0, 0 ];
	var timestep = 0.01;
	var throttle = 1;
	var brake = 0;
	// var transmission = {clutch: 1, clutchFrictionCoeff: 0, gear: false, gearbox: [ -0.15, 0.163, 0.262, 0.38, 0.52, 0.68, 0.87 ] };
 	var up = false,
    right = false,
    down = false,
    left = false,
	clutch = false;

	var cameraOffset = new THREE.Vector3();
	var components = [];
	var sceneGeometry = new THREE.Geometry();
	var totalLength = 0; // Total length is normalized and is used to preview the full !scaled! scene
	var sceneCenter = new THREE.Vector3();
	var dimDiv = 1; // This variable divides all dimension data in order to provide a better visualization
	var utils = new Utils( dimDiv );

	var meshMaterial = {
		tire: new THREE.MeshPhongMaterial( { shininess: 50, color : 0x1b1b1b } ),
		rim: new THREE.MeshPhysicalMaterial( { color: 0xd7d7d7, roughness: 0.17, metalness: 0.47, reflectivity: 1, clearCoat: 0.64, clearCoatRoughness: 0.22 } ),
		building: new THREE.MeshLambertMaterial( { color: 0xcccccc, opacity: 0.95, transparent: true } ),
		ground: new THREE.MeshBasicMaterial( { color: 0x77aa22, side: THREE.FrontSide, opacity: 0.65, transparent: true } )
	};
	// meshMaterial.rim.wireframe = true;
	// meshMaterial.tire.wireframe = true;

		init();
		animate();

		function init() {
			scene = new THREE.Scene();
			group = new THREE.Group();
			var components = [];
			data.vehicles.forEach( vehicle => {
				// var wheelMaterials = {rim : new THREE.MeshPhongMaterial(vehicle.components.wheel[0].meshMaterial.rim), tire : new THREE.MeshPhysicalMaterial(vehicle.components.wheel[0].meshMaterial.tire) };
				// wheelMaterials.rim.color = parseInt(vehicle.components.wheel[0].meshMaterial.rim.colour);
				// wheelMaterials.tire.color = parseInt(vehicle.components.wheel[0].meshMaterial.tire.colour);

				var wheel = new Wheel( vehicle.components.wheel[0].DI, vehicle.components.wheel[0].DO, vehicle.components.wheel[0].t, vehicle.components.wheel[0].tireType, vehicle.components.wheel[0].tireDims, vehicle.components.wheel[0].rimType, vehicle.components.wheel[0].rimDims,
					vehicle.components.wheel[0].pressure, vehicle.components.wheel[0].frictionOptions, meshMaterial );
				var transmission = {clutch: 1, clutchFrictionCoeff: vehicle.components.clutch.clutchFrictionCoeff, gear: false, gearbox: vehicle.components.transmission.gearbox };
				console.log(vehicle.components.wheel[0].DI, vehicle.components.wheel[0].DO, vehicle.components.wheel[0].t, vehicle.components.wheel[0].tireType, vehicle.components.wheel[0].tireDims, vehicle.components.wheel[0].rimType, vehicle.components.wheel[0].rimDims,
					vehicle.components.wheel[0].pressure, vehicle.components.wheel[0].frictionOptions);
				var engine = new Engine( vehicle.components.engine.shaft_inertia, vehicle.components.engine.rev_limit, vehicle.components.engine.idle_rot, vehicle.components.engine.maximum_hp );//I = M / 2 * R ^ 2 [kg*m^2]
				if ( vehicle.type === "Car" ) {
					var car_geo = Car.makeCarGeo( /*2D front to rear points*/vehicle.geometry.pointArray, vehicle.geometry.wheelsCentersPositions, vehicle.geometry.radius, vehicle.geometry.width, vehicle.geometry.bevelThickness );
					car = new Car( wheel, vehicle.geometry.wheelsCentersPositions, engine, vehicle.mass, transmission, car_geo, vehicle.spawnPosition, camera, -1);
					components.push( car );
				}
			});

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
			car.camera.cameraOffset = cameraOffset;
			// car.camera.theta = spawnPosition.rotation * 2 * Math.PI;
			scene.add( camera );

			var listener = new THREE.AudioListener();
			camera.add( listener );

			sound = new THREE.Audio( listener );

			var audioLoader = new THREE.AudioLoader();
			audioLoader.load( 'public/sounds/engine.ogg', function( buffer ) {
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
			var plane = new THREE.Mesh( planeGeo, meshMaterial.ground );
			plane.rotation.x = - Math.PI / 2;
			plane.position.y = - 0.0001 * totalLength;
			scene.add(plane);
			var buildingsGeo = new THREE.Geometry();
			for ( i = 0; i < 2000 ; i++ ) {
				let height = Math.random() * 20 + 10;
				buildingsGeo.merge(new THREE.BoxGeometry(Math.random() * 20 + 10, height,Math.random() * 20 + 10 ).translate( (Math.random() * 1000 - 500) * totalLength, height / 2, (Math.random() * 1000 - 500) * totalLength ));
			}
			var buildingsMesh = new THREE.Mesh( buildingsGeo, meshMaterial.building );
			scene.add(buildingsMesh);

			// Add components
			for (var i = 0; i < components.length;i++)
				group.add(components[i].group);

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
			 timer2 = performance.now();
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
			timestep = timer2 - timer1;
			// timestep = clock.getDelta();
			timer1 = performance.now();
			// console.log(timer1);
			frameBuffer.push( timestep );
			overlay.innerHTML = 'Framerate : ' + String( 1000 / ( frameBuffer.reduce((a, b) => a + b, 0) / frameBuffer.length ).toFixed() ) + ' FPS';
			frameBuffer.shift();
			throttle += ( up ? ( throttle < 2 ? 0.05 * timestep : 0 ) : ( throttle > 1 ? - 0.1 * timestep * (throttle - 1) : 0 ) );
			brake += ( down ? ( brake < 1 ? 0.2 * timestep : 0 ) : ( brake > 0 ? - 0.4 * timestep * brake : 0 ) );
			steerSpeed = Math.min( 0.05 * car.maxSpeed / Math.abs(car.speed), 1) * ( (left ? 0.6 * timestep : 0) - (right ? 0.6 * timestep : 0) ) - (!(left || right) ?  timestep * car.ackermanSteering.steeringWheelPosition : 0);
			console.log(steerSpeed, car.maxSpeed);
			car.transmission.clutch += !clutch ? (car.transmission.clutch < 1 ? 0.05 * timestep : 0 ) : (car.transmission.clutch > 0 ? - 0.05 * timestep * car.transmission.clutch : 0 );
			if (car.transmission.clutch < 0) car.transmission.clutch = 0;
			HUD.innerHTML = 'Engine RPM : ' + String( (car.engine._rot * 60).toFixed() ) +
				' , Speed : ' + String( ( car.speed * 3.6 ).toFixed(1) ) +
				' , Throttle : ' + String( throttle.toFixed(1) ) +
				' , Gear : ' + String( car.transmission.gear === false ? 'N' : (car.transmission.gear !== 0 ? car.transmission.gear : 'R') ) +
				' , Accelaration : ' + String( car.acceleration.toFixed(1) ) +
				' , Brake : ' + String( brake.toFixed(1) ) +
				' , Steering : ' +	String( (car.ackermanSteering.steeringWheelPosition).toFixed(1) ) +
				' , Clutch : ' + String( car.transmission.clutch.toFixed(1) ) +
				' , Power : ' + String( car.engine._currentPower.toFixed() ) +
				' , Torque : ' + String( car.engine._currentTorque.toFixed() );
			// sound.setVolume(Math.min(Math.abs(speed.length()) / 20, 0.5));
			// console.log(car.speed.length());

			car.updateLoad();
			car.updateClutchConnection( throttle, brake, timestep / 5 );
			sound.setPlaybackRate( isNaN(car.engine._rot) ? 0 : car.engine._rot / car.engine._idle_rot * 0.9 );
			car.updateWheelTransformation( timestep / 5, steerSpeed );
			// car.moveCar( timestep / 1 );
			car.applyTransformation( timestep / 5 );
			scene.updateMatrixWorld();
			// console.log(car.centerTransformation);
			controls.target.copy(car.center);
			controls.update();
 			renderer.render( scene, camera );
			timer2 = performance.now();
		}

		window.requestAnimationFrame(render);
	}).fail(function(){

	}).always(function(){

	});
	</script>

	</body>
</html>
