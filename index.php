<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>Ajax JSON Input File Demo</title>
		<meta name="viewport" content="width=device-width, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
		<style>

		</style>
	</head>
	<body>

	<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
	<script src="http://code.jquery.com/jquery-migrate-1.1.0.js"></script>

	<script src="js/three/csg.js"></script>
	<script src="js/three/ThreeCSG.js"></script>
	<script src="js/VesselUtils.js"></script>

	<script src="js/three/three.min.js"></script>
	<script src="js/three/controls/OrbitControls.js"></script>
	<script src="js/three/controls/FlyControls.js"></script>
	<script src="js/three/controls/DeviceOrientationControls.js"></script>
	<script src="js/three/controls/MapControls.js"></script>
	<script src="js/three/controls/FirstPersonControls.js"></script>
	<script src="js/three/controls/TransformControls.js"></script>

	<script src="Models/CylindricalShell.js"></script>
	<script src="Models/HemisphericalEnd.js"></script>
	<script src="Models/TorisphericalEnd.js"></script>
	<script src="Models/WeldedFlatEnd.js"></script>
	<script src="Models/BoltedFlatEnd.js"></script>
	<script src="Models/BoltedDomedEnd.js"></script>
	<script src="Models/ConicalShell.js"></script>
	<script src="Models/ExpansionBellows.js"></script>
	<script src="Models/Flange.js"></script>
	<script src="Models/Nozzle.js"></script>
	<script src="Models/EllipsoidalEnd.js"></script>
	<script src="Models/SphericalShell.js"></script>
	<script src="Models/RectangularShell.js"></script>
	<script src="Models/StiffRing.js"></script>
	<script src="Models/SaddleRingSupport.js"></script>
	<script src="Models/LegSupport.js"></script>
	<script src="Models/BracketSupport.js"></script>
	<script src="Models/AttachmentLug.js"></script>
	<script src="Models/LiftingTrunnions.js"></script>
	<script src="Models/Platform.js"></script>
	<script src="Models/PipeBendsElbows.js"></script>
	<script src="Models/TubeBundle.js"></script>
	<script src="Models/Shape.js"></script>

	<script>

	$.ajax({
		cache: 		false,
		type: 		"POST",
		// url: 		"./json/CylindricalShell.json",
		// url:			"./json/ConicalShell.json",
		// url:			"./json/SphericalShell.json",
		// url:			"./json/RectangularShell.json",
		// url:			"./json/PipeBendsElbows.json",
		// url:			"./json/HemisphericalEnd.json",
		// url:			"./json/EllipsoidalEnd.json",
		// url:			"./json/WeldedFlatEnd.json",
		// url:			"./json/BoltedFlatEnd.json",
		// url:			"./json/BoltedDomedEnd.json",
		// url:			"./json/Nozzle.json",
		// url:			"./json/Flange.json",
		// url:			"./json/ExpansionBellows.json",
		// url:			"./json/StiffRing.json",
		// url:			"./json/BracketSupport.json",
		// url:			"./json/SaddleRingSupport.json",
		// url:			"./json/LegSupport.json",
		// url:			"./json/AttachmentLug.json",
		// url:			"./json/LiftingTrunnions.json",
		url:			"./json/Platform.json",
		// url:			"./json/vessel.json",
		// url:			"./json/data.json",
		// url:			" http://vesselapp.com.dedi4051.your-server.de/graphicsAjaxDemo.php",
		dataType: "json"
	}).done(function(data){
		var group, camera, componentsInfo, componentsUUID, scene, renderer, INTERSECTED, childs;
		var clickInfo = {
		  x: 0,
		  y: 0,
			clickCounter: 0,
		  userHasClicked: false,
			userHasRightClicked: false,
			currentUUID: [ '', '' ]
		};
		var raycaster = new THREE.Raycaster();
		var mouse = new THREE.Vector2();
		var components = [];
		componentsInfo = [];
		componentsUUID = [];
		var sceneGeometry = new THREE.Geometry();
		var totalLength = 0; // Total length is normalized and is used to preview the full !scaled! scene
		var sceneCenter = new THREE.Vector3();
		var componentIndices = [];
		var dimDiv = 100; // This variable divides all dimension data in order to provide a better visualization
		var utils = new VesselUtils( dimDiv );
		// var outsideMeshMaterial = new THREE.MeshPhongMaterial({
		var outsideMeshMaterial = new THREE.MeshLambertMaterial({
			color: 0xcccccc,
			opacity: 0.65,
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
			inside: insideMeshMaterial.clone(),
			outside: outsideMeshMaterial.clone(),
			intermidiate: intermidiateMeshMaterial.clone()
		};
		console.log("Number of components : ",data.components.length);
		for (var i = 0; i < data.components.length;i++) {
			componentIndices[(data.components[i].component_id)] = i;
			switch ( data.components[i].componentDesign_id ) {
				case "1": // Cylindrical Shell
					componentsInfo.push( { 'component_id': data.components[i].component_id, 'componentDesign_name': data.components[i].componentDesign_name, 'Ri' : data.components[i].Ri, 'length': parseFloat(data.components[i].length), 'nominal_thickness': data.components[i].nominal_thickness,
						'attachment_x': data.components[i].attachment_x, 'attachment_y': data.components[i].attachment_y, 'attachment_z': data.components[i].attachment_z, 'orientation': data.components[i].vessel_orientation, 'direction': data.components[i].direction } );
					components.push(new CylindricalShell(data.components[i].Ri, parseFloat(data.components[i].length), data.components[i].nominal_thickness, data.components[i].attachment_x, data.components[i].attachment_y, data.components[i].attachment_z, data.components[i].vessel_orientation, data.components[i].direction, meshMaterial, dimDiv));
					break;
				case "2": // Conical Shell
					componentsInfo.push( { 'component_id': data.components[i].component_id, 'componentDesign_name': data.components[i].componentDesign_name, 'Di_high' : data.components[i].Di_high, 'Di_low' : data.components[i].Di_low, 'length': data.components[i].length, 'nominal_thickness': data.components[i].nominal_thickness, 'rT1':  data.components[i].rT1, 'rT2':  data.components[i].rT2,
						'attachment_x': data.components[i].attachment_x, 'attachment_y': data.components[i].attachment_y, 'attachment_z': data.components[i].attachment_z, 'orientation': data.components[i].orientation, 'direction': data.components[i].direction } );
					components.push(new ConicalShell(data.components[i].Di_high / 2, data.components[i].Di_low / 2, data.components[i].length, data.components[i].nominal_thickness, data.components[i].rT1, data.components[i].rT2,
						 data.components[i].offset, data.components[i].offsetRotationAngle, data.components[i].attachment_x, data.components[i].attachment_y, data.components[i].attachment_z, data.components[i].orientation, data.components[i].direction, meshMaterial, dimDiv));
					break;
				case "3": // Spherical shell
					componentsInfo.push( { 'component_id': data.components[i].component_id, 'componentDesign_name': data.components[i].componentDesign_name, 'Ro' : data.components[i].Ro, 'attachment_x': data.components[i].attachment_x, 'attachment_y': data.components[i].attachment_y, 'attachment_z': data.components[i].attachment_z } );
					components.push(new SphericalShell(data.components[i].Ro, data.components[i].attachment_x, data.components[i].attachment_y, data.components[i].attachment_z, meshMaterial, dimDiv));
					break;
				case "4": // Rectangular Shell
					componentsInfo.push( { 'component_id': data.components[i].component_id, 'componentDesign_name': data.components[i].componentDesign_name, 'width' : data.components[i].width, 'length': data.components[i].length, 'height' : data.components[i].height, 'width_thickness': data.components[i].width_thickness,
					  'length_thickness': data.components[i].length_thickness, 'height_thickness': data.components[i].height_thickness, 'r':  data.components[i].r, 'attachment_x': data.components[i].attachment_x, 'attachment_y': data.components[i].attachment_y, 'attachment_z': data.components[i].attachment_z,
						'openSidesOrientation': data.components[i].openSidesOrientation, 'direction': data.components[i].direction, 'heads': data.components[i].heads } );
					components.push(new RectangularShell(data.components[i].width, data.components[i].length, data.components[i].height, data.components[i].width_thickness, data.components[i].length_thickness, data.components[i].height_thickness, data.components[i].r, data.components[i].attachment_x, data.components[i].attachment_y, data.components[i].attachment_z,
						data.components[i].openSidesOrientation, data.components[i].direction, data.components[i].heads, meshMaterial, dimDiv));
					break;
				case "5": // Pipe Bends/Elbows
						componentsInfo.push( { 'component_id': data.components[i].component_id, 'componentDesign_name': data.components[i].componentDesign_name } );
					components.push(new PipeBendsElbows(data.components[i].pipeRadius, data.components[i].pipeWidth, data.components[i].bendRadius, data.components[i].bendAngle, data.components[i].placementAngle, data.components[i].attachment_x, data.components[i].attachment_y, data.components[i].attachment_z,
						data.components[i].orientation, data.components[i].direction, meshMaterial, dimDiv));
					break;
				case "6": // Hemispherical End
					componentsInfo.push( { 'component_id': data.components[i].component_id, 'componentDesign_name': data.components[i].componentDesign_name } );
					components.push(new HemisphericalEnd(data.components[i].Di / 2, data.components[i].headThickness, data.components[i].attachment_x, data.components[i].attachment_y, data.components[i].attachment_z,
						data.components[i].vessel_orientation, data.components[i].direction, meshMaterial, dimDiv));
					break;
				case "7": // Ellipsoidal End
					componentsInfo.push( { 'component_id': data.components[i].component_id, 'componentDesign_name': data.components[i].componentDesign_name } );
					components.push(new EllipsoidalEnd(data.components[i].Di, data.components[i].crown_radius, data.components[i].r, data.components[i].length, data.components[i].nominal_thickness, data.components[i].attachment_x, data.components[i].attachment_y, data.components[i].attachment_z,
						 data.components[i].vessel_orientation, data.components[i].direction, meshMaterial, dimDiv));
					break;
				case "8": // Torispherical End

					break;
				case "9": // Welded Flat End
					componentsInfo.push( { 'component_id': data.components[i].component_id, 'componentDesign_name': data.components[i].componentDesign_name } );
					components.push(new WeldedFlatEnd(data.components[i].type, data.components[i].torusInRadius, data.components[i].torusOutRadius, data.components[i].D, data.components[i].width, data.components[i].h1, data.components[i].weldWidth, data.components[i].attachment_x, data.components[i].attachment_y, data.components[i].attachment_z,
						data.components[i].orientation, data.components[i].direction, meshMaterial, dimDiv));
					break;
				case "10": // Bolted Flat End
					componentsInfo.push( { 'component_id': data.components[i].component_id, 'componentDesign_name': data.components[i].componentDesign_name } );
					components.push(new BoltedFlatEnd( data.components[i].DO, data.components[i].DI, data.components[i].t, data.components[i].K, data.components[i].L, data.components[i].faceType, data.components[i].face, data.components[i].faceTypeDims, data.components[i].numBolts,
						data.components[i].attachment_x, data.components[i].attachment_y, data.components[i].attachment_z, data.components[i].orientation, data.components[i].direction, meshMaterial, dimDiv));
					break;
				case "11": // Bolted Domed End
					componentsInfo.push( { 'component_id': data.components[i].component_id, 'componentDesign_name': data.components[i].componentDesign_name } );
					components.push(new BoltedDomedEnd(data.components[i].D, data.components[i].OD, data.components[i].crown_radius, data.components[i].r, data.components[i].nominal_thickness, data.components[i].h1, data.components[i].tOffset, data.components[i].t,
						data.components[i].K, data.components[i].L, data.components[i].numBolts, data.components[i].attachment_x, data.components[i].attachment_y, data.components[i].attachment_z, data.components[i].orientation, data.components[i].direction, meshMaterial, dimDiv));
					break;
				case "12": // Nozzle / Opening
					componentsInfo.push( { 'component_id': data.components[i].component_id, 'componentDesign_name': data.components[i].componentDesign_name } );
					components.push(new Nozzle(data.components[i].nozzleSettings, components[componentIndices[data.components[i].attachToShell]], data.components[i].positionOffset, data.components[i].flangeArgs, data.components[i].placementRotation,
						data.components[i].attachment_x, data.components[i].attachment_y, data.components[i].attachment_z, data.components[i].orientation, data.components[i].direction, meshMaterial, dimDiv));
					break;
				case "13": // Flange
					componentsInfo.push( { 'component_id': data.components[i].component_id, 'componentDesign_name': data.components[i].componentDesign_name } );
					components.push(new Flange(data.components[i].flangeType, data.components[i].flangeTypeDims, data.components[i].B, data.components[i].D, data.components[i].t, data.components[i].K, data.components[i].L, data.components[i].numBolts, data.components[i].isBlind,
						data.components[i].faceType, data.components[i].face, data.components[i].faceTypeDims, data.components[i].attachment_x, data.components[i].attachment_y, data.components[i].attachment_z, data.components[i].orientation, data.components[i].direction, meshMaterial, dimDiv));
					break;
				case "14": // Tubesheet

					break;
				case "15": // Expansion Bellows
					componentsInfo.push( { 'component_id': data.components[i].component_id, 'componentDesign_name': data.components[i].componentDesign_name } );
					components.push(new ExpansionBellows(data.components[i].convolutionType, data.components[i].mean_diameter, data.components[i].h, data.components[i].nominal_thickness, data.components[i].r, data.components[i].numConvolutions, data.components[i].convolutionLength, data.components[i].extraHubLength, data.components[i].hasFlange,
						data.components[i].flangeDims, data.components[i].attachment_x, data.components[i].attachment_y, data.components[i].attachment_z, data.components[i].orientation, data.components[i].direction, meshMaterial, dimDiv))
					break;
				case "16": // Tube Bundle

					break;
				case "17": // Stiff Ring
					componentsInfo.push( { 'component_id': data.components[i].component_id, 'componentDesign_name': data.components[i].componentDesign_name } );
					components.push(new StiffRing(data.components[i].ringType, data.components[i].ringDims,data.components[i].vessel_length, data.components[i].distribution_length, data.components[i].placement_diameter, data.components[i].numRings, data.components[i].isInside,
						data.components[i].attachment_x, data.components[i].attachment_y, data.components[i].attachment_z, data.components[i].vessel_orientation, data.components[i].direction, meshMaterial, dimDiv));
					break;
				case "18": // Saddle / Ring Support
					componentsInfo.push( { 'component_id': data.components[i].component_id, 'componentDesign_name': data.components[i].componentDesign_name } );
					components.push(new SaddleRingSupport(data.components[i].form, data.components[i].basePlateDims,  data.components[i].padDims,  data.components[i].webDims, data.components[i].ribOptions, data.components[i].ribDims, data.components[i].stiffRingType, data.components[i].ringArgs,
						data.components[i].vessel_length, data.components[i].offsetPosition, data.components[i].outer_diameter, data.components[i].wrapAngle, data.components[i].isFlipped, data.components[i].stackedType,	data.components[i].attachment_x, data.components[i].attachment_y, data.components[i].attachment_z, data.components[i].orientation, data.components[i].direction, meshMaterial, dimDiv));
					break;
				case "19": // Skirt Support

					break;
				case "20": // Bracket Support
					componentsInfo.push( { 'component_id': data.components[i].component_id, 'componentDesign_name': data.components[i].componentDesign_name } );
					components.push(new BracketSupport(data.components[i].vessel_orientation, data.components[i].vessel_length, data.components[i].placement_diameter, data.components[i].bracketOptions, data.components[i].distribution_length, data.components[i].bracketDims, data.components[i].plateDims, data.components[i].ribType,
						data.components[i].ribDims, data.components[i].legOptions, data.components[i].hole_positions, data.components[i].L, data.components[i].attachment_x, data.components[i].attachment_y, data.components[i].attachment_z, data.components[i].orientation, data.components[i].direction, meshMaterial, dimDiv));
					break;
				case "21": // Leg Support
					componentsInfo.push( data.components[i] );
					// componentsInfo.push( { 'component_id': data.components[i].component_id, 'componentDesign_name': data.components[i].componentDesign_name } );
					components.push(new LegSupport( components[componentIndices[data.components[i].attachToShell]], data.components[i].legOptions, data.components[i].legDims, data.components[i].hasBracket, data.components[i].bracketDims, data.components[i].outer_diameter, data.components[i].intersect, data.components[i].numLegs, data.components[i].offsetRotation, data.components[i].baseType, data.components[i].baseDims,
						data.components[i].attachment_x, data.components[i].attachment_y, data.components[i].attachment_z, data.components[i].direction, meshMaterial, dimDiv));
					break;
				case "22": // Ring Support

					break;
				case "23": // Intermidiate Head

					break;
				case "24": // Groups of Nozzles / Openings

					break;
				case "25": // Nozzle/Loc.Loads on Cyl.Sh

					break;
				case "26": // Nozzle/Loc.Loads on Ends

					break;
				case '27': // Line Loads

					break;
				case "28": // Attachment Lug
					componentsInfo.push( { 'component_id': data.components[i].component_id, 'componentDesign_name': data.components[i].componentDesign_name } );
					components.push(new AttachmentLug( data.components[i].lugSettings, components[componentIndices[data.components[i].attachToShell]], data.components[i].distribution_length, data.components[i].lugRibDims, data.components[i].lugDims, data.components[i].hasBracket, data.components[i].bracketDims,
						data.components[i].attachment_x, data.components[i].attachment_y, data.components[i].attachment_z, data.components[i].orientation, data.components[i].direction, meshMaterial, dimDiv));
					break;
				case "29": // Lifting Lugs

					break;
				case "30": // Lifting Trunnions
					componentsInfo.push( { 'component_id': data.components[i].component_id, 'componentDesign_name': data.components[i].componentDesign_name } );
					components.push(new LiftingTrunnions( components[componentIndices[data.components[i].attachToShell]], data.components[i].trunnionSettings, data.components[i].trunnionDims, data.components[i].hasBracket, data.components[i].bracketDims,
						data.components[i].attachment_x, data.components[i].attachment_y, data.components[i].attachment_z, data.components[i].orientation, data.components[i].direction, meshMaterial, dimDiv));
					break;
				case "31": // Half Pipe Jacket

					break;
				case "32": // Backing Ring

					break;
				case "33": // Intermediate Cylindrical Shell

					break;
				case "34": // Intermediate Conical Shell

					break;
				case "35": // Platform
					componentsInfo.push( { 'component_id': data.components[i].component_id, 'componentDesign_name': data.components[i].componentDesign_name } );
					components.push(new Platform( components[componentIndices[data.components[i].attachToShell]], data.components[i].platformSettings, data.components[i].platformDims, data.components[i].ladderDims, data.components[i].segments,
						data.components[i].attachment_x, data.components[i].attachment_y, data.components[i].attachment_z, data.components[i].orientation, data.components[i].direction, meshMaterial, dimDiv));
					break;
				default:
					console.warn("Unknown component : ", data.components[i].componentDesign_id);
				}
			}

			// console.log(componentIndices);
			// if (sceneComponents > 0)
			// 	sceneCenter.divideScalar(sceneComponents);

			init();
			animate();

			function init() {
				scene = new THREE.Scene();

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
					VesselUtils.mergeGroupChildren( geo, components[i].group, arr );
					sceneGeometry.merge( geo.clone() );
				}
				sceneGeometry.computeBoundingSphere()
				totalLength = 1.65 * sceneGeometry.boundingSphere.radius;
				sceneCenter = sceneGeometry.boundingSphere.center.clone();

				// camera
				camera = new THREE.PerspectiveCamera( 40, window.innerWidth / window.innerHeight, 1, 50 * totalLength);
				camera.position.set( sceneCenter.x + totalLength, sceneCenter.y + totalLength, sceneCenter.z + totalLength );
				scene.add( camera );

				// controls
				controls = new THREE.OrbitControls( camera, renderer.domElement );
				controls.minDistance = totalLength / 10;
				controls.maxDistance = 10 * totalLength;
				controls.target0 = sceneCenter;
				controls.target = sceneCenter;
				controls.maxPolarAngle = Math.PI * 2;
				controls.screenSpacePanning = true;
				controls.update();
				// console.log(controls);

				scene.add( new THREE.AmbientLight( 0x222222 ) );

				var light = new THREE.PointLight( 0xffffff, 1 );
				camera.add( light );

				scene.add( new THREE.AxesHelper( 5000 ) );

				group = new THREE.Group();
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
					group.add(components[i].finalGroup);
					componentsUUID.push([ components[i].finalGroup.children[0].uuid, components[i].finalGroup.children[1].uuid ]);
					// group.add(components[i].group);
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

			}

			function onMouseMove( event ) {

				// calculate mouse position in normalized device coordinates
				// (-1 to +1) for both components

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
			}
			function onRightClick ( event ) {
				clickInfo.userHasRightClicked = true;
			  clickInfo.x = event.clientX;
			  clickInfo.y = event.clientY;
			}

			function animate(){

				requestAnimationFrame( animate );
				render();
				clickInfo.userHasClicked = false;
				clickInfo.userHasRightClicked = false;
			}

			function render() {

				// update the picking ray with the camera and mouse position
				raycaster.setFromCamera( mouse, camera );
				// console.log(clickInfo.userHasClicked);
				// calculate objects intersecting the picking ray
				var intersects = raycaster.intersectObjects( childs );

				// console.log(scene.children[3]);
				if ( intersects.length > 0 ) {
					if ( INTERSECTED != intersects[ 0 ].object ) {
						if ( INTERSECTED ) INTERSECTED.material.emissive.setHex( INTERSECTED.currentHex );
						INTERSECTED = intersects[ 0 ].object;
						INTERSECTED.currentHex = INTERSECTED.material.emissive.getHex();
						INTERSECTED.material.emissive.setHex( 0x2222dd );
					}
				} else {
					if ( INTERSECTED ) INTERSECTED.material.emissive.setHex( INTERSECTED.currentHex );
					INTERSECTED = null;
				}

				if( clickInfo.userHasClicked ) {
					if (INTERSECTED == null) {
						console.log( 'Selection cleared' );
						clickInfo.clickCounter = 0;
						clickInfo.currentUUID = [ '', '' ];
					} else {
						clickInfo.clickCounter ++;
						for (var i = 0; i < componentsUUID.length; i++)
							if ( componentsUUID[i][0] === INTERSECTED.uuid || componentsUUID[i][1] === INTERSECTED.uuid ) {
								if ( clickInfo.currentUUID === componentsUUID[i][0] || clickInfo.currentUUID === componentsUUID[i][1] )
									clickInfo.clickCounter ++;
								else {
									clickInfo.currentUUID = INTERSECTED.uuid;
									clickInfo.clickCounter = 0;
								}
								console.log( VesselUtils.printInfo( componentsInfo[i] ) );
								if ( clickInfo.clickCounter > 1 ) {
									console.log( 'Double clicked on component_id : ', componentsInfo[i].component_id )
									clickInfo.clickCounter = 0;
									clickInfo.currentUUID = [ '', '' ];
								}
							}
					}
				}
				if ( INTERSECTED != null && clickInfo.userHasRightClicked ) {
					for (var i = 0; i < componentsUUID.length; i++)
						if ( componentsUUID[i][0] === INTERSECTED.uuid || componentsUUID[i][1] === INTERSECTED.uuid )
							console.log( 'Right clicked on component_id', componentsInfo[i].component_id );
				}

				renderer.render( scene, camera );
			}
			window.addEventListener( 'mousemove', onMouseMove, false );
			window.addEventListener( 'click', onMouseClick, false );
			window.addEventListener( 'contextmenu', onRightClick, false );
			window.requestAnimationFrame(render);
		}).fail(function(){

		}).always(function(){

		});
	</script>

	</body>
</html>
