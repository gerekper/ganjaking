( function ( $ ) {
	'use strict';
	$(document).ready(function () {
		if($('.content_hover_effect').length > 0){
			$('.content_hover_effect').each(function () {
				var $this=$(this);
				var hover_uniqid = $this.data('hover_uniqid');
				var hover_shadow = $this.data('hover_shadow');
				var content_hover_effects= $this.data('content_hover_effects');
				if(content_hover_effects=='float_shadow'){
					$('head').append('<style >.'+hover_uniqid+'.content_hover_float_shadow:before{background: -webkit-radial-gradient(center, ellipse, '+hover_shadow+' 0%, rgba(60, 60, 60, 0) 70%);background: radial-gradient(ellipse at center, '+hover_shadow+' 0%, rgba(60, 60, 60, 0) 70%);}</style>');
				}
				if(content_hover_effects=='shadow_radial'){
					$('head').append('<style >.'+hover_uniqid+'.content_hover_radial:after{background: -webkit-radial-gradient(50% -50%, ellipse, '+hover_shadow+' 0%, rgba(0, 0, 0, 0) 80%);background: radial-gradient(ellipse at 50% -50%, '+hover_shadow+' 0%, rgba(0, 0, 0, 0) 80%);}.'+hover_uniqid+'.content_hover_radial:before{background: -webkit-radial-gradient(50% 150%, ellipse, '+hover_shadow+' 0%, rgba(0, 0, 0, 0) 80%);background: radial-gradient(ellipse at 50% 150%, '+hover_shadow+' 0%, rgba(0, 0, 0, 0) 80%);}</style>');
				}
				if(content_hover_effects=='grow_shadow'){
					$('head').append('<style >.'+hover_uniqid+'.content_hover_grow_shadow:hover{-webkit-box-shadow: 0 10px 10px -10px '+hover_shadow+';-moz-box-shadow: 0 10px 10px -10px '+hover_shadow+';box-shadow: 0 10px 10px -10px '+hover_shadow+';}</style>');
				}			
			});
		}
	});	
} ( jQuery ) );