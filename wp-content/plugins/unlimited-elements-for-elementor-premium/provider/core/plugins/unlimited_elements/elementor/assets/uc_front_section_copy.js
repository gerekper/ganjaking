function UnlimitedElementsCopySection(){
	
	var g_objSections, g_options;
	
	
	/**
	 * get button html for put into section
	 */
	function getHTMLButton(){
		
		var html = "";
		
		html += "<div class='uc-section-copy-wrapper'>";
		html += "	<a href='javascript:void(0)' class='uc-section-copy-button'>Copy Section</a>";
		html += "   <input type='text' class='uc-section-copy-input'>";
		html += "</div>";
		
		return(html);
	}
	
	
	/**
	 * put section buttons
	 */
	function putSectionButtons(){
		
		if(g_objSections.length == 0)
			return(false);
		
		var urlAjax = g_options.ajax_url;
				
		jQuery.each(g_objSections, function(index, section){
			var objSection = jQuery(section);
			var sectionID = objSection.data("id");
			
			var html = getHTMLButton();
			
			objSection.append(html);
			
			var objParent = objSection.parents(".elementor");
			var parentID = objParent.data("elementor-id");
			
			var objInput = objSection.find(".uc-section-copy-input");
			
			var value = "---uc-section-copy~"+parentID+"~"+sectionID+"~"+urlAjax;
			
			value = "uesection--"+encodeContent(value);
			
			objInput.val(value);
			
		});
		
	}
	
	/**
	 * copy section
	 */
	function onButtonCopyClick(){
		
		var objButton = jQuery(this);
		var objWrapper = objButton.parent();
		var objInput = objWrapper.find(".uc-section-copy-input");
		
		var input = objInput[0];
		
		input.select();
		input.setSelectionRange(0, 99999);
		document.execCommand("copy");
		
		objButton.html("Section Copied!!!");
		setTimeout(function(){
			objButton.html("Copy Section");
		},500);
	}
	
	/**
	 * raw url encode
	 */
	function rawurlencode(str){str=(str+'').toString();return encodeURIComponent(str).replace(/!/g,'%21').replace(/'/g,'%27').replace(/\(/g,'%28').replace(/\)/g,'%29').replace(/\*/g,'%2A');}
		
	
	/**
	 * utf-8 encode
	 */
	function utf8_encode(argString){
		if(argString===null||typeof argString==="undefined"){return"";}
		var string=(argString+'');var utftext="",start,end,stringl=0;start=end=0;stringl=string.length;for(var n=0;n<stringl;n++){var c1=string.charCodeAt(n);var enc=null;if(c1<128){end++;}else if(c1>127&&c1<2048){enc=String.fromCharCode((c1>>6)|192)+String.fromCharCode((c1&63)|128);}else{enc=String.fromCharCode((c1>>12)|224)+String.fromCharCode(((c1>>6)&63)|128)+String.fromCharCode((c1&63)|128);}
		if(enc!==null){if(end>start){utftext+=string.slice(start,end);}
		utftext+=enc;start=end=n+1;}}
		if(end>start){utftext+=string.slice(start,stringl);}
	return utftext;}
	
	
	/**
	 * base 64 encode
	 */
	function base64_encode(data){
		var b64="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";var o1,o2,o3,h1,h2,h3,h4,bits,i=0,ac=0,enc="",tmp_arr=[];if(!data){return data;}
		data=utf8_encode(data+'');do{o1=data.charCodeAt(i++);o2=data.charCodeAt(i++);o3=data.charCodeAt(i++);bits=o1<<16|o2<<8|o3;h1=bits>>18&0x3f;h2=bits>>12&0x3f;h3=bits>>6&0x3f;h4=bits&0x3f;tmp_arr[ac++]=b64.charAt(h1)+b64.charAt(h2)+b64.charAt(h3)+b64.charAt(h4);}while(i<data.length);enc=tmp_arr.join('');var r=data.length%3;return(r?enc.slice(0,r-3):enc)+'==='.slice(r||3);
	}
	
	
	/**
	 * encode some content
	 */
	function encodeContent(value){
		return base64_encode(rawurlencode(value));
	};
	
	
	/**
	 * init copy section
	 */
	this.init = function(){
				
		if(typeof g_ucCopySectionConfig == "undefined"){
			console.log("copy section not inited");
			return(false);
		}
			
		g_options = JSON.parse(g_ucCopySectionConfig);
		
		g_objSections = jQuery(".elementor-top-section");
		if(g_objSections.length == 0)
			return(false);
		
		putSectionButtons();
		
		//init events
		g_objSections.on("click",".uc-section-copy-button", onButtonCopyClick);
				
	}
	
}

jQuery(document).ready(function(){
	
	var objCopySection = new UnlimitedElementsCopySection();
	objCopySection.init();
	
});

