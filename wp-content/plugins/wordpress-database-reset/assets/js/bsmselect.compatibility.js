/*
 * Better Select Multiple Compatibility Plugin - jQuery Plugin
 *
 * Copyright (c) 2010-2011 by Victor Berchet - http://www.github.com/vicb
 *
 * Dual licensed under the MIT (MIT-LICENSE.txt) and GPL (GPL-LICENSE.txt) licenses.
 *
 * version: v1.0.1 - 2011-11-14
 */
!function(a){a.bsmSelect.plugins.compatibility=function(){return this instanceof a.bsmSelect.plugins.compatibility?void 0:new a.bsmSelect.plugins.compatibility},a.extend(a.bsmSelect.plugins.compatibility.prototype,{init:function(b){var c=b.options;"undefined"!=typeof c.animate&&(c.animate===!0?(c.showEffect=a.bsmSelect.effects.verticalListAdd,c.hideEffect=a.bsmSelect.effects.verticalListRemove):c.showEffect=a.isFunction(c.animate.add)?c.animate.add:"string"==typeof c.animate.add&&a.isFunction(a.bsmSelect.effects[c.animate.add])?a.bsmSelect.effects[c.animate.add]:a.bsmSelect.effects.show,c.hideEffect=a.isFunction(c.animate.drop)?c.animate.drop:"string"==typeof c.animate.drop&&a.isFunction(a.bsmSelect.effects[c.animate.drop])?a.bsmSelect.effects[c.animate.drop]:a.bsmSelect.effects.remove),"undefined"!=typeof c.highlight&&(c.highlight===!0?c.highlightEffect=a.bsmSelect.effects.highlight:a.isFunction(c.highlight)?c.highlightEffect=c.highlight:"string"==typeof c.highlight&&a.isFunction(a.bsmSelect.effects[c.highlight])&&(c.highlightEffect=a.bsmSelect.effects[c.highlight]))}})}(jQuery);
