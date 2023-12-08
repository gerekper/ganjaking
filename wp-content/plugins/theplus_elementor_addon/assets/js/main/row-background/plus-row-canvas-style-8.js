/*--bottom bubble js ---*/
function snow_particles_background(canvas_scene, canvas_inner) {
	"use strict";
	let circles, target, animateHeader = true;
	let canvas = canvas_inner;
	let width = canvas_scene.innerWidth();
	let height = canvas_scene.innerHeight();
	let canvas_header = canvas_scene;
	let ctx = canvas.getContext('2d');

	initHeader();
	addListeners();

	function initHeader() {
		canvas.width = width;
		canvas.height = height;
		target = {
			x: 0,
			y: height
		};
		canvas_header.css({
			'height': height + 'px'
		});
		circles = [];
		for (let x = 0; x < width * 0.5; x++) {
			let c = new Circle();
			circles.push(c);
		}
		animate();
	}

	function addListeners() {
		window.addEventListener('scroll', scrollCheck);
		window.addEventListener('resize', resize);
	}

	function scrollCheck() {
		if (document.body.scrollTop > height) animateHeader = false;
		else animateHeader = true;
	}

	function resize() {
		width = window.innerWidth;
		height = window.innerHeight;
		canvas_header.css({
			'height': height + 'px'
		});
		canvas.width = width;
		canvas.height = height;
	}

	function animate() {
		if (animateHeader) {
			ctx.clearRect(0, 0, width, height);
			for (let i in circles) {
				circles[i].draw();
			}
		}
		requestAnimationFrame(animate);
	}


	function Circle() {
		let $this = this;

		(function () {
			$this.pos = {};
			init();
		})();

		function init() {
			$this.pos.x = Math.random() * width;
			$this.pos.y = height + Math.random() * 100;
			$this.alpha = 0.1 + Math.random() * 0.4;
			$this.scale = 0.1 + Math.random() * 0.3;
			$this.velocity = Math.random();
		}

		this.draw = function () {
			if ($this.alpha <= 0) {
				init();
			}
			$this.pos.y -= $this.velocity;
			$this.alpha -= 0.0003;
			ctx.beginPath();
			ctx.arc($this.pos.x, $this.pos.y, $this.scale * 10, 0, 2 * Math.PI, false);
			ctx.fillStyle = 'rgba(255,255,255,' + $this.alpha + ')';
			ctx.fill();
		};
	}
}
/*--bottom bubble js ---*/