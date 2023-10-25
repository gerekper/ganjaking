"use strict";

var animationElementPosition = function animationElementPosition(idClass) {
  var element = jQuery(idClass);
  var offset = element.offset();
  return {
    'top': offset.top,
    'right': offset.left + element.outerWidth(),
    'bottom': offset.top + element.outerHeight(),
    'left': offset.left
  };
};

function check_animation_classes(item) {
  var wintop = jQuery(window).scrollTop();
  var checkscrollbottom = wintop + jQuery(window).outerHeight();
  var parenttop = animationElementPosition(item).top;
  var parentbottom = animationElementPosition(item).bottom;
  var transformcss = '';

  if (jQuery(item).children('.sp-animation-translatey-up').length) {
    var default_value = 50;
    var default_speed = 1;
    default_speed = jQuery(item).children('.sp-animation-translatey-up').attr("data-vertical-scroll-speed");
    var animate_value = default_value * default_speed;
    var max_animate_value = -animate_value;
    var combineheight = parenttop + wintop; //console.log("checkscrollbottom" + checkscrollbottom + "parenttop" + parenttop + "wintop" + wintop + "combineheight" + combineheight);

    if (checkscrollbottom > combineheight) {
      //console.log("this is top one");
      animate_value = animate_value * .1;
      var differnce_val = wintop * (default_speed * .2);
      animate_value = animate_value - differnce_val;

      if (animate_value <= max_animate_value) {
        animate_value = max_animate_value;
      }
    } else {
      if (checkscrollbottom > parenttop) {
        var difference = (checkscrollbottom - parenttop) * (default_speed * .2); //console.log("variable value = " + difference);

        animate_value = animate_value - difference;

        if (animate_value <= max_animate_value) {
          animate_value = max_animate_value;
        }
      }
    }

    jQuery(item).children('.sp-animation-translatey-up').css("--translateY", animate_value + "px");
    transformcss = transformcss + " translateY(var(--translateY))";
  }

  if (jQuery(item).children('.sp-animation-translatey-down').length) {
    var _default_value = 50;
    var _default_speed = 1;
    _default_speed = jQuery(item).children('.sp-animation-translatey-down').attr("data-vertical-scroll-speed");

    var _animate_value = _default_value * _default_speed;

    var _max_animate_value = _animate_value;
    _animate_value = -_animate_value;

    var _combineheight = parenttop + wintop;

    if (checkscrollbottom > _combineheight) {
      //console.log("this is top one");
      _animate_value = _animate_value * .1;

      var _differnce_val = wintop * (_default_speed * .2);

      _animate_value = _animate_value + _differnce_val;

      if (_animate_value >= _max_animate_value) {
        _animate_value = _max_animate_value;
      }
    } else {
      if (checkscrollbottom > parenttop) {
        var difference = (checkscrollbottom - parenttop) * (_default_speed * .2); //console.log("variable value = " + difference);

        _animate_value = _animate_value + difference;

        if (_animate_value >= _max_animate_value) {
          _animate_value = _max_animate_value;
        }
      }
    }

    jQuery(item).children('.sp-animation-translatey-down').css("--translateY", _animate_value + "px");
    transformcss = transformcss + " translateY(var(--translateY))";
  }

  if (jQuery(item).children('.sp-animation-translatex-left').length) {
    var _default_value2 = 50;
    var _default_speed2 = 1;
    _default_speed2 = jQuery(item).children('.sp-animation-translatex-left').attr("data-horizotal-scroll-speed");

    var _animate_value2 = _default_value2 * _default_speed2;

    var _max_animate_value2 = -_animate_value2;

    var _combineheight2 = parenttop + wintop;

    if (checkscrollbottom > _combineheight2) {
      //console.log("this is top one");
      _animate_value2 = _animate_value2 * .1;

      var _differnce_val2 = wintop * (_default_speed2 * .125);

      _animate_value2 = _animate_value2 - _differnce_val2;

      if (_animate_value2 <= _max_animate_value2) {
        _animate_value2 = _max_animate_value2;
      }
    } else {
      if (checkscrollbottom > parenttop) {
        var difference = (checkscrollbottom - parenttop) * (_default_speed2 * .125); //console.log("variable value = " + difference);

        _animate_value2 = _animate_value2 - difference;

        if (_animate_value2 <= _max_animate_value2) {
          _animate_value2 = _max_animate_value2;
        }
      }
    }

    jQuery(item).children('.sp-animation-translatex-left').css("--translateX", _animate_value2 + "px");
    transformcss = transformcss + " translateX(var(--translateX))";
  }

  if (jQuery(item).children('.sp-animation-translatex-right').length) {
    var _default_value3 = 50;
    var _default_speed3 = 1;
    _default_speed3 = jQuery(item).children('.sp-animation-translatex-right').attr("data-horizotal-scroll-speed");

    var _animate_value3 = _default_value3 * _default_speed3;

    var _max_animate_value3 = _animate_value3;
    _animate_value3 = -_animate_value3;

    var _combineheight3 = parenttop + wintop;

    if (checkscrollbottom > _combineheight3) {
      //console.log("this is top one");
      _animate_value3 = _animate_value3 * .1;

      var _differnce_val3 = wintop * (_default_speed3 * .125);

      _animate_value3 = _animate_value3 + _differnce_val3;

      if (_animate_value3 >= _max_animate_value3) {
        _animate_value3 = _max_animate_value3;
      }
    } else {
      if (checkscrollbottom > parenttop) {
        var difference = (checkscrollbottom - parenttop) * (_default_speed3 * .125); //console.log("variable value = " + difference);

        _animate_value3 = _animate_value3 + difference;

        if (_animate_value3 >= _max_animate_value3) {
          _animate_value3 = _max_animate_value3;
        }
      }
    }

    jQuery(item).children('.sp-animation-translatex-right').css("--translateX", _animate_value3 + "px");
    transformcss = transformcss + " translateX(var(--translateX))";
  }

  if (jQuery(item).children('.sp-animation-transparency-fadein').length) {
    var _default_value4 = .1;
    var _default_speed4 = 1;
    _default_speed4 = jQuery(item).children('.sp-animation-transparency-fadein').attr("data-transparency-scroll-speed");

    var _animate_value4 = _default_value4 * (10 - _default_speed4);

    var start_val = _animate_value4;
    var _max_animate_value4 = 1;

    if (checkscrollbottom > parenttop) {
      var transparencyspeed = _default_speed4; //var difference = (checkscrollbottom - parenttop) * (start_val * .0035);

      var difference = (checkscrollbottom - parenttop) * (.000125 * transparencyspeed);
      _animate_value4 = _animate_value4 + difference;

      if (_animate_value4 >= _max_animate_value4) {
        _animate_value4 = _max_animate_value4;
      }
    }

    jQuery(item).children('.sp-animation-transparency-fadein').css("opacity", _animate_value4);
  }

  if (jQuery(item).children('.sp-animation-transparency-fadeout').length) {
    var _default_value5 = .1;
    var _default_speed5 = 1;
    _default_speed5 = jQuery(item).children('.sp-animation-transparency-fadeout').attr("data-transparency-scroll-speed");
    var _animate_value5 = 1;
    var _start_val = _animate_value5;

    var _max_animate_value5 = _default_value5 * (10 - _default_speed5); //default_value * default_speed;


    if (checkscrollbottom > parenttop) {
      var _transparencyspeed = _default_speed5; //var difference = (checkscrollbottom - parenttop) * (max_animate_value * .0050);

      var difference = (checkscrollbottom - parenttop) * (.000125 * _transparencyspeed);
      _animate_value5 = _animate_value5 - difference;

      if (_animate_value5 <= _max_animate_value5) {
        _animate_value5 = _max_animate_value5;
      }
    }

    jQuery(item).children('.sp-animation-transparency-fadeout').css("opacity", _animate_value5);
  }

  if (jQuery(item).children('.sp-animation-blur-fadein').length) {
    var _default_value6 = 1;
    var _default_speed6 = 1;
    _default_speed6 = jQuery(item).children('.sp-animation-blur-fadein').attr("data-blur-scroll-speed");

    var _animate_value6 = _default_value6 * _default_speed6;

    var _start_val2 = _animate_value6;
    var _max_animate_value6 = 0;

    if (checkscrollbottom > parenttop) {
      var difference = (checkscrollbottom - parenttop) * (_start_val2 * .0014);
      _animate_value6 = _animate_value6 - difference;

      if (_animate_value6 <= _max_animate_value6) {
        _animate_value6 = _max_animate_value6;
      }
    }

    jQuery(item).children('.sp-animation-blur-fadein').css("--blur", _animate_value6 + "px");
  }

  if (jQuery(item).children('.sp-animation-blur-fadeout').length) {
    var _default_value7 = 1;
    var _default_speed7 = 1;
    _default_speed7 = jQuery(item).children('.sp-animation-blur-fadeout').attr("data-blur-scroll-speed");
    var _animate_value7 = 0;

    var _max_animate_value7 = _default_value7 * _default_speed7;

    var _start_val3 = _max_animate_value7;

    if (checkscrollbottom > parenttop) {
      var difference = (checkscrollbottom - parenttop) * (_start_val3 * .0014);
      _animate_value7 = _animate_value7 + difference;

      if (_animate_value7 <= _max_animate_value7) {
        _animate_value7 = _max_animate_value7;
      }
    }

    jQuery(item).children('.sp-animation-blur-fadeout').css("--blur", _animate_value7 + "px");
  }

  if (jQuery(item).children('.sp-animation-rotate-toleft').length) {
    var _default_value8 = 50;
    var _default_speed8 = 1;
    _default_speed8 = jQuery(item).children('.sp-animation-rotate-toleft').attr("data-rotate-scroll-speed");

    var _animate_value8 = _default_value8 * _default_speed8;

    var _max_animate_value8 = -_animate_value8;

    if (checkscrollbottom > parenttop) {
      var difference = (checkscrollbottom - parenttop) * (_default_speed8 * 0.120); //console.log("variable value = " + difference);

      _animate_value8 = _animate_value8 - difference;

      if (_animate_value8 <= _max_animate_value8) {
        _animate_value8 = _max_animate_value8;
      }
    }

    jQuery(item).children('.sp-animation-rotate-toleft').css("--rotateZ", _animate_value8 + "deg");
    transformcss = transformcss + " rotateZ(var(--rotateZ))";
  }

  if (jQuery(item).children('.sp-animation-rotate-toright').length) {
    var _default_value9 = 50;
    var _default_speed9 = 1;
    _default_speed9 = jQuery(item).children('.sp-animation-rotate-toright').attr("data-rotate-scroll-speed");

    var _animate_value9 = _default_value9 * _default_speed9;

    var _max_animate_value9 = _animate_value9;
    _animate_value9 = -_animate_value9;

    if (checkscrollbottom > parenttop) {
      var difference = (checkscrollbottom - parenttop) * (_default_speed9 * 0.120); //console.log("variable value = " + difference);

      _animate_value9 = _animate_value9 + difference;

      if (_animate_value9 >= _max_animate_value9) {
        _animate_value9 = _max_animate_value9;
      }
    }

    jQuery(item).children('.sp-animation-rotate-toright').css("--rotateZ", _animate_value9 + "deg");
    transformcss = transformcss + " rotateZ(var(--rotateZ))";
  }

  if (jQuery(item).children('.sp-animation-scale-up').length) {
    var _default_value10 = 1;
    var _default_speed10 = 1;
    _default_speed10 = jQuery(item).children('.sp-animation-scale-up').attr("data-scale-scroll-speed");
    var _animate_value10 = _default_value10;

    var _max_animate_value10 = _default_value10 + _default_speed10 * .1;

    if (checkscrollbottom > parenttop) {
      var difference = (checkscrollbottom - parenttop) * (_default_speed10 * 0.00014); //console.log("variable value = " + difference);

      _animate_value10 = _animate_value10 + difference;

      if (_animate_value10 >= _max_animate_value10) {
        _animate_value10 = _max_animate_value10;
      }
    }

    jQuery(item).children('.sp-animation-scale-up').css("--scale", _animate_value10);
    transformcss = transformcss + " scale(var(--scale))";
  }

  if (jQuery(item).children('.sp-animation-scale-down').length) {
    var _default_value11 = 1;
    var _default_speed11 = 1;
    _default_speed11 = jQuery(item).children('.sp-animation-scale-down').attr("data-scale-scroll-speed");

    var _animate_value11 = _default_value11 + _default_speed11 * .1;

    var _max_animate_value11 = _default_value11;

    if (checkscrollbottom > parenttop) {
      var difference = (checkscrollbottom - parenttop) * (_default_speed11 * 0.00014); //console.log("variable value = " + difference);

      _animate_value11 = _animate_value11 - difference;

      if (_animate_value11 <= _max_animate_value11) {
        _animate_value11 = _max_animate_value11;
      }
    }

    jQuery(item).children('.sp-animation-scale-down').css("--scale", _animate_value11);
    transformcss = transformcss + " scale(var(--scale))";
  }
}

