<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>Ajax JSON Input File Demo</title>
		<meta name="viewport" content="width=1920, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
		<style>

		</style>
	</head>
	<body>

	<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
	<script src="http://code.jquery.com/jquery-migrate-1.1.0.js"></script>

	<script src="js/three/csg.js"></script>
	<script src="js/three/ThreeCSG.js"></script>
	<script src="js/Utils.js"></script>

	<script src="js/three/three.min.js"></script>
	<script src="js/three/controls/OrbitControls.js"></script>
	<script src="js/three/controls/FlyControls.js"></script>
	<script src="js/three/controls/DeviceOrientationControls.js"></script>
	<script src="js/three/controls/MapControls.js"></script>
	<script src="js/three/controls/FirstPersonControls.js"></script>
	<script src="js/three/controls/TransformControls.js"></script>
	<script src="engine/Wheel.js"></script>
	<script src="engine/Car.js"></script>
	<script src="Models/Tire.js"></script>
	<script src="Models/Rim.js"></script>

	<script>

	var group, camera, componentsInfo, componentsUUID, scene, renderer, INTERSECTED, childs, car, acceleration, steerAcceleration;
	var steerSpeed = 0;
	var speed = new THREE.Vector3();
	var up = false,
    right = false,
    down = false,
    left = false;

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
			car = new Car( wheel, [new THREE.Vector2( - 10, 9 ), new THREE.Vector2( 10, 9)]);
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
		  if (e.keyCode === 38 /* up */ || e.keyCode === 87 /* w */ || e.keyCode === 90 /* z */){
		    up = true
		  }
		  if (e.keyCode === 39 /* right */ || e.keyCode === 68 /* d */){
		    right = true
		  }
		  if (e.keyCode === 40 /* down */ || e.keyCode === 83 /* s */){
		    down = true
		  }
		  if (e.keyCode === 37 /* left */ || e.keyCode === 65 /* a */ || e.keyCode === 81 /* q */){
		    left = true
		  }
		}
		document.addEventListener('keyup',release)
		function release(e){
		  if (e.keyCode === 38 /* up */ || e.keyCode === 87 /* w */ || e.keyCode === 90 /* z */){
		    up = false
		  }
		  if (e.keyCode === 39 /* right */ || e.keyCode === 68 /* d */){
		    right = false
		  }
		  if (e.keyCode === 40 /* down */ || e.keyCode === 83 /* s */){
		    down = false
		  }
		  if (e.keyCode === 37 /* left */ || e.keyCode === 65 /* a */ || e.keyCode === 81 /* q */){
		    left = false
		  }
		}

		function animate(){

			requestAnimationFrame( animate );
			render();
		}

		function render() {
			acceleration = (up ? 1 : 0) - (down ? 1 : 0) - (!(up || down) ? 0.5 * Math.sign(speed.x) : 0);
			steerSpeed = (left ? 0.8 : 0) - (right ? 0.8 : 0) - (!(left || right) ? 0.8 * Math.sign(car.steeringWheelPosition) : 0);
			console.log( acceleration, speed, car.steeringWheelPosition );
			// steerSpeed += steerAcceleration;
			speed.x += acceleration;
			car.rotateWheels( 0.05, speed.x / ( car.wheelR *  Math.PI / 2) );
			car.moveCar( 0.05, speed );
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
