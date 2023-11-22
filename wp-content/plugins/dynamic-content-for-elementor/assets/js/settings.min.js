function dceGetElementSettings($element){var elementSettings=[];var modelCID=$element.data('model-cid');if(elementorFrontend.isEditMode()&&modelCID){var settings=elementorFrontend.config.elements.data[modelCID];var type=settings.attributes.widgetType||settings.attributes.elType;var settingsKeys=elementorFrontend.config.elements.keys[type];if(!settingsKeys){settingsKeys=elementorFrontend.config.elements.keys[type]=[];jQuery.each(settings.controls,function(name,control){if(control.frontend_available){settingsKeys.push(name)}})}
jQuery.each(settings.getActiveControls(),function(controlKey){if(-1!==settingsKeys.indexOf(controlKey)){elementSettings[controlKey]=settings.attributes[controlKey]}})}else{elementSettings=$element.data('settings')||{}}
return elementSettings}
function dceObserveElement($target,$function_callback){if(elementorFrontend.isEditMode()){var elemToObserve=$target;var config={attributes:!0,childList:!1,characterData:!0};var MutationObserver=window.MutationObserver||window.WebKitMutationObserver||window.MozMutationObserver;var observer=new MutationObserver($function_callback);observer.observe(elemToObserve,config)}}
window.dynamicooo={};dynamicooo.getActiveBreakpointsMinPointAndPostfix=()=>{let breakpoints=elementorFrontend.config.responsive.activeBreakpoints;let ret={};for(let key in breakpoints){ret[key]={min_point:elementorFrontend.breakpoints.getDeviceMinBreakpoint(key),postfix:`_${key}`,}}
ret.desktop={min_point:elementorFrontend.breakpoints.getDeviceMinBreakpoint('desktop'),postfix:'',}
return ret}
dynamicooo.makeSwiperBreakpoints=(swiperSettings,elementorSettings,elementorSettingsPrefix)=>{elementorSettingsPrefix=elementorSettingsPrefix||'';swiperBreakpoints={}
let elementorBreakpoints=dynamicooo.getActiveBreakpointsMinPointAndPostfix();let first=!0;for(let device in elementorBreakpoints){let min_point=elementorBreakpoints[device].min_point;if(first){min_point=0;first=!1}
let postfix=elementorBreakpoints[device].postfix;let breakpointSettings={}
for(let swiperSettingsKey in swiperSettings){if(swiperSettingsKey=='slidesPerGroup'){let slidesPerView=elementorSettings[elementorSettingsPrefix+swiperSettings.slidesPerView.elementor_key+postfix];if(slidesPerView==1){breakpointSettings[swiperSettingsKey]=1;continue}}
let elementorSettingsKey=elementorSettingsPrefix+swiperSettings[swiperSettingsKey].elementor_key;let default_value=swiperSettings[swiperSettingsKey].default_value;let postfixedKey=elementorSettingsKey+postfix;let value;if(typeof elementorSettings[postfixedKey]!=="undefined"){value=elementorSettings[postfixedKey]}else{value=elementorSettings[elementorSettingsKey]}
if(typeof value!=="undefined"){let filter=Number;if(typeof swiperSettings[swiperSettingsKey].filter==="function"){filter=swiperSettings[swiperSettingsKey].filter}
value=filter(value)}else{value=default_value}
breakpointSettings[swiperSettingsKey]=value}
swiperBreakpoints[min_point]=breakpointSettings}
return swiperBreakpoints}
window.initMap=()=>{const event=new Event('dce-google-maps-api-loaded');window.dispatchEvent(event)}