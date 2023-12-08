jQuery( window ).on( 'elementor:init', function() {
	// Query Control
	var PlusQuery = elementor.modules.controls.Select2.extend( {

		cache: null,
		isTitlesReceived: false,

		getSelect2Placeholder: function getSelect2Placeholder() {
			var self = this;
			
			return {
				id: '',
				text: self.model.get('placeholder') || 'All',
			};
		},

		getSelect2DefaultOptions: function getSelect2DefaultOptions() {
			var self = this;

			return jQuery.extend( elementor.modules.controls.Select2.prototype.getSelect2DefaultOptions.apply( this, arguments ), {
				ajax: {
					transport: function transport( params, success, failure ) {
						var data = {
							q 			: params.data.q,
							query_type 	: self.model.get('query_type'),
							object_type : self.model.get('object_type'),
							query_options 	: self.model.get('query_options'),
						};

						return elementorCommon.ajax.addRequest('plus_query_control_filter_autocomplete', {
							data 	: data,
							success : success,
							error 	: failure,
						});
					},
					data: function data( params ) {
						return {
							q 	 : params.term,
							page : params.page,
						};
					},
					cache: true
				},
				escapeMarkup: function escapeMarkup(markup) {
					return markup;
				},
				minimumInputLength: 2
			});
		},

		get_value_titles: function get_value_titles() {
			var self 		= this,
			    valueIds 		= this.getControlValue(),
			    queryTypeOpt 	= this.model.get('query_type'),
			    objectTypeOpt 	= this.model.get('object_type'),
				queryOptionsOpt = this.model.get('query_options');

			if ( ! valueIds || ! queryTypeOpt ) return;

			if ( ! _.isArray( valueIds ) ) {
				valueIds = [ valueIds ];
			}

			elementorCommon.ajax.loadObjects({
				action 	: 'plus_query_control_value_titles',
				ids 	: valueIds,
				data 	: {
					query_type 	: queryTypeOpt,
					object_type : objectTypeOpt,
					query_options 	: queryOptionsOpt,
					unique_id 	: '' + self.cid + queryTypeOpt,
				},
				success: function success(data) {
					self.isTitlesReceived = true;
					self.model.set('options', data);
					self.render();
				},
				before: function before() {
					self.add_spinner();
				},
			});
		},

		add_spinner: function add_spinner() {
			this.ui.select.prop('disabled', true);
			this.$el.find('.elementor-control-title').after('<span class="elementor-control-spinner">&nbsp;<i class="fas fa-spinner fa-spin"></i>&nbsp;</span>');
		},

		onReady: function onReady() {
			setTimeout( elementor.modules.controls.Select2.prototype.onReady.bind(this) );

			if ( ! this.isTitlesReceived ) {
				this.get_value_titles();
			}
		},
		
		onBeforeDestroy: function onBeforeDestroy() {
			if (this.ui.select.data('select2')) {
				this.ui.select.select2('destroy');
			}

			this.$el.remove();
		},

	} );
    
	elementor.addControlView( 'plus-query', PlusQuery );
} );

setTimeout(function() { 
    jQuery( "body.elementor-panel-loading #elementor-panel-state-loading" ).prepend( "<div class='theplus-bk-ele-spinner-main'>Still loading? <br/><a href='https://docs.posimyth.com/tpae/elementor-panel-in-the-editor-is-frozen-and-showing-a-spinning-circle/' target='_blank' class='theplus-bk-ele-spinner-info'>Check Solutions</a></div>" );
}, 60000);