function check_mouse_animation_classes(item, event) {
  if (jQuery(item).children('.sp-mouse-track-opposite').length) {
    //window.addEventListener('mousemove', event => {
    var absMaxX = window.innerWidth / 2;
    var absMaxY = window.innerHeight / 2;
    var maxDistance = Math.sqrt(Math.pow(absMaxX, 2) + Math.pow(absMaxY, 2));
    var mouseX = event.clientX;
    var mouseY = event.clientY;
    var directionX = mouseX - absMaxX >= 0 ? -1 : 1;
    var directionY = mouseY - absMaxY >= 0 ? -1 : 1;
    var distance = Math.sqrt(Math.pow(mouseX - absMaxX, 2) + Math.pow(mouseY - absMaxY, 2));
    var translation = distance / maxDistance * 50;
    var distancex = Math.sqrt(Math.pow(mouseX - absMaxX, 2));
    var translationx = distancex / absMaxX * 50;
    var distancey = Math.sqrt(Math.pow(mouseY - absMaxY, 2));
    var translationy = distancey / absMaxY * 50;
    var default_speed = 5;
    default_speed = jQuery(item).children('.sp-mouse-track-opposite').attr("data-mouse-track-speed");
    var translationxspeed = directionX * translationx * default_speed;
    var translationyspeed = directionY * translationy * default_speed; //console.log("translationxspeed" + translationxspeed + "translationyspeed" + translationyspeed);

    var transformval = jQuery(item).children('.sp-mouse-track-opposite').get(0).style.transform;

    if (jQuery(item).children('.sp-mouse-track-opposite').get(0).style.transform) {
      if (transformval.indexOf('translateX') == -1) {
        transformval = transformval + " translateX(var(--translateX)) ";
      }

      if (transformval.indexOf('translateY') == -1) {
        transformval = transformval + " translateY(var(--translateY)) ";
      }
    } else {
      transformval = " translateX(var(--translateX))   translateY(var(--translateY)) ";
    }

    jQuery(item).children('.sp-mouse-track-opposite').css("--translateX", "".concat(translationxspeed, "px"));
    jQuery(item).children('.sp-mouse-track-opposite').css("--translateY", "".concat(translationyspeed, "px")); //console.log(transformval);

    jQuery(item).children('.sp-mouse-track-opposite').css("transform", transformval); //});
  }

  if (jQuery(item).children('.sp-mouse-track-direct').length) {
    //window.addEventListener('mousemove', event => {
    var _absMaxX = window.innerWidth / 2;

    var _absMaxY = window.innerHeight / 2;

    var _maxDistance = Math.sqrt(Math.pow(_absMaxX, 2) + Math.pow(_absMaxY, 2));

    var _mouseX = event.clientX;
    var _mouseY = event.clientY;

    var _directionX = _mouseX - _absMaxX >= 0 ? 1 : -1;

    var _directionY = _mouseY - _absMaxY >= 0 ? 1 : -1;

    var _distance = Math.sqrt(Math.pow(_mouseX - _absMaxX, 2) + Math.pow(_mouseY - _absMaxY, 2));

    var _translation = _distance / _maxDistance * 50;

    var _distancex = Math.sqrt(Math.pow(_mouseX - _absMaxX, 2));

    var _translationx = _distancex / _absMaxX * 50;

    var _distancey = Math.sqrt(Math.pow(_mouseY - _absMaxY, 2));

    var _translationy = _distancey / _absMaxY * 50;

    var _default_speed12 = 1;
    _default_speed12 = jQuery(item).children('.sp-mouse-track-direct').attr("data-mouse-track-speed");

    var _translationxspeed = _directionX * _translationx * _default_speed12;

    var _translationyspeed = _directionY * _translationy * _default_speed12; //console.log("translationxspeed" + translationxspeed + "translationyspeed" + translationyspeed);


    var _transformval = jQuery(item).children('.sp-mouse-track-direct').get(0).style.transform;

    if (jQuery(item).children('.sp-mouse-track-direct').get(0).style.transform) {
      if (_transformval.indexOf('translateX') == -1) {
        _transformval = _transformval + " translateX(var(--translateX)) ";
      }

      if (_transformval.indexOf('translateY') == -1) {
        _transformval = _transformval + " translateY(var(--translateY)) ";
      }
    } else {
      _transformval = " translateX(var(--translateX))   translateY(var(--translateY)) ";
    }
    /*
    if (false === transformval.includes("translateX(") && false === transformval.includes("translateX(")) {
    	transformval = transformval + ` translateX(var(--translateX))   translateY(var(--translateY)) `;
    	//console.log("if");
    } else if (false === transformval.includes("translateX(")) {
    	transformval = transformval + ` translateX(var(--translateX)) `;
    	//jQuery(item).children('.sp-mouse-track-opposite').css("--translateX", `${translationxspeed}px`);
    	//console.log("else if");
    } else if (false === transformval.includes("translateY(")) {
    	transformval = transformval + `  translateY(var(--translateY)) `;
    	//jQuery(item).children('.sp-mouse-track-opposite').css("--translateY", `${translationyspeed}px`);
    	//console.log("else");
    }*/


    jQuery(item).children('.sp-mouse-track-direct').css("--translateX", "".concat(_translationxspeed, "px"));
    jQuery(item).children('.sp-mouse-track-direct').css("--translateY", "".concat(_translationyspeed, "px")); //console.log(transformval);

    jQuery(item).children('.sp-mouse-track-direct').css("transform", _transformval); //jQuery(item).children('.sp-mouse-track-direct').css("transform", `translateX(-${directionX * translationx}px) translateY(-${directionY * translationy}px)`);
    //});
  }

  if (jQuery(item).children('.sp-mouse3d-opposite').length) {
    //window.addEventListener('mousemove', event => {
    var _absMaxX2 = window.innerWidth / 2;

    var _absMaxY2 = window.innerHeight / 2;

    var _maxDistance2 = Math.sqrt(Math.pow(_absMaxX2, 2) + Math.pow(_absMaxY2, 2));

    var _mouseX2 = event.clientX;
    var _mouseY2 = event.clientY;

    var _directionX2 = _mouseX2 - _absMaxX2 >= 0 ? -1 : 1;

    var _directionY2 = _mouseY2 - _absMaxY2 >= 0 ? 1 : -1;

    var _distance2 = Math.sqrt(Math.pow(_mouseX2 - _absMaxX2, 2) + Math.pow(_mouseY2 - _absMaxY2, 2));

    var _translation2 = _distance2 / _maxDistance2 * 5;

    var _distancex2 = Math.sqrt(Math.pow(_mouseX2 - _absMaxX2, 2));

    var _translationx2 = _distancex2 / _absMaxX2 * 5;

    var _distancey2 = Math.sqrt(Math.pow(_mouseY2 - _absMaxY2, 2));

    var _translationy2 = _distancey2 / _absMaxY2 * 5;

    var _default_speed13 = 1;
    _default_speed13 = jQuery(item).children('.sp-mouse3d-opposite').attr("data-mouse3d-track-speed");
    var rotatexspeed = _directionX2 * _translationx2 * _default_speed13;
    var rotateyspeed = _directionY2 * _translationy2 * _default_speed13;
    var _transformval2 = jQuery(item).children('.sp-mouse3d-opposite').get(0).style.transform;

    if (jQuery(item).children('.sp-mouse3d-opposite').get(0).style.transform) {
      if (_transformval2.indexOf('rotateX') == -1) {
        _transformval2 = _transformval2 + " rotateX(var(--rotateX)) ";
      }

      if (_transformval2.indexOf('rotateY') == -1) {
        _transformval2 = _transformval2 + " rotateY(var(--rotateY)) ";
      }
    } else {
      _transformval2 = " rotateX(var(--rotateX)) rotateY(var(--rotateY)) ";
    }
    /*
    if (false === transformval.includes("rotateX(") && false === transformval.includes("rotateY(")) {
    	transformval = transformval + ` rotateX(var(--rotateX)) rotateY(var(--rotateY)) `;
    } else if (false === transformval.includes("rotateX(")) {
    	transformval = transformval + ` rotateX(var(--rotateX)) `;
    } else if (false === transformval.includes("rotateY(")) {
    	transformval = transformval + `  rotateY(var(--rotateY)) `;
    }*/


    jQuery(item).children('.sp-mouse3d-opposite').css("--rotateX", "".concat(rotateyspeed, "deg"));
    jQuery(item).children('.sp-mouse3d-opposite').css("--rotateY", "".concat(rotatexspeed, "deg"));
    jQuery(item).children('.sp-mouse3d-opposite').css("transform", _transformval2); //jQuery(item).children('.sp-mouse-track-direct').css("transform", `translateX(-${directionX * translationx}px) translateY(-${directionY * translationy}px)`);
    //});

    /*
    const card = document.querySelector("body");
    	const tiltEffectSettings = {
    	reverse: true,
    	max: 50, // max tilt rotation (degrees (deg))
    	perspective: 1000, // transform perspective, the lower the more extreme the tilt gets (pixels (px))
    	scale: 1, // transform scale - 2 = 200%, 1.5 = 150%, etc..
    	speed: 500, // speed (transition-duration) of the enter/exit transition (milliseconds (ms))
    	easing: "cubic-bezier(.03,.98,.52,.99)" // easing (transition-timing-function) of the enter/exit transition
    };
    	window.addEventListener('mousemove', event => {
    		const cardWidth = card.offsetWidth;
    	const cardHeight = card.offsetHeight;
    	const centerX = card.offsetLeft + cardWidth / 2;
    	const centerY = card.offsetTop + cardHeight / 2;
    	const mouseX = event.clientX - centerX;
    	const mouseY = event.clientY - centerY;
    	const mouseReverse = tiltEffectSettings.reverse ? -1 : 1;
    	const mouseReverseNeg = -(mouseReverse);
    		const rotateXUncapped = (mouseReverse) * tiltEffectSettings.max * mouseY / (cardHeight / 2);
    	const rotateYUncapped = (mouseReverseNeg) * tiltEffectSettings.max * mouseX / (cardWidth / 2);
    	const rotateX = rotateXUncapped < -tiltEffectSettings.max ? -tiltEffectSettings.max :
    		(rotateXUncapped > tiltEffectSettings.max ? tiltEffectSettings.max : rotateXUncapped);
    	const rotateY = rotateYUncapped < -tiltEffectSettings.max ? -tiltEffectSettings.max :
    		(rotateYUncapped > tiltEffectSettings.max ? tiltEffectSettings.max : rotateYUncapped);
    			jQuery(item).children('.sp-mouse3d-opposite').css("--rotateX", `${rotateX}deg`);
    	jQuery(item).children('.sp-mouse3d-opposite').css("--rotateY", `${rotateX}deg`);
    		//rotateY
    	jQuery(item).children('.sp-mouse3d-opposite').css("transform", `perspective(${tiltEffectSettings.perspective}px) rotateX(var(--rotateX)) rotateY(var(--rotateY))
    				  scale3d(${tiltEffectSettings.scale}, ${tiltEffectSettings.scale}, ${tiltEffectSettings.scale})`);
    	});
    */
  }

  if (jQuery(item).children('.sp-mouse3d-direct').length) {
    //window.addEventListener('mousemove', event => {
    var _absMaxX3 = window.innerWidth / 2;

    var _absMaxY3 = window.innerHeight / 2;

    var _maxDistance3 = Math.sqrt(Math.pow(_absMaxX3, 2) + Math.pow(_absMaxY3, 2));

    var _mouseX3 = event.clientX;
    var _mouseY3 = event.clientY;

    var _directionX3 = _mouseX3 - _absMaxX3 >= 0 ? -1 : 1;

    var _directionY3 = _mouseY3 - _absMaxY3 >= 0 ? 1 : -1;

    var _distance3 = Math.sqrt(Math.pow(_mouseX3 - _absMaxX3, 2) + Math.pow(_mouseY3 - _absMaxY3, 2));

    var _translation3 = _distance3 / _maxDistance3 * 5;

    var _distancex3 = Math.sqrt(Math.pow(_mouseX3 - _absMaxX3, 2));

    var _translationx3 = _distancex3 / _absMaxX3 * 5;

    var _distancey3 = Math.sqrt(Math.pow(_mouseY3 - _absMaxY3, 2));

    var _translationy3 = _distancey3 / _absMaxY3 * 5;

    var _default_speed14 = 1;
    _default_speed14 = jQuery(item).children('.sp-mouse3d-direct').attr("data-mouse3d-track-speed");

    var _rotatexspeed = _directionX3 * _translationx3 * _default_speed14;

    var _rotateyspeed = _directionY3 * _translationy3 * _default_speed14;

    var _transformval3 = jQuery(item).children('.sp-mouse3d-direct').get(0).style.transform;

    if (jQuery(item).children('.sp-mouse3d-direct').get(0).style.transform) {
      if (_transformval3.indexOf('rotateX') == -1) {
        _transformval3 = _transformval3 + " rotateX(var(--rotateX)) ";
      }

      if (_transformval3.indexOf('rotateY') == -1) {
        _transformval3 = _transformval3 + " rotateY(var(--rotateY)) ";
      }
    } else {
      _transformval3 = " rotateX(var(--rotateX)) rotateY(var(--rotateY)) ";
    }
    /*
    if (false === transformval.includes("rotateX(") && false === transformval.includes("rotateY(")) {
    	transformval = transformval + ` rotateX(var(--rotateX)) rotateY(var(--rotateY)) `;
    } else if (false === transformval.includes("rotateX(")) {
    	transformval = transformval + ` rotateX(var(--rotateX)) `;
    } else if (false === transformval.includes("rotateY(")) {
    	transformval = transformval + `  rotateY(var(--rotateY)) `;
    } 
    */


    jQuery(item).children('.sp-mouse3d-direct').css("--rotateX", "".concat(_rotatexspeed, "deg"));
    jQuery(item).children('.sp-mouse3d-direct').css("--rotateY", "".concat(_rotateyspeed, "deg"));
    jQuery(item).children('.sp-mouse3d-direct').css("transform", _transformval3); //jQuery(item).children('.sp-mouse-track-direct').css("transform", `translateX(-${directionX * translationx}px) translateY(-${directionY * translationy}px)`);
    //});

    /*
    const card = document.querySelector("body");
    const tiltEffectSettings = {
    	reverse: true,
    	max: 50, // max tilt rotation (degrees (deg))
    	perspective: 1000, // transform perspective, the lower the more extreme the tilt gets (pixels (px))
    	scale: 1, // transform scale - 2 = 200%, 1.5 = 150%, etc..
    	speed: 500, // speed (transition-duration) of the enter/exit transition (milliseconds (ms))
    	easing: "cubic-bezier(.03,.98,.52,.99)" // easing (transition-timing-function) of the enter/exit transition
    };
    	window.addEventListener('mousemove', event => {
    		const cardWidth = card.offsetWidth;
    	const cardHeight = card.offsetHeight;
    	const centerX = card.offsetLeft + cardWidth / 2;
    	const centerY = card.offsetTop + cardHeight / 2;
    	const mouseX = event.clientX - centerX;
    	const mouseY = event.clientY - centerY;
    	const mouseReverse = tiltEffectSettings.reverse ? -1 : 1;
    	const mouseReverseNeg = -(mouseReverse);
    		const rotateXUncapped = (mouseReverse) * tiltEffectSettings.max * mouseY / (cardHeight / 2);
    	const rotateYUncapped = (mouseReverse) * tiltEffectSettings.max * mouseX / (cardWidth / 2);
    	const rotateX = rotateXUncapped < -tiltEffectSettings.max ? -tiltEffectSettings.max :
    		(rotateXUncapped > tiltEffectSettings.max ? tiltEffectSettings.max : rotateXUncapped);
    	const rotateY = rotateYUncapped < -tiltEffectSettings.max ? -tiltEffectSettings.max :
    		(rotateYUncapped > tiltEffectSettings.max ? tiltEffectSettings.max : rotateYUncapped);
    		jQuery(item).children('.sp-mouse3d-direct').css("transform", `perspective(${tiltEffectSettings.perspective}px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) 
    				  scale3d(${tiltEffectSettings.scale}, ${tiltEffectSettings.scale}, ${tiltEffectSettings.scale})`);
    	});
    */
  }
}

