<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.themepunch.com
 * @since      1.0.0
 *
 * @package    Revslider_Maintenance_Addon
 * @subpackage Revslider_Maintenance_Addon/public
 * @author     ThemePunch <info@themepunch.com>
 */
if(!defined('ABSPATH')) exit();
 
class Revslider_Maintenance_Addon_Public {

	/**
	 * The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 */
	private $version;

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 */
	public function enqueue_styles() {
		//wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/revslider-maintenance-addon-public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/revslider-maintenance-addon-public.js', array( 'jquery' ), $this->version, false );
	}

	/**
	 * Maintenance Page
	 *
	 * Displays the coming soon page for anyone who's not logged in.
	 * The login page gets excluded so that you can login if necessary.
	 */
	public function maintenance_mode(){
		$enabled = get_option( "revslider_maintenance_enabled" );
		if(!$enabled) return;
		
		global $pagenow;
		
		$revslider_maintenance_addon_values = self::return_mta_data();
		
		//if not login page, admin user, addon inactive show maintenance page
		if ( $pagenow !== 'wp-login.php' && $pagenow !=='revslider-sharing-addon-call.php' && $pagenow !=='revslider-login-addon-public-display.php' && ! current_user_can( 'manage_options' ) && ! is_admin() ) {
			// Fix for 502 Error since 2.0.1
			//header( 'HTTP/1.1 Service Unavailable', true, 503 );
			//header( 'Content-Type: text/html; charset=utf-8' );
			$protocol = $_SERVER['SERVER_PROTOCOL'];
			if ( 'HTTP/1.1' != $protocol && 'HTTP/1.0' != $protocol )
			$protocol = 'HTTP/1.0';
			header( "$protocol 503 Service Unavailable", true, 503 );
			header( 'Content-Type: text/html; charset=utf-8' );
			if ( file_exists( plugin_dir_path( __FILE__ ) . 'partials/revslider-maintenance-addon-public-display.php' ) ) {
				require_once( plugin_dir_path( __FILE__ ) . 'partials/revslider-maintenance-addon-public-display.php' );
			}
			
			die();
		}
	
	}
	
	public static function return_mta_data(){
		$mta = array();
		parse_str(get_option('revslider_maintenance_addon'), $mta);

		//defaults
		$mta['revslider-maintenance-addon-type'] = isset($mta['revslider-maintenance-addon-type']) ? $mta['revslider-maintenance-addon-type'] : 'slider';
		// $mta['revslider-maintenance-addon-active'] = isset($mta['revslider-maintenance-addon-active']) ? $mta['revslider-maintenance-addon-active'] : '0';
		$mta['revslider-maintenance-addon-slider'] = isset($mta['revslider-maintenance-addon-slider']) ? $mta['revslider-maintenance-addon-slider'] : '';
		$mta['revslider-maintenance-addon-page'] = isset($mta['revslider-maintenance-addon-page']) ? $mta['revslider-maintenance-addon-page'] : '';

		//Date Defaults
		$date=date_create(date('Y-m-d G:i',time()));
		$default_date = date_format($date,"F d, Y");
		$default_hour = date_format($date,"G");
		$default_minute = date_format($date,"i");

		$mta['revslider-maintenance-addon-countdown-day'] = isset($mta['revslider-maintenance-addon-countdown-day']) ? $mta['revslider-maintenance-addon-countdown-day'] : $default_date;
		$mta['revslider-maintenance-addon-countdown-hour'] = isset($mta['revslider-maintenance-addon-countdown-hour']) ? $mta['revslider-maintenance-addon-countdown-hour'] : $default_hour;
		$mta['revslider-maintenance-addon-countdown-minute'] = isset($mta['revslider-maintenance-addon-countdown-minute']) ? $mta['revslider-maintenance-addon-countdown-minute'] : $default_minute;
		$mta['revslider-maintenance-addon-countdown-active'] = isset($mta['revslider-maintenance-addon-countdown-active']) ? $mta['revslider-maintenance-addon-countdown-active'] : '0';
		$mta['revslider-maintenance-addon-auto-deactive'] = isset($mta['revslider-maintenance-addon-auto-deactive']) ? $mta['revslider-maintenance-addon-auto-deactive'] : '0';
		
		$addonTime = strtotime($mta['revslider-maintenance-addon-countdown-day']." ".$mta['revslider-maintenance-addon-countdown-hour'].":".$mta['revslider-maintenance-addon-countdown-minute']);
		$currentTime = current_time('timestamp');
		
		/*
		 * This is the time difference between the scheduled time and the real time (server-side)
		 * the difference is set here in the wp_option and then read/printed by the JS in the public-display class for front-end calculation
		*/
		$mta['revslider-maintenance-addon-real-time'] = $addonTime - $currentTime;
		update_option('revslider_maintenance_addon', http_build_query($mta));
		
		//if autodeactivate is on and set autodeactivate
		if(isset($mta['revslider-maintenance-addon-auto-deactive']) && $mta['revslider-maintenance-addon-auto-deactive']){
			//if now exceeded end date turn maintenance off
			if($addonTime - $currentTime <= 0){
				$mta['revslider-maintenance-addon-active'] = 0;
				update_option('revslider_maintenance_addon', http_build_query($mta));
				update_option('revslider_maintenance_enabled', 0);
			}
		}
		
		return $mta;
	}
	
