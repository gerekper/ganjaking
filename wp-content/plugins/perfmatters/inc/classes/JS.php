<?php
namespace Perfmatters;

class JS
{
	//initialize js
	public static function init()
	{
		$defer_check = !empty(apply_filters('perfmatters_defer_js', !empty(Config::$options['assets']['defer_js'])));
		$delay_check = !empty(apply_filters('perfmatters_delay_js', !empty(Config::$options['assets']['delay_js'])));

		if($defer_check || $delay_check) {

			//actions + filters
			add_filter('perfmatters_output_buffer_template_redirect', array('Perfmatters\JS', 'optimize'));
		}

		if(!empty(Config::$options['assets']['fastclick'])) {
			add_filter('wp_head', array('Perfmatters\JS', 'print_fastclick'));
		}
	}

	//add defer tag to js files in html
	public static function optimize($html) {

		//strip comments before search
		$html_no_comments = preg_replace('/<!--(.*)-->/Uis', '', $html);

		//match all script tags
		preg_match_all('#(<script\s?([^>]+)?\/?>)(.*?)<\/script>#is', $html_no_comments, $matches);

		//no script tags found
		if(!isset($matches[0])) {
			return $html;
		}

		$defer_check = !empty(apply_filters('perfmatters_defer_js', !empty(Config::$options['assets']['defer_js']))) && !Utilities::get_post_meta('perfmatters_exclude_defer_js');
		$delay_check = !empty(apply_filters('perfmatters_delay_js', !empty(Config::$options['assets']['delay_js']))) && !Utilities::get_post_meta('perfmatters_exclude_delay_js');

		//build js exlusions array
		$js_exclusions = array();

		if($defer_check) {

			//add jquery if needed
			if(empty(Config::$options['assets']['defer_jquery'])) {
				array_push($js_exclusions, 'jquery(?:\.min)?.js');
			}

			//add extra user exclusions
			if(!empty(Config::$options['assets']['js_exclusions']) && is_array(Config::$options['assets']['js_exclusions'])) {
				foreach(Config::$options['assets']['js_exclusions'] as $line) {
					array_push($js_exclusions, preg_quote($line));
				}
			}

			//convert exlusions to string for regex
			$js_exclusions = implode('|', $js_exclusions);
		}

		//loop through scripts
		foreach($matches[0] as $i => $tag) {

			$atts_array = !empty($matches[2][$i]) ? Utilities::get_atts_array($matches[2][$i]) : array();
			
			//skip if type is not javascript
			if(isset($atts_array['type']) && stripos($atts_array['type'], 'javascript') == false) {
				continue;
			}

			//delay javascript
			if($delay_check) {

				$delay_flag = false;

				$delay_js_behavior = apply_filters('perfmatters_delay_js_behavior', Config::$options['assets']['delay_js_behavior'] ?? '');

				if(empty($delay_js_behavior)) {

					$delayed_scripts = apply_filters('perfmatters_delayed_scripts', Config::$options['assets']['delay_js_inclusions']);

					if(!empty($delayed_scripts)) {
						foreach($delayed_scripts as $delayed_script) {
							if(strpos($tag, $delayed_script) !== false) {

								$delay_flag = true;
				
								if(!empty($atts_array['src'])) {
				                	$atts_array['data-pmdelayedscript'] = $atts_array['src'];
				                	unset($atts_array['src']);
				    			}
				    			else {
				    				$atts_array['data-pmdelayedscript'] = "data:text/javascript;base64," . base64_encode($matches[3][$i]);
				    			}
							}
						}
					}
				}
				else {

					$excluded_scripts = array(
						'perfmatters-delayed-scripts-js',
						'lazyload',
						'lazyLoadInstance',
						'lazysizes',
						'customize-support',
						'fastclick'
					);

					if(!empty(Config::$options['assets']['delay_js_exclusions']) && is_array(Config::$options['assets']['delay_js_exclusions'])) {
						$excluded_scripts = array_merge($excluded_scripts, Config::$options['assets']['delay_js_exclusions']);
					}

					$excluded_scripts = apply_filters('perfmatters_delay_js_exclusions', $excluded_scripts);

					if(!empty($excluded_scripts)) {
						foreach($excluded_scripts as $excluded_script) {
							if(strpos($tag, $excluded_script) !== false) {
								continue 2;
							}
						}
					}

					$delay_flag = true;

					if(!empty($atts_array['type'])) {
	    				$atts_array['data-perfmatters-type'] = $atts_array['type'];
	    			}

	    			$atts_array['type'] = 'pmdelayedscript';
				}

				if($delay_flag) {

	    			$atts_array['data-cfasync'] = "false";
	    			$atts_array['data-no-optimize'] = "1";
	    			$atts_array['data-no-defer'] = "1";
	    			$atts_array['data-no-minify'] = "1";

	    			//wp rocket compatability
					if(defined('WP_ROCKET_VERSION')) {
						$atts_array['data-rocketlazyloadscript'] = "1";
					}

	    			$delayed_atts_string = Utilities::get_atts_string($atts_array);
	                $delayed_tag = sprintf('<script %1$s>', $delayed_atts_string) . (!empty($delay_js_behavior) ? $matches[3][$i] : '') . '</script>';

	    			//replace new full tag in html
					$html = str_replace($tag, $delayed_tag, $html);

					continue;
				}
			}

			//defer javascript
			if($defer_check) {

				//src is not set
				if(empty($atts_array['src'])) {
					continue;
				}

				//check if src is excluded
				if(!empty($js_exclusions) && preg_match('#(' . $js_exclusions . ')#i', $atts_array['src'])) {
					continue;
				}

				//skip if there is already an async
				if(stripos($matches[2][$i], 'async') !== false) {
					continue;
				}

				//skip if there is already a defer
				if(stripos($matches[2][$i], 'defer' ) !== false ) {
					continue;
				}

				//add defer to opening tag
				$deferred_tag_open = str_replace('>', ' defer>', $matches[1][$i]);

				//replace new open tag in original full tag
				$deferred_tag = str_replace($matches[1][$i], $deferred_tag_open, $tag);

				//replace new full tag in html
				$html = str_replace($tag, $deferred_tag, $html);
			}
		}

		if($delay_check) {
                $html = str_replace('</body>', self::print_delay_js() . '</body>', $html);
		}

		return $html;
	}