function animation_mouse_tranform(item) {
  var transformcss = '';

  if (jQuery(item).children('.sp-animation-translatey-up').length) {
    transformcss = transformcss + " translateY(var(--translateY))";
  }

  if (jQuery(item).children('.sp-animation-translatey-down').length) {
    transformcss = transformcss + " translateY(var(--translateY))";
  }

  if (jQuery(item).children('.sp-animation-translatex-left').length) {
    transformcss = transformcss + " translateX(var(--translateX))";
  }

  if (jQuery(item).children('.sp-animation-translatex-right').length) {
    transformcss = transformcss + " translateX(var(--translateX))";
  }

  if (jQuery(item).children('.sp-animation-rotate-toleft').length) {
    transformcss = transformcss + " rotateZ(var(--rotateZ))";
  }

  if (jQuery(item).children('.sp-animation-rotate-toright').length) {
    transformcss = transformcss + " rotateZ(var(--rotateZ))";
  }

  if (jQuery(item).children('.sp-animation-scale-up').length) {
    transformcss = transformcss + " scale(var(--scale))";
  }

  if (jQuery(item).children('.sp-animation-scale-down').length) {
    transformcss = transformcss + " scale(var(--scale))";
  }

  jQuery(item).children('.sp-animation-effects').css("transform", "".concat(transformcss));
  var transformtransparencycss = '';

  if (jQuery(item).children('.sp-animation-transparency-fadein').length) {
    transformtransparencycss = transformtransparencycss + "opacity";
  }

  if (jQuery(item).children('.sp-animation-transparency-fadeout').length) {
    transformtransparencycss = transformtransparencycss + "opacity";
  } //if (transformtransparencycss != '') {


  jQuery(item).children('.sp-animation-effects').css("will-change", "".concat(transformtransparencycss)); //}

  var transformblurcss = '';

  if (jQuery(item).children('.sp-animation-blur-fadein').length) {
    transformblurcss = transformblurcss + " blur(var(--blur))";
  }

  if (jQuery(item).children('.sp-animation-blur-fadeout').length) {
    transformblurcss = transformblurcss + " blur(var(--blur))";
  }

  jQuery(item).children('.sp-animation-effects').css("filter", "".concat(transformblurcss));
  return transformcss;
}

