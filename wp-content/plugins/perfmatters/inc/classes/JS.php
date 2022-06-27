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

			$atts_array = !empty($matches[2][$i]) ? perfmatters_lazyload_get_atts_array($matches[2][$i]) : array();
			
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
						'customize-support'
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

	    			$delayed_atts_string = perfmatters_lazyload_get_atts_string($atts_array);
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
	  			return '<script type="text/javascript" id="perfmatters-delayed-scripts-js">' . (!empty($timeout) ? 'const pmDelayTimer=setTimeout(pmTriggerDOMListener,' . $timeout . '*1000),' : '') . 'pmUserInteractions=["keydown","mousemove","wheel","touchmove","touchstart","touchend","touchcancel","touchforcechange"],pmDelayedScripts={normal:[],defer:[],async:[]},jQueriesArray=[];var pmDOMLoaded=!1;function pmTriggerDOMListener(){' . (!empty($timeout) ? 'clearTimeout(pmDelayTimer),' : '') . 'pmUserInteractions.forEach(function(e){window.removeEventListener(e,pmTriggerDOMListener,{passive:!0})}),"loading"===document.readyState?document.addEventListener("DOMContentLoaded",pmTriggerDelayedScripts):pmTriggerDelayedScripts()}async function pmTriggerDelayedScripts(){pmDelayEventListeners(),pmDelayJQueryReady(),pmProcessDocumentWrite(),pmSortDelayedScripts(),pmPreloadDelayedScripts(),await pmLoadDelayedScripts(pmDelayedScripts.normal),await pmLoadDelayedScripts(pmDelayedScripts.defer),await pmLoadDelayedScripts(pmDelayedScripts.async),await pmTriggerEventListeners()}function pmDelayEventListeners(){let e={};function t(t,n){function r(n){return e[t].delayedEvents.indexOf(n)>=0?"perfmatters-"+n:n}e[t]||(e[t]={originalFunctions:{add:t.addEventListener,remove:t.removeEventListener},delayedEvents:[]},t.addEventListener=function(){arguments[0]=r(arguments[0]),e[t].originalFunctions.add.apply(t,arguments)},t.removeEventListener=function(){arguments[0]=r(arguments[0]),e[t].originalFunctions.remove.apply(t,arguments)}),e[t].delayedEvents.push(n)}function n(e,t){const n=e[t];Object.defineProperty(e,t,{get:n||function(){},set:function(n){e["perfmatters"+t]=n}})}t(document,"DOMContentLoaded"),t(window,"DOMContentLoaded"),t(window,"load"),t(window,"pageshow"),t(document,"readystatechange"),n(document,"onreadystatechange"),n(window,"onload"),n(window,"onpageshow")}function pmDelayJQueryReady(){let e=window.jQuery;Object.defineProperty(window,"jQuery",{get:()=>e,set(t){if(t&&t.fn&&!jQueriesArray.includes(t)){t.fn.ready=t.fn.init.prototype.ready=function(e){pmDOMLoaded?e.bind(document)(t):document.addEventListener("perfmatters-DOMContentLoaded",function(){e.bind(document)(t)})};const e=t.fn.on;t.fn.on=t.fn.init.prototype.on=function(){if(this[0]===window){function t(e){return e.split(" ").map(e=>"load"===e||0===e.indexOf("load.")?"perfmatters-jquery-load":e).join(" ")}"string"==typeof arguments[0]||arguments[0]instanceof String?arguments[0]=t(arguments[0]):"object"==typeof arguments[0]&&Object.keys(arguments[0]).forEach(function(e){delete Object.assign(arguments[0],{[t(e)]:arguments[0][e]})[e]})}return e.apply(this,arguments),this},jQueriesArray.push(t)}e=t}})}function pmProcessDocumentWrite(){const e=new Map;document.write=document.writeln=function(t){var n=document.currentScript,r=document.createRange();let a=e.get(n);void 0===a&&(a=n.nextSibling,e.set(n,a));var o=document.createDocumentFragment();r.setStart(o,0),o.appendChild(r.createContextualFragment(t)),n.parentElement.insertBefore(o,a)}}function pmSortDelayedScripts(){document.querySelectorAll("script[type=pmdelayedscript]").forEach(function(e){e.hasAttribute("src")?e.hasAttribute("defer")&&!1!==e.defer?pmDelayedScripts.defer.push(e):e.hasAttribute("async")&&!1!==e.async?pmDelayedScripts.async.push(e):pmDelayedScripts.normal.push(e):pmDelayedScripts.normal.push(e)})}function pmPreloadDelayedScripts(){var e=document.createDocumentFragment();[...pmDelayedScripts.normal,...pmDelayedScripts.defer,...pmDelayedScripts.async].forEach(function(t){var n=t.getAttribute("src");if(n){var r=document.createElement("link");r.href=n,r.rel="preload",r.as="script",e.appendChild(r)}}),document.head.appendChild(e)}async function pmLoadDelayedScripts(e){var t=e.shift();return t?(await pmReplaceScript(t),pmLoadDelayedScripts(e)):Promise.resolve()}async function pmReplaceScript(e){return await pmNextFrame(),new Promise(function(t){const n=document.createElement("script");[...e.attributes].forEach(function(e){let t=e.nodeName;"type"!==t&&("data-type"===t&&(t="type"),n.setAttribute(t,e.nodeValue))}),e.hasAttribute("src")?(n.addEventListener("load",t),n.addEventListener("error",t)):(n.text=e.text,t()),e.parentNode.replaceChild(n,e)})}async function pmTriggerEventListeners(){pmDOMLoaded=!0,await pmNextFrame(),document.dispatchEvent(new Event("perfmatters-DOMContentLoaded")),await pmNextFrame(),window.dispatchEvent(new Event("perfmatters-DOMContentLoaded")),await pmNextFrame(),document.dispatchEvent(new Event("perfmatters-readystatechange")),await pmNextFrame(),document.perfmattersonreadystatechange&&document.perfmattersonreadystatechange(),await pmNextFrame(),window.dispatchEvent(new Event("perfmatters-load")),await pmNextFrame(),window.perfmattersonload&&window.perfmattersonload(),await pmNextFrame(),jQueriesArray.forEach(function(e){e(window).trigger("perfmatters-jquery-load")}),window.dispatchEvent(new Event("perfmatters-pageshow")),await pmNextFrame(),window.perfmattersonpageshow&&window.perfmattersonpageshow()}async function pmNextFrame(){return new Promise(function(e){requestAnimationFrame(e)})}pmUserInteractions.forEach(function(e){window.addEventListener(e,pmTriggerDOMListener,{passive:!0})});</script>';
	  		}
	  	}
	}
}