setInterval(function(){
    jQuery( "#elementor-panel-elements-search-input" ).on("keyup",function() {
        if( jQuery(this).closest('#elementor-panel-page-elements').find('#elementor-panel-elements') 
            && jQuery(this).closest('#elementor-panel-page-elements').find('#elementor-panel-elements').length > 0
            && jQuery(this).closest('#elementor-panel-page-elements').find('#elementor-panel-elements').html().length > 0){
            jQuery('.tp-wid-missinginfo').remove();
        }else{
            jQuery(this).closest('#elementor-panel-page-elements').find('#elementor-panel-elements').append('<div class="tp-wid-missinginfo" style="color:#fff;font-size:12px;line-height:3;">Unable to find a widget?<br/>Make sure to enable from Plus Settings. <br/> <a href="https://docs.posimyth.com/tpae/elementor-widgets-or-custom-widgets-not-showing-on-elementor-editor/" target="_blank">Learn More ></a></div>');
        }
    });
}, 100);

jQuery(document).on( 'click' , ".tp-beach-fb-button", function() {
	let url = "https://theplusaddons.com/social-app-reviews/",
		EditModeClass = this.closest("#elementor-controls"),
		FillTextArea = jQuery(EditModeClass).find('.elementor-control-BToken textarea');
		FillPageId = jQuery(EditModeClass).find('.elementor-control-BPPId input');

		var top = screen.height / 2 - 520 / 2,
			left = screen.width / 2 - 670 / 2,
			PopupOne = window.open(url,"","location=1,status=1,resizable=yes,width=670,height=520,top="+top+",left="+left );

        function tp_callback() {
            if (!PopupOne || PopupOne.closed != false) {
                jQuery.ajax({
                    type: "POST",
                    url: PlusEditor_localize.ajax,
                    dataType: "JSON",
                    data: {
                        action: "theplus_socialreview_Gettoken",
                        security: PlusEditor_localize.ajax,
						GetNonce: PlusEditor_localize.SocialReview_nonce,
                    },
                    success: function (res) {
						if(res.success){
							jQuery(FillTextArea).val(res.SocialReview['data'][0].access_token).trigger("input");
							jQuery(FillPageId).val(res.SocialReview['data'][0].id).trigger("input");
						}else{
							alert("something wrong");
						}
                    },
                    error: function (err) {
                        console.log(err);
                    }
                });
            } else {
                setTimeout(tp_callback, 100);
            }
        }
        setTimeout(tp_callback, 100);
		
});

jQuery(document).on( 'click' , ".tp-review-fb-button", function() {
	var url = "https://theplusaddons.com/social-app-reviews/",
		EditModeClass= this.closest(".elementor-repeater-row-controls.editable"),
		FillTextArea = jQuery(EditModeClass).find('.elementor-control-Token textarea');
		FillPageId = jQuery(EditModeClass).find('.elementor-control-FbPageId input');

    var top = screen.height / 2 - 520 / 2,
        left = screen.width / 2 - 670 / 2,
        PopupOne = window.open(url,"","location=1,status=1,resizable=yes,width=670,height=520,top="+top+",left="+left );
		
        function tp_callback() {
            if (!PopupOne || PopupOne.closed != false) {
                jQuery.ajax({
                    type: "POST",
                    url: PlusEditor_localize.ajax,
                    dataType: "JSON",
                    data: {
                        action: "theplus_socialreview_Gettoken",
                        security: PlusEditor_localize.ajax,
						GetNonce: PlusEditor_localize.SocialReview_nonce,
                    },
                    success: function (res) {
						if(res.success){
							jQuery(FillTextArea).val(res.SocialReview['data'][0].access_token).trigger("input");
							jQuery(FillPageId).val(res.SocialReview['data'][0].id).trigger("input");
						}else{
							alert("something wrong");
						}
                    },
                    error: function (err) {
                        console.log(err);
                    }
                });
            } else {
                setTimeout(tp_callback, 100);
            }
        }
        setTimeout(tp_callback, 100);
});

jQuery(document).on( 'click' , ".tp-feed-fb-button", function() {
	var EditModeClass= this.closest(".elementor-repeater-row-controls.editable"),
	    GetAppid = jQuery(EditModeClass).find('.elementor-control-SFFbAppId input').val();
	if(GetAppid === ''){
        alert('Enter APP ID');
        return false;
    }else{
		Facebook_key_generat('SocialFeed', this);
	}
});