function animation_mouseover_tranform(item) {
  var transformcss = '';

  if (jQuery(item).children('.sp-mouse-track-opposite').length) {
    transformcss = transformcss + " scale(var(--scale))"; //jQuery(item).children('.sp-mouse-track-direct').css("transform", `translateX(-${directionX * translationx}px) translateY(-${directionY * translationy}px)`);
  }

  if (jQuery(item).children('.sp-mouse-track-direct').length) {}

  if (jQuery(item).children('.sp-mouse3d-direct').length) {}

  if (jQuery(item).children('.sp-mouse3d-opposite').length) {}

  return transformcss;
}

function check_entrance_animation_classes(item) {
  var wintop = jQuery(window).scrollTop();
  var checkscrollbottom = wintop + jQuery(window).outerHeight();
  var parenttop = animationElementPosition(item).top;
  var parentbottom = animationElementPosition(item).bottom;
  var transformcss = ''; //if (jQuery(item).hasClass('sp-entrace-animation')) {

  if (checkscrollbottom > parenttop) {
    var default_animation = jQuery(item).attr("data-entrance-animation");
    jQuery(item).addClass('sp-animation-time');
    jQuery(item).addClass(default_animation);
  } //}

}

function entrance_animation_effects() {
  var entranceanimationLoadParents = document.querySelectorAll(".sp-entrace-animation");

  for (var i = 0; i < entranceanimationLoadParents.length; i++) {
    var entranceitemload = entranceanimationLoadParents[i];
    check_entrance_animation_classes(entranceitemload);
  }
}