	public static function add_js($mta){
		global $rs_maintanence_script_added;
		if($rs_maintanence_script_added === true) return;
		
		$rs_maintanence_script_added = true;
		?>
		<script>
			function initCountDown() {
				<?php
				/*
				* the "real-time" is the AddOn scheduled time minus the server time
				* the difference is then concatenated onto the user-side time here for time-zone independent accuracy
				* (the countdown script is compatible with this new Epoch time stamp by default)
				*/
				?>
				var d = '<?php echo $mta['revslider-maintenance-addon-real-time']; ?>';
				var t = new Date().getTime();
				var targetdate = parseInt(t, 10) + (parseInt(d, 10) * 1000);

				var slidechanges = [
						{ days:0, hours:0, minutes:0, seconds:12, slide:2},
						{ days:0, hours:0, minutes:0, seconds:0, slide:3}
					],
					quickjump = 15000,
					t_days,
					t_hours,
					t_minutes,
					t_seconds;

				id_array = jQuery("rs-module-wrap:first").attr("id").split("_");
				id = id_array[2];				
				jQuery.globalEval('var api = revapi'+id+';');

				function maint_quick_change(a) {					
					jQuery("rs-layer:contains('%"+a+"%'), .rs-layer:contains('%"+a+"%'), rs-layer:contains('{{"+a+"}}'), .rs-layer:contains('{{"+a+"}}')" ).each(function(){
						if (this.dataset.type==="text") {
							var _ = jQuery(this);							
							_.html(_.html().replace('%'+a+'%','<'+a+' class="'+a+'" style="display:inline-block;position:relative;">00</'+a+'>').replace('{{'+a+'}}','<'+a+' class="'+a+'" style="display:inline-block;position:relative;">00</'+a+'>'));
						}
					});
					return a;
				}

				t_days = maint_quick_change("t_days");
				t_hours = maint_quick_change("t_hours");
				t_minutes = maint_quick_change("t_minutes");
				t_seconds = maint_quick_change("t_seconds");

				// countdown.js jQuery Engine MADE BY HILIOS
				// https://github.com/hilios/jQuery.countdown
				!function(t){"use strict";"function"==typeof define&&define.amd?define(["jquery"],t):t(jQuery)}(function(t){"use strict";function e(t){if(t instanceof Date)return t;if(String(t).match(o))return String(t).match(/^[0-9]*$/)&&(t=Number(t)),String(t).match(/\-/)&&(t=String(t).replace(/\-/g,"/")),new Date(t);throw new Error("Couldn't cast `"+t+"` to a date object.")}function s(t){var e=t.toString().replace(/([.?*+^$[\]\\(){}|-])/g,"\\$1");return new RegExp(e)}function n(t){return function(e){var n=e.match(/%(-|!)?[A-Z]{1}(:[^;]+;)?/gi);if(n)for(var a=0,o=n.length;o>a;++a){var r=n[a].match(/%(-|!)?([a-zA-Z]{1})(:[^;]+;)?/),l=s(r[0]),c=r[1]||"",u=r[3]||"",f=null;r=r[2],h.hasOwnProperty(r)&&(f=h[r],f=Number(t[f])),null!==f&&("!"===c&&(f=i(u,f)),""===c&&10>f&&(f="0"+f.toString()),e=e.replace(l,f.toString()))}return e=e.replace(/%%/,"%")}}function i(t,e){var s="s",n="";return t&&(t=t.replace(/(:|;|\s)/gi,"").split(/\,/),1===t.length?s=t[0]:(n=t[0],s=t[1])),1===Math.abs(e)?n:s}var a=[],o=[],r={precision:100,elapse:!1};o.push(/^[0-9]*$/.source),o.push(/([0-9]{1,2}\/){2}[0-9]{4}( [0-9]{1,2}(:[0-9]{2}){2})?/.source),o.push(/[0-9]{4}([\/\-][0-9]{1,2}){2}( [0-9]{1,2}(:[0-9]{2}){2})?/.source),o=new RegExp(o.join("|"));var h={Y:"years",m:"months",n:"daysToMonth",w:"weeks",d:"daysToWeek",D:"totalDays",H:"hours",M:"minutes",S:"seconds"},l=function(e,s,n){this.el=e,this.$el=t(e),this.interval=null,this.offset={},this.options=t.extend({},r),this.instanceNumber=a.length,a.push(this),this.$el.data("countdown-instance",this.instanceNumber),n&&("function"==typeof n?(this.$el.on("update.countdown",n),this.$el.on("stoped.countdown",n),this.$el.on("finish.countdown",n)):this.options=t.extend({},r,n)),this.setFinalDate(s),this.start()};t.extend(l.prototype,{start:function(){null!==this.interval&&clearInterval(this.interval);var t=this;this.update(),this.interval=setInterval(function(){t.update.call(t)},this.options.precision)},stop:function(){clearInterval(this.interval),this.interval=null,this.dispatchEvent("stoped")},toggle:function(){this.interval?this.stop():this.start()},pause:function(){this.stop()},resume:function(){this.start()},remove:function(){this.stop.call(this),a[this.instanceNumber]=null,delete this.$el.data().countdownInstance},setFinalDate:function(t){this.finalDate=e(t)},update:function(){if(0===this.$el.closest("html").length)return void this.remove();var e,s=void 0!==t._data(this.el,"events"),n=new Date;e=this.finalDate.getTime()-n.getTime(),e=Math.ceil(e/1e3),e=!this.options.elapse&&0>e?0:Math.abs(e),this.totalSecsLeft!==e&&s&&(this.totalSecsLeft=e,this.elapsed=n>=this.finalDate,this.offset={seconds:this.totalSecsLeft%60,minutes:Math.floor(this.totalSecsLeft/60)%60,hours:Math.floor(this.totalSecsLeft/60/60)%24,days:Math.floor(this.totalSecsLeft/60/60/24)%7,daysToWeek:Math.floor(this.totalSecsLeft/60/60/24)%7,daysToMonth:Math.floor(this.totalSecsLeft/60/60/24%30.4368),totalDays:Math.floor(this.totalSecsLeft/60/60/24),weeks:Math.floor(this.totalSecsLeft/60/60/24/7),months:Math.floor(this.totalSecsLeft/60/60/24/30.4368),years:Math.abs(this.finalDate.getFullYear()-n.getFullYear())},this.options.elapse||0!==this.totalSecsLeft?this.dispatchEvent("update"):(this.stop(),this.dispatchEvent("finish")))},dispatchEvent:function(e){var s=t.Event(e+".countdown");s.finalDate=this.finalDate,s.elapsed=this.elapsed,s.offset=t.extend({},this.offset),s.strftime=n(this.offset),this.$el.trigger(s)}}),t.fn.countdown=function(){var e=Array.prototype.slice.call(arguments,0);return this.each(function(){var s=t(this).data("countdown-instance");if(void 0!==s){var n=a[s],i=e[0];l.prototype.hasOwnProperty(i)?n[i].apply(n,e.slice(1)):null===String(i).match(/^[$A-Z_][0-9A-Z_$]*$/i)?(n.setFinalDate.call(n,i),n.start()):t.error("Method %s does not exist on jQuery.countdown".replace(/\%s/gi,i))}else new l(this,e[0],e[1])})}});


				var currentd,currenth,currentm,currents;

				function animateAndUpdate(o,nt,ot) {
					var elements = document.getElementsByClassName(o);
					
					for (var i in elements) {						
						if (!elements.hasOwnProperty(i)) continue;
						var o = elements[i];						
						if (ot==undefined) {
							o.innerHTML = nt;
						} else {							
							if (o.style.opacity!==" " && o.style.opacity!==0) {								
								punchgs.TweenLite.fromTo(o,0.45,
								{autoAlpha:1,rotationY:0,scale:1},
								{autoAlpha:0,rotationY:-180,scale:0.5,ease:punchgs.back.in,onComplete:function() { o.innerHTML = nt;} });

								punchgs.TweenLite.fromTo(o,0.45,
								{autoAlpha:0,rotationY:180,scale:0.5},
								{autoAlpha:1,rotationY:0,scale:1,ease:punchgs.back.out,delay:0.5 });
							} else {
								o.innerHTML = nt;
							}
						}
					}
					return nt;
				}

				function countprocess(event) {

					
					var newd = event.strftime('%D'),
					newh = event.strftime('%H'),
					newm = event.strftime('%M'),
					news = event.strftime('%S');

					<?php if($mta['revslider-maintenance-addon-auto-deactive']){ ?>if(newd=="00" && newh=="00" && newm=="00" && news=="00") window.location.reload();<?php } ?>

					if (newd != currentd) currentd = animateAndUpdate(t_days,newd,currentd);
					if (newh != currenth) currenth = animateAndUpdate(t_hours,newh,currenth);
					if (newm != currentm) currentm = animateAndUpdate(t_minutes,newm,currentm);
					if (news != currents) currents = animateAndUpdate(t_seconds,news,currents);
					

					jQuery.each(slidechanges,function(i,obj) {
						var dr = obj.days==undefined || obj.days>=newd,
						hr = obj.hours==undefined || obj.hours>=newh,
						mr = obj.minutes==undefined || obj.minutes>=newm,
						sr = obj.seconds==undefined || obj.seconds>=news;						
						if (dr && hr && mr && sr && !obj.changedown) {						
							api.revshowslide(obj.slide);
							obj.changedown = true;
						}
					})
				}			
				

				api.countdown(targetdate, countprocess);
			}
			function waitInitCountDown() {
				if (window.RS_MODULES!==undefined && window.RS_MODULES.minimal) initCountDown();
				else setTimeout(waitInitCountDown,100);
			}

			document.addEventListener("DOMContentLoaded", waitInitCountDown);
		</script>
		<?php
	}
}