jQuery(document).on( 'click' , ".tp-feed-IG-button", function() {
	var EditModeClass= this.closest(".elementor-repeater-row-controls.editable"),
	    GetAppid = jQuery(EditModeClass).find('.elementor-control-SFFbAppId input').val();
	if(GetAppid === ''){
        alert('Enter APP ID');
        return false;
    }else{
		Facebook_key_generat('IG_SocialFeed', this);
	}
});

function Facebook_key_generat(Type, $this) {
    (function(d, s, id){
        var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) {return;}
            js = d.createElement(s); js.id = id;
            js.src = "https://connect.facebook.net/en_US/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'))
    
    var EditModeClass= $this.closest(".elementor-repeater-row-controls.editable"),
        GetAppid ='',
        SFOption ='',
        FillTextArea ='',
        Permissions ='',
        FillPageId ='',
		$ = jQuery;
    
    if(Type == 'SocialFeed'){
        GetAppid = $(EditModeClass).find('.elementor-control-SFFbAppId input').val(),
        GetAppSecret = $(EditModeClass).find('.elementor-control-SFFbAppSecretId input').val(),
        SFOption = $(EditModeClass).find('.elementor-control-ProfileType select :selected').val(),
        FillTextArea = $(EditModeClass).find('.elementor-control-RAToken textarea');

        if(SFOption == 'page'){
            FillPageId = $(EditModeClass).find('.elementor-control-Pageid input');
            Permissions = 'pages_read_engagement,pages_read_user_content,user_photos,pages_show_list';
        }else if(SFOption == 'post'){
            Permissions = 'user_posts,user_photos,user_videos,user_likes,user_link';
        }
    }else if(Type == 'IG_SocialFeed'){
        GetAppid = $(EditModeClass).find('.elementor-control-SFFbAppId input').val(),
        GetAppSecret = $(EditModeClass).find('.elementor-control-SFFbAppSecretId input').val(),
        FillTextArea = $(EditModeClass).find('.elementor-control-RAToken textarea'),
        FillPageId = $(EditModeClass).find('.elementor-control-IGPageId input'),
        Permissions = 'pages_show_list,pages_read_engagement,instagram_basic,ads_management,business_management,instagram_content_publish,instagram_manage_comments,instagram_manage_insights';
    }

        window.fbAsyncInit = function() {
            FB.init({
				appId: GetAppid,
				status: true,
				cookie: true,
				oauth: true,
				xfbml: true,
				version: 'v11.0',
            });

            FB.login(function(loginRes) {
                if (loginRes.status === 'connected') {
                    var userid = loginRes.authResponse.userID,
                        accessToken = loginRes.authResponse.accessToken;

                    if( loginRes && Type == 'SocialFeed' ){
                        if(SFOption == 'page'){
                            Generate_longlived_token(FillTextArea, FillPageId, GetAppid, GetAppSecret, userid, accessToken );
                        }else if(SFOption == 'post'){
                            Generate_longlived_token(FillTextArea, FillPageId, GetAppid, GetAppSecret, userid, accessToken );
                        }
                    }else if(loginRes && Type == 'IG_SocialFeed'){
                        Generate_longlived_token(FillTextArea, FillPageId, GetAppid, GetAppSecret, userid, accessToken );
                    }
                } else if (loginRes.status === 'not_authorized'){
                    console.log('not_authorized');
                } else {
                    console.log('login status fail');
                }
            }, {scope: Permissions, auth_type: 'rerequest'} );

        };
}

function Generate_longlived_token(FillTextArea, FillPageId, GetAppid, GetAppSecret, userID, accessToken) {
    var $ = jQuery;

    fetch('https://graph.facebook.com/v11.0/oauth/access_token?grant_type=fb_exchange_token&client_id='+GetAppid+'&client_secret='+GetAppSecret+'&fb_exchange_token='+accessToken)
    .then(responsee => responsee.json())
    .then(function(result) {
        
        if(result){
            $(FillTextArea).val(result.access_token).trigger("input");
            fetch('https://graph.facebook.com/'+userID+'/accounts?access_token='+result.access_token )
            .then(response => response.json())
            .then(function(result) {
                if(result){
                    $(FillPageId).val(result.data[0].id).trigger("input");
                }
            });
        }
    });        
}

jQuery(document).on( 'click' , ".tp-feed-delete-transient, .tp-SReview-delete-transient, .tp-table-delete-transient", function() {
    let Self = this;
        ClassName = jQuery(this).attr('class'),
        BlockName = '';

    if(ClassName == 'tp-feed-delete-transient'){
        BlockName = 'SocialFeed';
    }else if(ClassName == 'tp-SReview-delete-transient'){
        BlockName = 'SocialReviews';
    }else if(ClassName == 'tp-table-delete-transient'){
        BlockName = 'Table';
    }  

    let BtnText = Self.textContent;
        Self.innerHTML = '<svg version="1.1" id="L9" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 100 100" enable-background="new 0 0 0 0" xml:space="preserve" ><path fill="#fff" d="M73,50c0-12.7-10.3-23-23-23S27,37.3,27,50 M30.9,50c0-10.5,8.5-19.1,19.1-19.1S69.1,39.5,69.1,50"><animateTransform attributeName="transform" attributeType="XML" type="rotate" dur="1s" from="0 50 50" to="360 50 50" repeatCount="indefinite" /></path></svg>';        
        Self.style.cssText = "padding: 0px; margin-right: 10px; pointer-events:none";

    let AjaxData = {
        action: 'Tp_delete_transient',
        blockName: BlockName,
        delete_transient_nonce: PlusEditor_localize.delete_transient_nonce,
    };

    // setTimeout(function(){
        jQuery.ajax({
            url : PlusEditor_localize.ajax,
            type: 'POST',
            data: AjaxData,
            dataType: "json",
            async: false,
            beforeSend: function() {
            },
            success: function(res){
                var CountTime = new Date( Date.now() + ( 5 * 60 * 1000 ) );

                var x = setInterval(function() {
                        let now = new Date().getTime(), 
                            distance = CountTime - now,
                            minutes = Math.floor( (distance % (1000 * 60 * 60) ) / (1000 * 60) ),
                            seconds = Math.floor( (distance % (1000 * 60) ) / 1000 );

                            Self.innerHTML = minutes + "m " + seconds + "s ";

                        if (distance < 0) {
                            clearInterval(x);
                            Self.innerHTML = BtnText;
                            Self.style.cssText = "padding: 10px; margin-right: 0px; pointer-events:inherit";
                        }
                    }, 1000);

                    setTimeout(function(){ 
                        Self.style.cssText = "padding: 10px; margin-right: 0px; pointer-events:none";
                    }, 1000);

            }
        });
    // }, 1000);
});

/**
 * Create / edit template
 */

var link_path = document.location.origin + document.location.pathname,
    templateDD = document.querySelector('.elementor-control-fp_content_template select'),
    preloadImg = PlusEditor_localize.THEPLUS_ASSETS_URL + '/images/live-editor/pre_loaderr.gif',
    headerLogo = PlusEditor_localize.THEPLUS_ASSETS_URL + '/images/live-editor/tp-logo.png',
    maxIcon = PlusEditor_localize.THEPLUS_ASSETS_URL + '/images/live-editor/Maximize.svg',
    minIcon = PlusEditor_localize.THEPLUS_ASSETS_URL + '/images/live-editor/Minimize.svg',
    closeIcon = PlusEditor_localize.THEPLUS_ASSETS_URL + '/images/live-editor/close.svg';


var leGlob = new Map()
var interval = setInterval(function () {
    var templateDD = document.querySelector('.elementor-control-fp_content_template select');

    if (templateDD) {
        clearInterval(interval);
        
        jQuery(document).on('click', ".tp-live-editor", function () {
            var getSelect = this.closest('.elementor-control-liveeditor').previousSibling.querySelector('select'),
                temp_title = getSelect.selectedOptions[0].text,
                tempVal = getSelect.value;

                document.querySelector('.elementor-editor-active').insertAdjacentHTML('afterbegin',`<div class="tp-live-editor-main"><div class="tp-pre-loader"><img src="${preloadImg}"></div><div class="tp-live-editor-wrapper" style="display:none"><div class="tp-live-editor-header" style="width:80%;"><div class="tp-logo"><img src="${headerLogo}" class="tp-icon"></img><h6>The Plus Addons For Elementor</h6></div><div class="tp-title-input"><input type="text" id="tp-title-le" placeholder="Enter template name..." value="${temp_title}"><button class="tp-title-change-edit">Update Title</button></div><div class="tp_controler"><div class="tp-le-max"><img src="${maxIcon}"></img></div><div class="tp-le-close"><img src="${closeIcon}"></img></div></div></div><div class="tp-live-editor-iframe" style="width:80%;"><iframe src="${link_path}?post=${tempVal}&action=elementor"></iframe></div></div></div>`)

            jQuery('.tp-live-editor-iframe iframe').on('load', function () {
                var tp_le_wrapper = document.querySelectorAll('.tp-live-editor-wrapper'),
                    pre_loader = document.querySelectorAll('.tp-pre-loader'),
                    updtButton = document.querySelector('.tp-title-change-edit');

                if (tp_le_wrapper.length > 0) {
                    pre_loader[0].style.display = "none"
                    tp_le_wrapper[0].style.display = "flex"
                }
                
                document.querySelector("#tp-title-le").addEventListener("focus", function (e) {
                    leGlob.set('titleVal', e.target.value);
                });
                document.querySelector("#tp-title-le").addEventListener("input", function (e) {
                    if (leGlob.get('titleVal') == e.target.value) {
                        leGlob.set('val', false)
                        updtButton.style.background = '#C4C4C4 ';
                    } else {
                        updtButton.style.background = 'white';
                        leGlob.set('val', true)
                    }
                });
                document.querySelector('.tp-title-change-edit').style.background = '#C4C4C4 ';

                jQuery(document).on('click', ".tp-title-change-edit", function () {
                    var title_cur = jQuery('#tp-title-le').val();
                    var data_id = document.querySelector('.elementor-control-fp_content_template select').value;
                    if(leGlob.get('val')){
                        leGlob.set('val', false)
                        jQuery.ajax({
                            type: "POST",
                            url: PlusEditor_localize.ajax,
                            dataType: "JSON",
                            data: {
                                action: "change_current_template_title",
                                security: PlusEditor_localize.live_editor,
                                updated_title: title_cur,
                                id: data_id,
                            },
                            success: function (res) {
                                console.log(res.data.post_title);
                            },
                            error: function (err) {
                                console.log(err);
                            }
                        });
                    }else{
                        updtButton.style.background = '#C4C4C4 ';
                    }
                })

            });
        });
    }
}, 100);

/*Create template */
jQuery(document).on('click', '.tp-live-create', function () {

    jQuery.ajax({
        type: "POST",
        url: PlusEditor_localize.ajax,
        dataType: "JSON",
        data: {
            action: "theplus_template_create",
            security: PlusEditor_localize.live_editor,
        },
        success: function (res) {

            var link_path_live = res.data.url,
                id_live = res.data.id,
                title_live = res.data.title;
            document.querySelector('.elementor-editor-active').insertAdjacentHTML('afterbegin',`<div class="tp-live-editor-main"><div class="tp-pre-loader"><img src="${preloadImg}"></div><div class="tp-live-editor-wrapper" style="display:none"><div class="tp-live-editor-header" style="width:80%;"><div class="tp-logo"><img src="${headerLogo}" class="tp-icon"></img><h6>The Plus Addons For Elementor</h6></div><div class="tp-title-input"><input type="text" id="tp-title-le" placeholder="Enter template name..." value="${title_live}"><button class="tp-title-change" data-id="${id_live}">Update Title</button></div><div class="tp_controler"><div class="tp-le-max"><img src="${maxIcon}"></img></div><div class="tp-le-close"><img src="${closeIcon}"></img></div></div></div><div class="tp-live-editor-iframe" style="width:80%;"><iframe src="${link_path_live}"></iframe></div></div></div>`)

            let getDD = document.querySelector('#elementor-controls .tp-template-create-btn select');
                option = document.createElement('option');
                option.value = id_live
                option.text = title_live;
                getDD.add(option);
                getDD.value = id_live;
                jQuery(getDD).trigger("change");

            jQuery('.tp-live-editor-iframe iframe').on('load', function () {
                var tp_le_wrapper = document.querySelectorAll('.tp-live-editor-wrapper'),
                    pre_loader = document.querySelectorAll('.tp-pre-loader'),
                    updtButton = document.querySelector('.tp-title-change');
                if (tp_le_wrapper.length > 0) {
                    pre_loader[0].style.display = "none"
                    tp_le_wrapper[0].style.display = "flex"
                }
                document.querySelector("#tp-title-le").addEventListener("focus", function (e) {
                    leGlob.set('titleVal', e.target.value);
                });
                document.querySelector("#tp-title-le").addEventListener("input", function (e) {
                    if (leGlob.get('titleVal') == e.target.value) {
                        leGlob.set('val', false)
                        updtButton.style.background = '#C4C4C4 ';
                    } else {
                        updtButton.style.background = 'white';
                        leGlob.set('val', true)
                    }
                });
                document.querySelector('.tp-title-change').style.background = '#C4C4C4 ';
                jQuery(document).on('click', '.tp-title-change', function () {
                    var title = jQuery('#tp-title-le').val(),
                        data_id = jQuery(this).data('id')
                    
                    if(leGlob.get('val')){
                        leGlob.set('val', false)
                        jQuery.ajax({
                            type: "POST",
                            url: PlusEditor_localize.ajax,
                            dataType: "JSON",
                            data: {
                                action: "change_current_template_title",
                                security: PlusEditor_localize.live_editor,
                                updated_title: title,
                                id: data_id,
                            },
                            success: function (res) {
                                console.log(res);
                            },
                            error: function (err) {
                                console.log(err);
                            }
                        });
                    }else{
                        updtButton.style.background = '#C4C4C4 ';
                    }
                })
            });
        },
        error: function (err) {
            console.log(err);
        }
    });

});

jQuery(document).on('click', '.tp-le-close', function () {
    let getDD = document.querySelector('#elementor-controls .tp-template-create-btn select');
    jQuery(getDD).trigger("change");
    document.querySelector('.tp-live-editor-main').remove();
})

jQuery(document).on('click', '.tp-le-max', function () {
    var tp_le_header = document.querySelector('.tp-live-editor-header'),
        tp_le_iframe = document.querySelector('.tp-live-editor-iframe')

    if (tp_le_header.style.width == "80%") {
        document.querySelector('.tp-le-max').innerHTML = '';
        document.querySelector('.tp-le-max').insertAdjacentHTML('afterbegin',`<img src="${minIcon}"></img>`);
        tp_le_header.style.width = "100%";
        tp_le_iframe.style.width = "100%";
        tp_le_iframe.style.height = "100%";

    } else if (tp_le_header.style.width == "100%") {
        document.querySelector('.tp-le-max').innerHTML = '';
        document.querySelector('.tp-le-max').insertAdjacentHTML('afterbegin',`<img src="${maxIcon}"></img>`)
        tp_le_header.style.width = "80%";
        tp_le_iframe.style.width = "80%";
        tp_le_iframe.style.height = "80%";

    }
})