jQuery(document).ready(function ($) {
  var animationLoadParents = document.querySelectorAll(".sp-animation-parent");

  for (var i = 0; i < animationLoadParents.length; i++) {
    var itemload = animationLoadParents[i];
    check_animation_classes(itemload);
  }

  entrance_animation_effects();
  jQuery(window).scroll(function () {
    var wintop = jQuery(window).scrollTop();
    var checkscrollbottom = wintop + jQuery(window).outerHeight();
    var checkelem;
    var animationParents = document.querySelectorAll(".sp-animation-parent");

    for (var _i = 0; _i < animationParents.length; _i++) {
      var item = animationParents[_i];
      check_animation_classes(item);
    }

    entrance_animation_effects();
  });
  var animationParentsCss = document.querySelectorAll(".sp-animation-parent");

  for (var _i2 = 0; _i2 < animationParentsCss.length; _i2++) {
    var item = animationParentsCss[_i2];
    var mouse_css = animation_mouse_tranform(item); //jQuery(item).children('.sp-animation-effects').css("transform", `${mouse_css}`);
  }

  var animationParents = document.querySelectorAll(".sp-mouse-parent");

  if (animationParents.length >= 1) {
    window.addEventListener('mousemove', function (event) {
      for (var _i3 = 0; _i3 < animationParents.length; _i3++) {
        var _item = animationParents[_i3];
        check_mouse_animation_classes(_item, event);
      }
    });
  } //sp-animation-time

});