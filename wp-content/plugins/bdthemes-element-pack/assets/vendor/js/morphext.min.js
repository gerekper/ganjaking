/*!
 * Morphext - Text Rotating Plugin for jQuery
 * https://github.com/MrSaints/Morphext
 *
 * Built on jQuery Boilerplate
 * http://jqueryboilerplate.com/
 *
 * Copyright 2014 Ian Lai and other contributors
 * Released under the MIT license
 * http://ian.mit-license.org/
 */
!function(t){"use strict";var i={animation:"bounceIn",separator:",",speed:2e3,complete:t.noop};function s(s,e){this.element=t(s),this.settings=t.extend({},i,e),this._defaults=i,this._init()}s.prototype={_init:function(){var i=this;this.phrases=[],this.element.addClass("morphext"),t.each(this.element.text().split(this.settings.separator),(function(s,e){i.phrases.push(t.trim(e))})),this.index=-1,this.animate(),this.start()},animate:function(){this.index=++this.index%this.phrases.length,this.element[0].innerHTML='<span class="animated '+this.settings.animation+'">'+this.phrases[this.index]+"</span>",t.isFunction(this.settings.complete)&&this.settings.complete.call(this)},start:function(){var t=this;this._interval=setInterval((function(){t.animate()}),this.settings.speed)},stop:function(){this._interval=clearInterval(this._interval)}},t.fn.Morphext=function(i){return this.each((function(){t.data(this,"plugin_Morphext")||t.data(this,"plugin_Morphext",new s(this,i))}))}}(jQuery);