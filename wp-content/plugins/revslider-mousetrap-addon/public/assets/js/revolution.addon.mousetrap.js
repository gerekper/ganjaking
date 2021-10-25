/**
 * @preserve
 * @version: 2.0.8 (03.10.2020)
 * @requires jquery.themepunch.revolution.js
 * @author ThemePunch
 */

;
(function ($) {
	var _R, rules = {
		rx: "rotationX",
		ry: "rotationY",
		rz: "rotation",
		sx: "scaleX",
		sy: "scaleY",
		op: "opacity"
	};
	var touch = 'ontouchend' in document;
	var radMin = Math.PI / 2 - 0.4;
	var radMax = Math.PI / 2 + 0.4;
	var TMBlock = {
		x: 0,
		y: 0,
		block: false
	};

	function setTMBlock(e) {
		TMBlock.x = e.clientX;
		TMBlock.y = e.clientY;
		TMBlock.block = false;
	}

	function calculateTMBlock(e) {
		var dx = TMBlock.x - e.clientX;
		var dy = TMBlock.y - e.clientY;
		var angle = Math.abs(Math.atan2(dy, dx));

		if (angle > radMin && angle < radMax) {
			TMBlock.block = 'no';
		} else {
			TMBlock.block = 'yes';
		}
	}

	window.RSMousetrap = function (slider) {
		if (slider === undefined || slider.length === 0 || slider[0].getElementsByClassName('rs-mtrap').length == 0) return;
		//slider.one('revolution.slide.onloaded', function() {
		if (jQuery === undefined || jQuery.fn === undefined) return;
		_R = jQuery.fn.revolution;
		if (_R && _R[slider[0].id]) init(slider[0].id);
		//});
	};

	//////////////////////////////////////////
	//	-	INITIALISATION OF MOUSETRAP 	-	//
	//////////////////////////////////////////
	var init = function (id) {

		//GENERAL PREPARE
		_R[id].mouseTraps = {
			layers: [],
			follow: [],
			defcursor: _R[id].c[0].style.cursor
		};

		// PREPARE FOLLOWING LAYERS
		prepareFollowingLayers(id);

		//START MOUSE LISTENER
		listenToMouse(id);
	};



	// PREPARE SINGLE LAYERS TO FOLLOW MOUSE
	var prepareFollowingLayers = function (id) {
		var layers = _R[id].c[0].getElementsByClassName('rs-mtrap'),
			i, u, s, fnd;

		for (i in layers)
			if (layers.hasOwnProperty(i)) _R[id].mouseTraps.layers.push(layers[i].id);

		_R[id].c.on('layerinitialised', function (e, a) {
			if (a === undefined || a.layer === undefined) return;
			if (_R[id].mouseTraps.layers.indexOf(a.layer) >= 0) {
				var _ = _R[id]._L[a.layer];
				_.mTrap = _.mousetrap === undefined ? [] : _.mousetrap.split(";");
				_.mouseTrap = {
					hide: true,
					follow: 'slider',
					offset: {
						x: 0,
						y: 0
					},
					olayer: [],
					delay: 0,
					ease: 'none',
					radius: 0,
					block: {
						x: false,
						y: false
					},
					revert: {
						use: false,
						speed: 0.0001,
						ease: 'none'
					}
				};
				for (u in _.mTrap) {
					if (!_.mTrap.hasOwnProperty(u)) continue;
					s = _.mTrap[u].split(":");
					switch (s[0]) {
						case "f":
							_.mouseTrap.follow = s[1];
							break;
						case "h":
							_.mouseTrap.hide = s[1] === "f" || s[1] === "false" || s[1] === false ? false : true;
							break;
						case "d":
							_.mouseTrap.delay = parseInt(s[1], 0) / 1000;
							break;
						case "mr":
							if (s[1] !== "") _.mouseTrap.radius = parseInt(s[1], 0);
							break;
						case "e":
							_.mouseTrap.ease = s[1];
							break;

						case "r":
							_.mouseTrap.revert.use = s[1] === "t" || s[1] === "true" || s[1] === true ? true : false;
							break;
						case "rs":
							_.mouseTrap.revert.speed = (parseInt(s[1], 0) / 1000) + 0.0001;
							break;
						case "re":
							_.mouseTrap.revert.ease = s[1];
							break;

						case "ro":
							_.mouseTrap.rotate.use = s[1] === "t" || s[1] === "true" || s[1] === true ? true : false;
							break;

						case "rus":
							_.mouseTrap.rules = _.mouseTrap.rules === undefined ? {} : _.mouseTrap.rules;
							_.mouseTrap.rules.speed = (parseInt(s[1], 0) / 1000) + 0.0001;
							break;
						case "rue":
							_.mouseTrap.rules = _.mouseTrap.rules === undefined ? {} : _.mouseTrap.rules;
							_.mouseTrap.rules.ease = s[1];
							break;

						case "bx":
							_.mouseTrap.block.x = s[1] === "t" || s[1] === "true" || s[1] === true ? true : false;
							break;
						case "by":
							_.mouseTrap.block.y = s[1] === "t" || s[1] === "true" || s[1] === true ? true : false;
							break;

						case "ola1":
						case "ola2":
						case "ola3":
						case "ola4":
						case "ola5":
							_.mouseTrap.olayer.push(s[1]);
							break;

						case "ox":
							if (s[1] !== "") _.mouseTrap.offset.x = s[1].split(",");
							break;
						case "oy":
							if (s[1] !== "") _.mouseTrap.offset.y = s[1].split(",");
							break;
						default:
							fnd = false;
							for (i in rules) {
								if (fnd || !rules.hasOwnProperty(i)) continue;
								if (s[0].indexOf(i) >= 0) {
									fnd = true;
									_.mouseTrap.rules = _.mouseTrap.rules === undefined ? {} : _.mouseTrap.rules;
									_.mouseTrap.rules[i] = _.mouseTrap.rules[i] === undefined ? {
										min: 0,
										max: 0,
										axis: "none",
										calc: "direction",
										offset: 0
									} : _.mouseTrap.rules[i];
									if (s[0] === i + "d") _.mouseTrap.rules[i].axis = s[1] === "h" ? "horizontal" : s[1] === "b" ? "both" : s[1] === "c" ? "center" : "vertical";
									if (s[0] === i + "min") _.mouseTrap.rules[i].min = parseFloat(s[1]);
									if (s[0] === i + "max") _.mouseTrap.rules[i].max = parseFloat(s[1]);
									if (s[0] === i + "o") _.mouseTrap.rules[i].offset = parseInt(s[1], 0);
									if (s[0] === i + "c") _.mouseTrap.rules[i].calc = s[1] === "s" ? "distance" : s[1] === "r" ? "direction" : s[1];
									_.mouseTrap.rules[i].len = _.mouseTrap.rules[i].max - _.mouseTrap.rules[i].min;
								}

							}
							break;
					}
				}


				if (_.mouseTrap.rules !== undefined) {
					_.mouseTrap.rules.speed = _.mouseTrap.rules.speed === undefined ? 0.2 : _.mouseTrap.rules.speed;
					_.mouseTrap.rules.ease = _.mouseTrap.rules.ease === undefined ? 'none' : _.mouseTrap.rules.ease;
				}
				_.lp.wrap('<mousetrap style="position:absolute;top:0px;left:0px;display:block;"></mousetrap>');
				_.mouseTrap.c = _.p[0].getElementsByTagName('mousetrap')[0];
				_.mouseTrap.offset = {
					x: _R.revToResp(_.mouseTrap.offset.x, _R[id].rle, 0),
					y: _R.revToResp(_.mouseTrap.offset.y, _R[id].rle, 0)
				};
				for (i in _.mouseTrap.offset.x) {
					_.mouseTrap.offset.x[i] = parseInt(_.mouseTrap.offset.x[i], 0);
					_.mouseTrap.offset.y[i] = parseInt(_.mouseTrap.offset.y[i], 0);
				}
				_.mouseTrap.radius = _R.revToResp(_.mouseTrap.radius, _R[id].rle, 0);
				tpGS.gsap.set(_.mouseTrap.c, {
					x: 0,
					y: 0,
					transformPerspective: 2000
				});
				_.mouseTrap.cache = {};
				_.mouseTrap.last = {};
				if (_._ingroup) _.groupparent = _R[id]._L[_.c.closest('rs-group')[0].id];
				if (_.mouseTrap.follow === "slider") _.mouseTrap.activeFollow = true;
				if (_.mouseTrap.follow === "self") {
					_.mouseTrap.hide = false;
					_.mouseTrap.block = {
						x: true,
						y: true
					};
					_.mouseTrap.caller = {
						layerid: _.c[0].id,
						mouse: {
							x: 0,
							y: 0
						}
					}
					waitForListener(_.mouseTrap.caller.layerid, _.c[0].id, id, 0);
				} else
				if (_.mouseTrap.follow === "olayer") {
					for (var i in _.mouseTrap.olayer) {
						_.mouseTrap.caller = {
							olayer: true,
							mouse: {
								x: 0,
								y: 0
							}
						}
						waitForListener(_.mouseTrap.olayer[i], _.c[0].id, id, 0);
					}
				}
				_R[id].mouseTraps.follow.push(a.layer);
			}
		});
	}

	var waitForListener = function (sid, lid, id, ncal) {
		if (_R[id]._L[sid] !== undefined) {
			_R[id]._L[sid].p.on('mouseenter touchstart', function (e) {

				if (touch) {
					var e = e.originalEvent;
					if (e.touches) e = e.touches[0];
					setTMBlock(e);
				}

				if (_R[id]._L[lid].mouseTrap.caller.olayer) _R[id]._L[lid].mouseTrap.caller.layerid = sid;
				_R[id]._L[lid].mouseTrap.activeFollow = true;
			});

			_R[id]._L[sid].p.on('mousemove touchmove', function (e) {

				if (TMBlock.block === 'no') return;

				if (touch) {
					var te = e;
					e = e.originalEvent;
					if (e.touches) e = e.touches[0];

					if (!TMBlock.block) calculateTMBlock(e);
					if (TMBlock.block === 'yes') {
						te.preventDefault();
					}
					if (TMBlock.block === 'no') {
						return;
					}
				}

				var rect = this.getBoundingClientRect();
				if (_R[id]._L[lid].mouseTrap.caller.olayer) _R[id]._L[lid].mouseTrap.caller.layerid = sid;
				_R[id]._L[lid].mouseTrap.activeFollow = true;
				_R[id]._L[lid].mouseTrap.caller.mouse = {
					x: e.clientX - rect.left,
					y: e.clientY - rect.top
				};

			});
			_R[id]._L[sid].p.on('mouseleave touchend', function (e) {
				_R[id]._L[lid].mouseTrap.activeFollow = false;
				revertAnim(_R[id]._L[lid]);
			});
		} else {
			ncal++;
			if (ncal < 50) setTimeout(function () {
				waitForListener(sid, lid, id, ncal);
			}, 100);
			else console.warn('Mouse Trap Sensor:' + sid + ' is not existing.')
		}
	}

	/*
	CALCULATE POSITIONS, SCALE, ROTATION AND SO ON
	 */

	var getMath = function (_) {
		var x, y, flip, alpha, dish, disv, dis, sensor, noreact = 0,
			w, h, mx, my;

		_.new = {
			x: 0 - _.group.x + _.mouse.x - _.slider.left,
			y: 0 - _.group.y + _.mouse.y - _.slider.top
		};

		// Move within a Radius Only		
		if (_.radius > 0) {
			x = _.new.x - _.orig.x;
			y = _.new.y - _.orig.y;
			if (Math.sqrt((x * x) + (y * y)) > _.radius && x !== 0 && y !== 0) {
				flip = _.new.x < _.orig.x ? -1 : 1;
				alpha = Math.atan(y / x);
				_.new.x = _.orig.x + (Math.cos(alpha) * _.radius * flip);
				_.new.y = _.orig.y + (Math.sin(alpha) * _.radius * flip);
			} else {
				_.new.x = x > _.radius ? _.new.x - x - _.radius : _.orig.x - _.new.x > _.radius ? _.orig.x - _.radius : _.new.x;
				_.new.y = y > _.radius ? _.new.y - y - _.radius : _.orig.y - _.new.y > _.radius ? _.orig.y - _.radius : _.new.y;
			}
		}


		// CHECK SPECIAL EFFECTS
		if (_.rules !== undefined) {
			_.new.rules = {
				ease: _.rules.ease,
				overwrite: true,
				duration: _.rules.speed,
				transformOrigin: _.origin
			};
			for (var i in rules)
				if (rules.hasOwnProperty(i) && _.rules[i] !== undefined) {
					//BOTH AXIS !!
					if (_.rules[i].axis === "both") {
						_.new.rules.rotation = _.last === undefined || _.last.rules === undefined || _.last.rules.rotation === undefined ? 0 : _.last.rules.rotation;
						_.last.x = _.last.x === undefined ? _.new.x : _.last.x;
						_.last.y = _.last.y === undefined ? _.new.y : _.last.y;
						_.new.ufr = _.last.ufr === undefined ? {
							x: _.last.x,
							y: _.last.y
						} : _.last.ufr;
						x = _.new.ufr.x - _.new.x;
						y = _.new.ufr.y - _.new.y;
						if (Math.sqrt((x * x) + (y * y)) > 5) {
							_.new.ufr.x = _.new.x;
							_.new.ufr.y = _.new.y;
							_.new.rules.rotation = Math.round(Math.atan2(y, x) * tpGS.RAD2DEG) - 90 || _.new.rules.rotation;
							_.new.rules.rotation += "_short"
						}
					} else if (_.rules[i].axis === "center") {
						_.new.rules.rotation = _.last === undefined || _.last.rules === undefined || _.last.rules.rotation === undefined ? 0 : _.last.rules.rotation;
						_.last.x = _.last.x === undefined ? _.new.x : _.last.x;
						_.last.y = _.last.y === undefined ? _.new.y : _.last.y;
						x = _.orig.x - _.new.x;
						y = _.orig.y - _.new.y;
						if (Math.sqrt((x * x) + (y * y)) > 5) {
							_.new.rules.rotation = Math.round(Math.atan2(y, x) * tpGS.RAD2DEG) - 90 || _.new.rules.rotation;
							_.new.rules.rotation += "_short"
						}
					} else if (_.rules[i].calc === "distance") {
						//DISTANCE BASED
						if (_.caller !== undefined) {
							_.new.width = w = _R[_.id]._L[_.caller.layerid].eow;
							_.new.height = h = _R[_.id]._L[_.caller.layerid].eoh;
							mx = _.caller.mouse.x;
							my = _.caller.mouse.y;

						} else {
							w = _R[_.id].width;
							h = _R[_.id].height;
							mx = _.realmouse.x;
							my = _.realmouse.y;
						}

						sensor = _.rules[i].axis === "horizontal" ? _.rules[i].offset !== 0 ? w * (_.rules[i].offset / 100) : w : _.rules[i].offset !== 0 ? h * (_.rules[i].offset / 100) : h;
						if (_.rules[i].offset !== 0) noreact = _.rules[i].axis === "horizontal" ? (w - sensor) / 2 : (h - sensor) / 2;
						dis = _.rules[i].axis === "horizontal" ? (mx - noreact) / sensor : (my - noreact) / sensor;
						if (_.rules[i].offset !== 0) dis = dis < 0 ? 0 : dis > 1 ? 1 : dis;
						_.new.rules[rules[i]] = _.rules[i].min + (_.rules[i].len * dis);
					} else if (_.rules[i].calc === "direction") {
						//DIRECTION BASED							
						dis = _.rules[i].axis === "vertical" ? _.last.y - _.new.y : _.last.x - _.new.x;
						if (Math.abs(dis) > 2) {
							_.new.rules[rules[i]] = _.last === undefined || _.last.rules === undefined || _.last.rules[rules[i]] === undefined ? 0 : _.last.rules[rules[i]];
							_.new.rules[rules[i]] = _.new.rules[rules[i]] + dis * 0.5
							_.new.rules[rules[i]] = _.new.rules[rules[i]] < _.rules[i].min ? _.rules[i].min : _.new.rules[rules[i]];
							_.new.rules[rules[i]] = _.new.rules[rules[i]] > _.rules[i].max ? _.rules[i].max : _.new.rules[rules[i]];


							_.last.y = _.new.y;
							_.last.x = _.new.x;
						}
					}
				}
		}

		// Block Axis if Needed
		_.new.x = _.block.x ? _.orig.x : _.new.x;
		_.new.y = _.block.y ? _.orig.y : _.new.y;
		return _.new;
	}

	/*
	REVERT TO 0 POSITION
	 */
	var revertAnim = function (_) {
		if (_.mouseTrap !== undefined && _.mouseTrap.revert !== undefined && _.mouseTrap.revert.use) {
			tpGS.gsap.to(_.p, _.mouseTrap.revert.speed, {
				/*left:_.calcx,top:_.calcy,*/
				x: 0,
				y: 0,
				overwrite: true,
				ease: _.mouseTrap.revert.ease
			});
			tpGS.gsap.to(_.mouseTrap.c, _.mouseTrap.revert.speed, {
				scaleX: 1,
				scaleY: 1,
				rotationX: 0,
				rotationY: 0,
				opacity: 1,
				rotation: 0,
				overwrite: true,
				ease: _.mouseTrap.revert.ease
			});
		}
	}

	/*
	DOANIMATE ELEMENTS
	 */
	var doAnimate = function (id, slide) {
		if (_R[id].mouseTrapFrame) _R[id].mouseTrapFrame = window.cancelAnimationFrame(_R[id].mouseTrapFrame);
		if (!_R[id].MTactiveSlide) return;

		var hide = -1,
			b = _R[id].MTactiveSlide.getBoundingClientRect();

		if (slide && slide.hasOwnProperty("getBoundingClientRect")) {
			b = slide.getBoundingClientRect();
		}

		for (var i in _R[id].mouseTraps.follow) {
			var t = b;
			var _ = _R[id]._L[_R[id].mouseTraps.follow[i]];
			if (_.mouseTrap.activeFollow !== true) continue;

			if (!_.MTparent) {
				if (_.p.closest("rs-static-layers").length > 0) {
					_.MTparent = {
						parent: _.p.closest("rs-static-layers")[0],
						type: "rs-static"
					}
				} else if (_.p.closest("rs-slide").length > 0) {
					_.MTparent = {
						parent: _.p.closest("rs-slide")[0],
						type: "rs-slide"
					}
				}
			}



			if (_.MTparent.type === "rs-static") {
				t = _.MTparent.parent.getBoundingClientRect();
			} else if (_.MTparent.parent !== _R[id].MTactiveSlide && !slide.hasOwnProperty("getBoundingClientRect")) {

				if (_R[id].carousel && _R[id].carousel.showLayersAllTime === "all") {
					t = _.MTparent.parent.getBoundingClientRect();
				} else {
					continue;
				}

			}



			var ox = parseInt(_.mouseTrap.offset.x[_R[id].level], 0) * _R[id].bw,
				oy = parseInt(_.mouseTrap.offset.y[_R[id].level], 0) * _R[id].bw;

			ox = _R[id].rtl ? -(_.eow - ox) : ox;

			var x = _R[id].clientX - ox,
				y = _R[id].clientY - oy;

			_.mouseTrap.last = getMath({
				rtl: _R[id].rtl,
				mouse: {
					x: x,
					y: y
				},
				last: _.mouseTrap.last,
				block: _.mouseTrap.block,
				ingroup: _._ingroup,
				group: _._ingroup ? {
					x: _.groupparent.calcx,
					y: _.groupparent.calcy
				} : {
					x: 0,
					y: 0
				},
				orig: {
					x: _.calcx,
					y: _.calcy
				},
				radius: _.mouseTrap.radius[_R[id].level] * _R[id].bw,
				rules: _.mouseTrap.rules,
				origin: ox + "px " + oy + "px",
				caller: _.mouseTrap.caller,
				realmouse: {
					x: _R[id].clientX,
					y: _R[id].clientY
				},

				slider: t,
				id: id
			});

			if (_.mouseTrap.last.width !== undefined) tpGS.gsap.set(_.p, {
				width: _.mouseTrap.last.width,
				height: _.mouseTrap.last.height,
				overwrite: true
			});
			_.pPointerStatus = _.mouseTrap.follow === "self" ? "auto" : "none";
			tpGS.gsap.to(_.p, {
				duration: _.mouseTrap.delay,
				x: _.mouseTrap.last.x - _.calcx,
				y: _.mouseTrap.last.y - _.calcy,
				pointerEvents: _.mouseTrap.follow === "self" ? "auto" : "none",
				overwrite: true,
				ease: _.mouseTrap.ease
			});
			// Causes problems on first hover
			// _.mouseTrap.last.rules.transformPerspective = _.knowTransformPerspective;			
			if (_.mouseTrap.last.rules !== undefined) tpGS.gsap.to(_.mouseTrap.c, _.mouseTrap.last.rules);
			if (hide === -1 || hide === true) hide = _.mouseTrap.hide;
		}
		if (hide === true) _R[id].c[0].style.cursor = "none";
		else if (hide === false || hide === -1) _R[id].c[0].style.cursor = _R[id].mouseTraps.defcursor;
	}


	//LISTEN TO MOUSE MOVE OVER SLIDER
	var listenToMouse = function (id) {
		_R[id].c.on('mousemove touchmove', function (e) {
			if (TMBlock.block === 'no') return;

			if (touch) {
				var te = e;
				e = e.originalEvent;
				if (e.touches) e = e.touches[0];

				if (!TMBlock.block) calculateTMBlock(e);
				if (TMBlock.block === 'yes') {
					te.preventDefault();
				}
				if (TMBlock.block === 'no') {
					return;
				}
			}

			_R[id].clientX = e.clientX;
			_R[id].clientY = e.clientY;
			if (!_R[id].mouseTrapFrame) _R[id].mouseTrapFrame = requestAnimationFrame(doAnimate.bind(this, id));
		});

		_R[id].c.on('touchstart', function (e) {
			if (touch) {
				var e = e.originalEvent;
				if (e.touches) e = e.touches[0];
				setTMBlock(e);
			}
		});

		_R[id].c.on('mouseleave touchend', function (e) {
			for (var i in _R[id].mouseTraps.follow) revertAnim(_R[id]._L[_R[id].mouseTraps.follow[i]]);
		});

		_R[id].c.on('revolution.slide.onafterswap', function (e, data) {
			setTimeout(function () {
				_R[id].MTactiveSlide = data.currentslide[0];
				doAnimate(id, _R[id].MTactiveSlide);
			}, 100);
		});

		_R[id].c.on('revolution.slide.onbeforeswap', function (e, data) {
			_R[id].MTactiveSlide = data.nextslide[0];
			doAnimate(id, _R[id].MTactiveSlide);
		});


		_R[id].c.on('layeraction', function (e, action, layer, event) {
			switch (action) {
				case "mtrapfollow":
					var _ = _R[id]._L[event.layer];
					if (_ !== undefined && _.mouseTrap !== undefined) {
						_.mouseTrap.activeFollow = true;
						_.mouseTrap.caller = {
							layerid: layer[0].id,
							mouse: {
								x: 0,
								y: 0
							}
						}
						_.mouseTrap.mouseMoveID = _.mouseTrap.mouseMoveID === undefined ? 0 : _.mouseTrap.mouseMoveID + 1;
						layer.on('touchstart.mouseMoveId' + _.mouseTrap.mouseMoveID, function (e) {
							if (touch) {
								var e = e.originalEvent;
								if (e.touches) e = e.touches[0];
								setTMBlock(e);
							}
						})

						layer.on('mousemove.mouseMoveId' + _.mouseTrap.mouseMoveID + ' ' + 'touchmove.mouseMoveId' + _.mouseTrap.mouseMoveID, function (e) {

							if (TMBlock.block === 'no') return;

							if (touch) {
								var te = e;
								e = e.originalEvent;
								if (e.touches) e = e.touches[0];

								if (!TMBlock.block) calculateTMBlock(e);
								if (TMBlock.block === 'yes') {
									te.preventDefault();
								}
								if (TMBlock.block === 'no') {
									return;
								}
							}

							var rect = this.getBoundingClientRect();

							_.mouseTrap.caller.mouse = {
								x: e.clientX - rect.left,
								y: e.clientY - rect.top
							};
						});
					}
					break;
				case "mtrapunfollow":
					var _ = _R[id]._L[event.layer];
					_.mouseTrap.activeFollow = false;

					layer.off('mousemove.mouseMoveId' + _.mouseTrap.mouseMoveID);
					layer.off('touchmove.mouseMoveId' + _.mouseTrap.mouseMoveID);
					layer.off('touchstart.mouseMoveId' + _.mouseTrap.mouseMoveID);
					revertAnim(_);
					break;
			}

		});
	}

})(jQuery);