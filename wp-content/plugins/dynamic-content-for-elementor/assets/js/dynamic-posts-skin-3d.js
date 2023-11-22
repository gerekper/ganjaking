/**
* demo3.js
* http://www.codrops.com
*
* Licensed under the MIT license.
* http://www.opensource.org/licenses/mit-license.php
*
* Copyright 2019, Codrops
* http://www.codrops.com
*/

var scene3d_istance = null;
var Widget_DCE_Dynamicposts_3d_Handler = function ($scope, $) {

	var elementSettings = dceGetElementSettings($scope);
	var scene3d = $scope.find('.dce-posts-container.dce-skin-3d');
	var postsList = $scope.find('.dce-3d-wrapper .dce-item-3d');
	var is3dEnabled = false;

	//Animating flag - is our app animating
	var isAnimating = false;
	var elementsQuantity = postsList.length;
	var camera, scene, renderer;
	var controls;
	var objects = [];
	var targets = { fila: [], circle: [], sphere: [], helix: [], grid: [] };
	var targets1 = { fila: [], circle: [], sphere: [], helix: [], grid: [] };
	var mouseX = 0, mouseY = 0;
	var windowHalfX = window.innerWidth / 2;
	var windowHalfY = window.innerHeight / 2;
	var targetSelect;

	// default values
	var panelWidth = Number(elementSettings[dceDynamicPostsSkinPrefix+'size_plane_3d']) || 320;
	var panelSpace = 400;
	var distanza = 1000;
	var diametro = (elementsQuantity*(panelWidth+panelSpace))/Math.PI;
	var raggio = diametro/2;
	var camDefaultY = 300;
	var enableBlur = Boolean(elementSettings[dceDynamicPostsSkinPrefix+'blur_depth_3d']) || false
	var positionType = elementSettings[dceDynamicPostsSkinPrefix+'type_3d']	 || 'circle';

	// il tipo di posizionamento
	var positioning = targets.circle;
	if ( positionType == 'fila' ){
		positioning = targets.fila;
	}

	var currentIndex = 0;

	init();
	animate();

	function init() {

		scene = new THREE.Scene();

		// HTML
		for ( var i = 0; i < elementsQuantity; i += 1 ) {
			var element = document.createElement( 'div' );
			element.className = 'dce-3d-element dce-3d-element-'+i;
			postsList.eq(i).detach().appendTo($(element));
			var link_area = document.createElement( 'div' );
			link_area.className = 'dce-3d-linkarea';
			element.appendChild( link_area );

			// Randomize initial positions
			var object = new THREE.CSS3DObject( element );
			object.position.x = Math.random() * 10000 - 6000;
			object.position.y = Math.random() * 10000 - 6000;
			object.position.z = Math.random() * 10000 - 6000;
			scene.add( object );
			objects.push( object );
		}

		var step = 0;

		// Shapes
		// sphere
		var vector = new THREE.Vector3();

		for ( var i = 0, l = objects.length; i < l; i ++ ) {
			var phi = Math.acos( - 1 + ( 2 * i ) / l );
			var theta = Math.sqrt( l * Math.PI ) * phi;
			var object = new THREE.Object3D();
			object.position.setFromSphericalCoords( 800, phi, theta );
			vector.copy( object.position ).multiplyScalar( 2 );
			object.lookAt( vector );
			targets.sphere.push( object );
		}

		// helix
		var vector = new THREE.Vector3();

		for ( var i = 0, l = objects.length; i < l; i ++ ) {
			var theta = i * 0.175 + Math.PI;
			var y = - ( i * 18 ) + 450;
			var object = new THREE.Object3D();

			object.position.setFromCylindricalCoords( 900, theta, y );
			vector.x = object.position.x * 2;
			vector.y = object.position.y;
			vector.z = object.position.z * 2;
			object.lookAt( vector );
			targets.helix.push( object );
		}

		// fila
		for ( var i = 0; i < objects.length; i ++ ) {
			var object = new THREE.Object3D();
			object.position.x = 0;
			object.position.y = 0;
			object.position.z = step;
			step -= (distanza+panelSpace);
			targets.fila.push( object );
		}

		// circle
		for ( var i = 0; i < objects.length; i ++ ) {
			targets.circle.push( rotationCalculation(i,raggio) );
			targets1.circle.push( rotationCalculation(i,raggio+distanza) );
			step += (2 * Math.PI) / elementsQuantity;
		}

		// grid
		for ( var i = 0; i < objects.length; i ++ ) {
			var object = new THREE.Object3D();
			object.position.x = ( ( i % 5 ) * 200 ) - 400;
			object.position.y = ( - ( Math.floor( i / 5 ) % 5 ) * 200 ) + 400;
			object.position.z = ( Math.floor( i / 25 ) ) * 1000 - 2000;
			targets.grid.push( object );
		}

		camera = new THREE.PerspectiveCamera( 40, window.innerWidth / window.innerHeight, 1, 10000 );
		targetSelect = objects[0];
		renderer = new THREE.CSS3DRenderer();
		renderer.setSize( jQuery('#dce-scene-3d-container')[0].clientWidth, window.innerHeight );
		$scope.find('#dce-scene-3d-container')[0].appendChild( renderer.domElement );

		// controls
		if (positionType == 'circle'){
			controls = new THREE.OrbitControls( camera, renderer.domElement );
		} else if (positionType == 'fila'){
			controls = new THREE.MapControls( camera, renderer.domElement );
		}

		if (positionType == 'circle'){
			controls.minDistance = -diametro;
			controls.maxDistance = diametro + distanza;
		}

		//Map
		controls.enableDamping = true; // an animation loop is required when either damping or auto-rotation are enabled
		controls.dampingFactor = 0.05;
		controls.enableZoom = false;
		controls.autoRotate = false;
		controls.screenSpacePanning = true;
		controls.maxPolarAngle = Math.PI / 2;
		controls.maxPolarAngle = Math.PI / 1.7;

		controls.addEventListener( 'change', render );

		// Initial Positions
		camReset();
		transform( positioning, 2000 );

		document.addEventListener( 'mousemove', onDocumentMouseMove, false );
		window.addEventListener( 'resize', onWindowResize, false );

		if(elementSettings[dceDynamicPostsSkinPrefix+'mousewheel_3d']) {
			scene3d.on("mousewheel DOMMouseScroll", onMouseWheel);
		}

		addElementsEvents();

		setTimeout(function(){
			// rilancio base per elaborare le cose dopo averle spostate negli elements..
			Widget_DCE_Dynamicposts_base_Handler($scope, $);
		},300);
		if ( elementSettings[dceDynamicPostsSkinPrefix+'3d_center_at_start'] === 'yes' ) {
			centerItem(0, 1000);
		}
	}

	let scrollStopAtEnd = elementSettings[dceDynamicPostsSkinPrefix+'mousewheel_3d_stop_at_end'] === 'yes';

	function onMouseWheel(event)
	{
		//Normalize event wheel delta
		var delta = event.originalEvent.wheelDelta / 30 || -event.originalEvent.detail;

		//If the user scrolled up, it goes to previous slide, otherwise - to next slide
		if(!isAnimating){
			if(delta < -1)
			{
				if(positionType == 'circle'){
					currentIndex += 1;
					if ( currentIndex >= elementsQuantity ) {
						currentIndex = 0;
						if ( scrollStopAtEnd ) {
							scene3d.off("mousewheel DOMMouseScroll", onMouseWheel);
							return;
						}
					}
				} else if(positionType == 'fila'){
					currentIndex = currentIndex > 0 ? currentIndex-1 : 0;
				}
				centerItem( currentIndex, 1000 );

			}
			else if(delta > 1)
			{
				if(positionType == 'circle'){
					currentIndex = currentIndex > 0 ? currentIndex-1 : elementsQuantity-1;
				} else if(positionType == 'fila'){
					currentIndex += 1;
					if ( currentIndex >= elementsQuantity ) {
						currentIndex = 0;
						if ( scrollStopAtEnd ) {
							scene3d.off("mousewheel DOMMouseScroll", onMouseWheel);
							return;
						}
					}
				}
				centerItem( currentIndex, 1000 );

			}
		}
		event.preventDefault();
	}

	function rotationCalculation(i,r) {
		var theta = ((Math.PI*2) / elementsQuantity);
		var angle = (theta * i)+(Math.PI/2);

		var object = new THREE.Object3D();
		object.position.x = r * Math.cos(angle);
		object.position.y = 0;
		object.position.z = r * Math.sin(angle);

		return object;
	}

	function addElementsEvents() {
		// al doppioclick sullo sfondo ricentro la scena
		$scope.find('#dce-scene-3d-container > div')[0].addEventListener( 'dblclick', function (el) {
			camReset();
			$scope.find('#dce-scene-3d-container > div').removeClass('hide-cursor');

			el.stopPropagation();
		}, false );

		$scope.find('#dce-scene-3d-container > div')[0].addEventListener( 'mousedown', function (el) {
			if(!is3dEnabled)
			$(this).addClass('grab');

		}, false );
		$scope.find('#dce-scene-3d-container > div')[0].addEventListener( 'mouseup', function (el) {
			if(!is3dEnabled)
			$(this).removeClass('grab');

		}, false );

		for ( var i = 0; i < objects.length; i ++ ) {
			var object = objects[ i ];
			(function(index){
				object.element.addEventListener( 'click', function (el) {
					el.stopPropagation();
					currentIndex = index;
					centerItem( index, 1000 );
					is3dEnabled = true;
					$scope.find('#dce-scene-3d-container > div').addClass('hide-cursor');

					// interrompo la rotazione
					controls.enableRotate = false;
				}, false );
			})(i);
		}

		$scope.find('.dce-3d-navigation .dce-3d-next')[0].addEventListener( 'click', function (el) {
			el.stopPropagation();
			currentIndex = currentIndex < elementsQuantity-1 ? currentIndex+1 : 0;
			centerItem( currentIndex, 1000 );
		}, false );

		$scope.find('.dce-3d-navigation .dce-3d-prev')[0].addEventListener( 'click', function (el) {
			el.stopPropagation();
			currentIndex = currentIndex > 0 ? currentIndex-1 : elementsQuantity-1;
			centerItem( currentIndex, 1000 );
		}, false );

		document.addEventListener("keyup", (e) => {
			if (e.keyCode == 27 && is3dEnabled) { // esc
			   camReset();
			}
			if (e.keyCode == 39 && is3dEnabled) { // right
			   currentIndex = currentIndex > 0 ? currentIndex-1 : elementsQuantity-1;
			   centerItem( currentIndex, 1000 );
			}
			if (e.keyCode == 37 && is3dEnabled) { // left
			   currentIndex = currentIndex < elementsQuantity-1 ? currentIndex+1 : 0;
			   centerItem( currentIndex, 1000 );
			}
		});
	}
	function centerItem( index, duration ) {
		if(positionType == 'circle'){
			//circle
			panCam(targets.circle[index].position.x, targets.circle[index].position.y, targets.circle[index].position.z, targets1.circle[index].position.x, targets1.circle[index].position.y, targets1.circle[index].position.z, duration);
		}else if(positionType == 'fila'){
			//fila
			panCam(targets.fila[index].position.x, targets.fila[index].position.y, targets.fila[index].position.z, targets.fila[index].position.x, camDefaultY, targets.fila[index].position.z+distanza, duration);
		}
		$('.dce-3d-trace').text(index);
	}

	function camReset(){
		  $scope.find('.dce-3d-navigation').removeClass('dce-pancam-item');

		  if(positionType == 'circle') {
			//circle
			var xTarget = targets1.circle[currentIndex].position.x;
			var yTarget = camDefaultY;
			var zTarget = targets1.circle[currentIndex].position.z;
		  }else if(positionType == 'fila'){
			//fila
			var xTarget = targets.fila[0].position.x;
			var yTarget = camDefaultY;
			var zTarget = targets.fila[0].position.z+distanza;
			currentIndex = 0;
		  }

		  // riabilito la rotazione
		  controls.enableRotate = true;
		  is3dEnabled = false;

		  gsap.to(camera.position, {
				duration: 1,
				ease: "power3.out",
				x: xTarget,
				y: yTarget,
				z: zTarget,
			});
		  gsap.to(controls.target, {
				duration: 1,
				ease: "power3.out",
				x: 0,
				y: 0,
				z: 0,
			});

		  for ( var i = 0; i < objects.length; i ++ ) {
			objects[i].element.childNodes[1].style.display = 'block';
			if(enableBlur) blurObject(objects[i],true);
		  }

	}

	function panCam(xTarget,yTarget,zTarget,xTarget1,yTarget1,zTarget1,tweenDuration){
		  $scope.find('.dce-3d-navigation').addClass('dce-pancam-item');

		  for ( var i = 0; i < objects.length; i ++ ) {
			if(i == currentIndex){
				objects[i].element.childNodes[1].style.display = 'none';

				if(enableBlur)
				gsap.to(objects[i].element, {
					duration: 1,
					ease: "power3.inOut",
					filter: "blur(0px)",
				});
			}else{
				objects[i].element.childNodes[1].style.display = 'block';

				if(enableBlur)
				gsap.to(objects[i].element, {
					duration: 1,
					ease: "power1.in",
					filter: "blur(7px)",
				});
			}
		  }


		  isAnimating = true;
		  gsap.to(camera.position, {
				duration: tweenDuration/1000,
				ease: "power3.inOut",
				x: xTarget1,
				y: yTarget1,
				z: zTarget1,
				onComplete: function(){
					isAnimating = false;
				}
			});
		  gsap.to(controls.target, {
				duration: tweenDuration/1000,
				ease: "power4.inOut",
				x: xTarget,
				y: yTarget,
				z: zTarget
			});
	}

	function transform( targets, duration ) {
		for ( var i = 0; i < objects.length; i ++ ) {
			var object = objects[ i ];
			var target = targets[ i ];
			tweenItem(object,target,duration);

			gsap.to(object.position, {
				duration: (duration * 2)/1000,
				onUpdate: render
			});
		}
	}

	function tweenItem( object, target, duration ) {
		var dur = Math.random() * duration + duration;

		gsap.to(object.position, {
				duration: dur/1000,
				ease: "power3.inOut",
				x: target.position.x,
				y: target.position.y,
				z: target.position.z
			});

		gsap.to(object.rotation, {
				duration: dur/1000,
				ease: "power3.inOut",
				x: target.rotation.x,
				y: target.rotation.y,
				z: target.rotation.z
			});
	}

	function onWindowResize() {
		camera.aspect = jQuery('#dce-scene-3d-container')[0].clientWidth / window.innerHeight;
		camera.updateProjectionMatrix();
		renderer.setSize( jQuery('#dce-scene-3d-container')[0].clientWidth, window.innerHeight );
		render();
	}

	function onDocumentMouseMove( event ) {
		mouseX = ( event.clientX - windowHalfX ) * 10;
		mouseY = ( event.clientY - windowHalfY ) * 10;
	}

	function animate() {
		requestAnimationFrame( animate );
		controls.update();
	}

	function render() {
		var time = Date.now() * 0.001;

		var rx = Math.sin( time * 0.7 ) * 0.5,
			ry = Math.sin( time * 0.3 ) * 0.5,
			rz = Math.sin( time * 0.2 ) * 0.5;

		for ( var i = 0; i < objects.length; i ++ ) {

			var object = objects[ i ];

			var distance = camera.position.z - object.position.z;

			if(!is3dEnabled){
				if(enableBlur){
					blurObject(object);
				}
			}

			if(positionType == 'circle'){
				object.lookAt( camera.position );
			}else if(positionType == 'fila'){
				if(i == currentIndex){
					object.lookAt( camera.position );
				}
			}
		}
		renderer.render( scene, camera );
	}

	function blurObject(object,animated = false){
		var point1 = new THREE.Vector3()
		point1.setFromMatrixPosition( camera.matrixWorld );
		var point1 = point1.clone();
		var point2 = object.position;
		var distance = (point1.distanceTo( point2 )/distanza)-1;
		distance = distance * (3*distance);
		if(animated){
			gsap.to(object.element, {
				duration: 0.6,
				webkitFilter:"blur(" +distance.toFixed(2)+ "px)",
				filter: "blur("+distance.toFixed(2)+"px)",
			});
		}else{
			gsap.set(object.element, {
				webkitFilter:"blur(" +distance.toFixed(2)+ "px)",
				filter: "blur("+distance.toFixed(2)+"px)",
			});
		}
	}
};

jQuery(window).on('elementor/frontend/init', function () {
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamicposts-v2.3d', Widget_DCE_Dynamicposts_3d_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamic-woo-products.3d', Widget_DCE_Dynamicposts_3d_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-dynamic-show-favorites.3d', Widget_DCE_Dynamicposts_3d_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-my-posts.3d', Widget_DCE_Dynamicposts_3d_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-sticky-posts.3d', Widget_DCE_Dynamicposts_3d_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-search-results.3d', Widget_DCE_Dynamicposts_3d_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-metabox-relationship.3d', Widget_DCE_Dynamicposts_3d_Handler);
	elementorFrontend.hooks.addAction('frontend/element_ready/dce-acf-relationship.3d', Widget_DCE_Dynamicposts_3d_Handler);
});
