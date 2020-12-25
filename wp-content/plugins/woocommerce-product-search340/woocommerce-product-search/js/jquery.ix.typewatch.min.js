/*!
*	TypeWatch 3/ix
*
*	Examples/Docs: github.com/dennyferra/TypeWatch
*  
*  Dual licensed under the MIT and GPL licenses:
*  http://www.opensource.org/licenses/mit-license.php
*  http://www.gnu.org/licenses/gpl.html
*/
;!function(a,b){if(typeof define==="function"&&define.amd){define(["jquery"],b)}else{if(typeof exports==="object"){b(require("jquery"))}else{b(a.jQuery)}}}(this,function(a){a.fn.typeWatch=function(e){var c=["TEXT","TEXTAREA","PASSWORD","TEL","SEARCH","URL","EMAIL","DATETIME","DATE","MONTH","WEEK","TIME","DATETIME-LOCAL","NUMBER","RANGE","DIV"];var b=a.extend({wait:750,callback:function(){},highlight:true,captureLength:2,allowSubmit:false,inputTypes:c},e);function d(i,g){var h=i.type==="DIV"?jQuery(i.el).html():jQuery(i.el).val();if((h.length>=b.captureLength&&h!=i.text)||(g&&(h.length>=b.captureLength||b.allowSubmit))||(h.length==0&&i.text)){i.text=h;i.cb.call(i.el,h)}}function f(j){var h=(j.type||j.nodeName).toUpperCase();if(jQuery.inArray(h,b.inputTypes)>=0){var k={timer:null,text:(h==="DIV")?jQuery(j).html():jQuery(j).val(),cb:b.callback,el:j,type:h,wait:b.wait};if(b.highlight&&h!=="DIV"){jQuery(j).focus(function(){this.select()})}var i=function(l){var p=k.wait;var o=false;var n=h;if(typeof l.keyCode!="undefined"&&l.keyCode==13&&n!=="TEXTAREA"&&h!=="DIV"){console.log("OVERRIDE");p=1;o=true}var m=function(){d(k,o)};clearTimeout(k.timer);k.timer=setTimeout(m,p)};var g=function(l){var m=l.data.timer;m.text="";if(m.elementType!=="DIV"){jQuery(m.el).val("")}else{jQuery(m.el).html("")}};jQuery(j).on("keydown paste cut input",i);jQuery(j).on("clear",null,{timer:k},g)}}return this.each(function(){f(this)})}});