	//print inline delay js
	public static function print_delay_js() {

		$timeout = apply_filters('perfmatters_delay_js_timeout', !empty(Config::$options['assets']['delay_timeout']) ? 10 : '');

	  	if(!empty(apply_filters('perfmatters_delay_js', !empty(Config::$options['assets']['delay_js'])))) {
	  		
	  		if(empty(apply_filters('perfmatters_delay_js_behavior', Config::$options['assets']['delay_js_behavior'] ?? ''))) {
	  			return '<script type="text/javascript" id="perfmatters-delayed-scripts-js">' . (!empty($timeout) ? 'const perfmattersDelayTimer = setTimeout(pmLoadDelayedScripts,' . $timeout . '*1000);' : '') . 'const perfmattersUserInteractions=["keydown","mousemove","wheel","touchmove","touchstart","touchend"];perfmattersUserInteractions.forEach(function(event){window.addEventListener(event,pmTriggerDelayedScripts,{passive:!0})});function pmTriggerDelayedScripts(){pmLoadDelayedScripts();' . (!empty($timeout) ? 'clearTimeout(perfmattersDelayTimer);' : '') . 'perfmattersUserInteractions.forEach(function(event){window.removeEventListener(event, pmTriggerDelayedScripts,{passive:!0});});}function pmLoadDelayedScripts(){document.querySelectorAll("script[data-pmdelayedscript]").forEach(function(elem){elem.setAttribute("src",elem.getAttribute("data-pmdelayedscript"));});}</script>';
	  		}
	  		else {
	  			$script = '<script type="text/javascript" id="perfmatters-delayed-scripts-js">';
	  				$script.= 'const pmDelayClick=' . json_encode(apply_filters('perfmatters_delay_js_delay_click', empty(Config::$options['assets']['disable_click_delay']))) . ';';
		  			if(!empty($timeout)) {
		  				$script.= 'const pmDelayTimer=setTimeout(pmTriggerDOMListener,' . $timeout . '*1000);';
		  			}  
		  			$script.= 'const pmUserInteractions=["keydown","mousedown","mousemove","wheel","touchmove","touchstart","touchend"],pmDelayedScripts={normal:[],defer:[],async:[]},jQueriesArray=[],pmInterceptedClicks=[];var pmDOMLoaded=!1,pmClickTarget="";function pmTriggerDOMListener(){"undefined"!=typeof pmDelayTimer&&clearTimeout(pmDelayTimer),pmUserInteractions.forEach(function(a){window.removeEventListener(a,pmTriggerDOMListener,{passive:!0})}),document.removeEventListener("visibilitychange",pmTriggerDOMListener),"loading"===document.readyState?document.addEventListener("DOMContentLoaded",pmTriggerDelayedScripts):pmTriggerDelayedScripts()}async function pmTriggerDelayedScripts(){pmDelayEventListeners(),pmDelayJQueryReady(),pmProcessDocumentWrite(),pmSortDelayedScripts(),pmPreloadDelayedScripts(),await pmLoadDelayedScripts(pmDelayedScripts.normal),await pmLoadDelayedScripts(pmDelayedScripts.defer),await pmLoadDelayedScripts(pmDelayedScripts.async),await pmTriggerEventListeners(),document.querySelectorAll("link[data-pmdelayedstyle]").forEach(function(a){a.setAttribute("href",a.getAttribute("data-pmdelayedstyle"))}),window.dispatchEvent(new Event("perfmatters-allScriptsLoaded")),pmReplayClicks()}function pmDelayEventListeners(){let c={};function a(a,b){function d(b){return c[a].delayedEvents.indexOf(b)>=0?"perfmatters-"+b:b}c[a]||(c[a]={originalFunctions:{add:a.addEventListener,remove:a.removeEventListener},delayedEvents:[]},a.addEventListener=function(){arguments[0]=d(arguments[0]),c[a].originalFunctions.add.apply(a,arguments)},a.removeEventListener=function(){arguments[0]=d(arguments[0]),c[a].originalFunctions.remove.apply(a,arguments)}),c[a].delayedEvents.push(b)}function b(a,b){let c=a[b];Object.defineProperty(a,b,{get:c||function(){},set:function(c){a["perfmatters"+b]=c}})}a(document,"DOMContentLoaded"),a(window,"DOMContentLoaded"),a(window,"load"),a(window,"pageshow"),a(document,"readystatechange"),b(document,"onreadystatechange"),b(window,"onload"),b(window,"onpageshow")}function pmDelayJQueryReady(){let a=window.jQuery;Object.defineProperty(window,"jQuery",{get:()=>a,set(b){if(b&&b.fn&&!jQueriesArray.includes(b)){b.fn.ready=b.fn.init.prototype.ready=function(a){pmDOMLoaded?a.bind(document)(b):document.addEventListener("perfmatters-DOMContentLoaded",function(){a.bind(document)(b)})};let c=b.fn.on;b.fn.on=b.fn.init.prototype.on=function(){if(this[0]===window){function a(a){return(a=a.split(" ")).map(function(a){return"load"===a||0===a.indexOf("load.")?"perfmatters-jquery-load":a}),a=a.join(" ")}"string"==typeof arguments[0]||arguments[0]instanceof String?arguments[0]=a(arguments[0]):"object"==typeof arguments[0]&&Object.keys(arguments[0]).forEach(function(b){delete Object.assign(arguments[0],{[a(b)]:arguments[0][b]})[b]})}return c.apply(this,arguments),this},jQueriesArray.push(b)}a=b}})}function pmProcessDocumentWrite(){let a=new Map;document.write=document.writeln=function(f){var b=document.currentScript,e=document.createRange();let c=a.get(b);void 0===c&&(c=b.nextSibling,a.set(b,c));var d=document.createDocumentFragment();e.setStart(d,0),d.appendChild(e.createContextualFragment(f)),b.parentElement.insertBefore(d,c)}}function pmSortDelayedScripts(){document.querySelectorAll("script[type=pmdelayedscript]").forEach(function(a){a.hasAttribute("src")?a.hasAttribute("defer")&& !1!==a.defer?pmDelayedScripts.defer.push(a):a.hasAttribute("async")&& !1!==a.async?pmDelayedScripts.async.push(a):pmDelayedScripts.normal.push(a):pmDelayedScripts.normal.push(a)})}function pmPreloadDelayedScripts(){var a=document.createDocumentFragment();[...pmDelayedScripts.normal,...pmDelayedScripts.defer,...pmDelayedScripts.async].forEach(function(d){var c=d.getAttribute("src");if(c){var b=document.createElement("link");b.href=c,b.rel="preload",b.as="script",a.appendChild(b)}}),document.head.appendChild(a)}async function pmLoadDelayedScripts(a){var b=a.shift();return b?(await pmReplaceScript(b),pmLoadDelayedScripts(a)):Promise.resolve()}async function pmReplaceScript(a){return await pmNextFrame(),new Promise(function(c){let b=document.createElement("script");[...a.attributes].forEach(function(c){let a=c.nodeName;"type"!==a&&("data-type"===a&&(a="type"),b.setAttribute(a,c.nodeValue))}),a.hasAttribute("src")?(b.addEventListener("load",c),b.addEventListener("error",c)):(b.text=a.text,c()),a.parentNode.replaceChild(b,a)})}async function pmTriggerEventListeners(){pmDOMLoaded=!0,await pmNextFrame(),document.dispatchEvent(new Event("perfmatters-DOMContentLoaded")),await pmNextFrame(),window.dispatchEvent(new Event("perfmatters-DOMContentLoaded")),await pmNextFrame(),document.dispatchEvent(new Event("perfmatters-readystatechange")),await pmNextFrame(),document.perfmattersonreadystatechange&&document.perfmattersonreadystatechange(),await pmNextFrame(),window.dispatchEvent(new Event("perfmatters-load")),await pmNextFrame(),window.perfmattersonload&&window.perfmattersonload(),await pmNextFrame(),jQueriesArray.forEach(function(a){a(window).trigger("perfmatters-jquery-load")});let a=new Event("perfmatters-pageshow");a.persisted=window.pmPersisted,window.dispatchEvent(a),await pmNextFrame(),window.perfmattersonpageshow&&window.perfmattersonpageshow({persisted:window.pmPersisted})}async function pmNextFrame(){return new Promise(function(a){requestAnimationFrame(a)})}function pmClickHandler(a){a.target.removeEventListener("click",pmClickHandler),pmRenameDOMAttribute(a.target,"pm-onclick","onclick"),pmInterceptedClicks.push(a),a.preventDefault(),a.stopPropagation(),a.stopImmediatePropagation()}function pmReplayClicks(){window.removeEventListener("touchstart",pmTouchStartHandler,{passive:!0}),window.removeEventListener("mousedown",pmTouchStartHandler),pmInterceptedClicks.forEach(a=>{a.target.outerHTML===pmClickTarget&&a.target.dispatchEvent(new MouseEvent("click",{view:a.view,bubbles:!0,cancelable:!0}))})}function pmTouchStartHandler(a){"HTML"!==a.target.tagName&&(pmClickTarget||(pmClickTarget=a.target.outerHTML),window.addEventListener("touchend",pmTouchEndHandler),window.addEventListener("mouseup",pmTouchEndHandler),window.addEventListener("touchmove",pmTouchMoveHandler,{passive:!0}),window.addEventListener("mousemove",pmTouchMoveHandler),a.target.addEventListener("click",pmClickHandler),pmRenameDOMAttribute(a.target,"onclick","pm-onclick"))}function pmTouchMoveHandler(a){window.removeEventListener("touchend",pmTouchEndHandler),window.removeEventListener("mouseup",pmTouchEndHandler),window.removeEventListener("touchmove",pmTouchMoveHandler,{passive:!0}),window.removeEventListener("mousemove",pmTouchMoveHandler),a.target.removeEventListener("click",pmClickHandler),pmRenameDOMAttribute(a.target,"pm-onclick","onclick")}function pmTouchEndHandler(a){window.removeEventListener("touchend",pmTouchEndHandler),window.removeEventListener("mouseup",pmTouchEndHandler),window.removeEventListener("touchmove",pmTouchMoveHandler,{passive:!0}),window.removeEventListener("mousemove",pmTouchMoveHandler)}function pmRenameDOMAttribute(b,a,c){b.hasAttribute&&b.hasAttribute(a)&&(event.target.setAttribute(c,event.target.getAttribute(a)),event.target.removeAttribute(a))}window.addEventListener("pageshow",a=>{window.pmPersisted=a.persisted}),pmUserInteractions.forEach(function(a){window.addEventListener(a,pmTriggerDOMListener,{passive:!0})}),pmDelayClick&&(window.addEventListener("touchstart",pmTouchStartHandler,{passive:!0}),window.addEventListener("mousedown",pmTouchStartHandler)),document.addEventListener("visibilitychange",pmTriggerDOMListener);';

		  			//trigger elementor animations
		  			if(function_exists('\is_plugin_active') && \is_plugin_active('elementor/elementor.php')) {
		  				$script.= 'var pmeDeviceMode,pmeAnimationSettingsKeys,pmeCurrentAnimation;function pmeAnimation(){(pmeDeviceMode=document.createElement("span")).id="elementor-device-mode",pmeDeviceMode.setAttribute("class","elementor-screen-only"),document.body.appendChild(pmeDeviceMode),requestAnimationFrame(pmeDetectAnimations)}function pmeDetectAnimations(){pmeAnimationSettingsKeys=pmeListAnimationSettingsKeys(getComputedStyle(pmeDeviceMode,":after").content.replace(/"/g,"")),document.querySelectorAll(".elementor-invisible[data-settings]").forEach(a=>{let b=a.getBoundingClientRect();if(b.bottom>=0&&b.top<=window.innerHeight)try{pmeAnimateElement(a)}catch(c){}})}function pmeAnimateElement(a){let b=JSON.parse(a.dataset.settings),d=b._animation_delay||b.animation_delay||0,c=b[pmeAnimationSettingsKeys.find(a=>b[a])];if("none"===c)return void a.classList.remove("elementor-invisible");a.classList.remove(c),pmeCurrentAnimation&&a.classList.remove(pmeCurrentAnimation),pmeCurrentAnimation=c;let e=setTimeout(()=>{a.classList.remove("elementor-invisible"),a.classList.add("animated",c),pmeRemoveAnimationSettings(a,b)},d);window.addEventListener("perfmatters-startLoading",function(){clearTimeout(e)})}function pmeListAnimationSettingsKeys(b="mobile"){let a=[""];switch(b){case"mobile":a.unshift("_mobile");case"tablet":a.unshift("_tablet");case"desktop":a.unshift("_desktop")}let c=[];return["animation","_animation"].forEach(b=>{a.forEach(a=>{c.push(b+a)})}),c}function pmeRemoveAnimationSettings(a,b){pmeListAnimationSettingsKeys().forEach(a=>delete b[a]),a.dataset.settings=JSON.stringify(b)}document.addEventListener("DOMContentLoaded",pmeAnimation)';
					}

		  		$script.= '</script>';
		  		
	  			return $script;
	  		}
	  	}
	}

	//print fastclick js
	public static function print_fastclick() {

		if(is_admin()) {
			return;
		}

		if(isset($_GET['perfmattersoff'])) {
			return;
		}

		//exclude specific woocommerce pages
	    if(function_exists('is_woocommerce') && (is_cart() || is_checkout() || is_account_page())) {
	        return;
	    }

		echo '<script src="' . plugins_url('perfmatters/vendor/fastclick/fastclick.min.js') . '"></script>';
		echo '<script>"addEventListener"in document&&document.addEventListener("DOMContentLoaded",function(){FastClick.attach(document.body)},!1);</script>';
	}
}