"use strict";/**
 * @namespace
 */jQuery(document).ready(function($){/**
	 * Resets sorting preference with ajax callback.
	 */$("#ac-table-actions .reset-sorting").click(function(e){e.preventDefault();if($(this).hasClass("disabled")){return}$(this).addClass("disabled");$.post(ajaxurl,{action:"acp_reset_sorting",list_screen:AC.list_screen,layout:AC.layout,_ajax_nonce:AC.ajax_nonce},function(response){if(response&&true===response.data){window.location.href=removeParam("order",removeParam("orderby",window.location.href))}else{$(this).removeClass("disabled")}},"json")})});/**
 * Remove query param from URL
 *
 * @param key string
 * @param sourceURL string
 * @returns {*}
 */function removeParam(key,sourceURL){var rtn=sourceURL.split("?")[0],param,params_arr=[],queryString=sourceURL.indexOf("?")!==-1?sourceURL.split("?")[1]:"";if(queryString!==""){params_arr=queryString.split("&");for(var i=params_arr.length-1;i>=0;i-=1){param=params_arr[i].split("=")[0];if(param===key){params_arr.splice(i,1)}}rtn=rtn+"?"+params_arr.join("&")}return rtn}