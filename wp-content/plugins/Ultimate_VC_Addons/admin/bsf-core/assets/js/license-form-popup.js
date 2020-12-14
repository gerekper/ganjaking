(function($){

	BSFCoreLicenseForm = {

		/**
		 * Init
		 */
		init: function()
		{
			this._showFormOnLoad();
			this._bind();
		},
		
		/**
		 * Binds events
		 */
		_bind: function()
		{
			$( document ).on('click', '.bsf-core-license-form-btn', BSFCoreLicenseForm._showFormOnClick);
			$( document ).on('click', '.bsf-core-license-form-close-btn', BSFCoreLicenseForm._closeForm);
			$( document ).on('click', '.bsf-core-license-form .bsf-core-license-form-overlay', BSFCoreLicenseForm._closeForm);
		},

		_changeURL: function( url )
		{
			History.pushState(null, null, url);
		},

		/**
		 * Show form on Load
		 */
		_showFormOnLoad: function( e )
		{
			if( BSFCoreLicenseForm._getParamFromURL('bsf-inline-license-form') ) {
				var slug = BSFCoreLicenseForm._getParamFromURL('bsf-inline-license-form');
				BSFCoreLicenseForm._showForm( slug );
			} 
		},

		/**
		 * Show form on Click
		 */
		_showFormOnClick: function( e )
		{
			// don't override click action if the link is not from the popup form.
			var licenseFormURl = $( this ).attr('href') || '';

			if ( null !==  BSFCoreLicenseForm._getParamFromURL('bsf-inline-license-form', licenseFormURl) ||
				true === $( this ).hasClass('bsf-core-plugin-link') ) {
				e.preventDefault();
				var slug = $( this ).attr('plugin-slug') || '';
				var url_params = {'bsf-inline-license-form':slug};
				BSFCoreLicenseForm._showForm( slug );

				// Change URL.
				if( ! BSFCoreLicenseForm._getParamFromURL('bsf-inline-license-form') ) {
					var current_url = window.location.href;
					var current_url_separator = ( window.location.href.indexOf( "?" ) === -1 ) ? "?" : "&";

					var new_url = current_url + current_url_separator + decodeURIComponent( $.param( url_params ) );
					
					BSFCoreLicenseForm._changeURL( new_url );
				}
			}

			

		},

		/**
		 * Show form by slug
		 */
		_showForm: function( slug )
		{
			if( $(".bsf-core-license-form[plugin-slug='"+slug+"']").length ) {
				$(".bsf-core-license-form[plugin-slug='"+slug+"']").show();
				$('body').addClass('bsf-core-license-form-open');
			}
		},

		/**
		 * Close form.
		 */
		_closeForm: function( e )
		{
			e.preventDefault();

			$('.bsf-core-license-form').hide();
			$('body').removeClass('bsf-core-license-form-open');

			if( BSFCoreLicenseForm._getParamFromURL('bsf-inline-license-form') ) {

				var url_params = BSFCoreLicenseForm._getQueryStrings();
				delete url_params['bsf-inline-license-form'];
				delete url_params['bsf-inline-license-form'];
				delete url_params['license_action'];
				delete url_params['token'];
				delete url_params['product_id'];
				delete url_params['purchase_key'];
				delete url_params['success'];
				delete url_params['status'];
				delete url_params['message'];
				delete url_params['debug'];
				delete url_params['activation_method'];

				var current_url = window.location.href;
				var root_url = current_url.substr(0, current_url.indexOf('?')); 
				if( jQuery.isEmptyObject( url_params ) ) {
					var new_url = root_url + decodeURIComponent( $.param( url_params ) );
				} else {
					var current_url_separator = ( root_url.indexOf( "?" ) === -1 ) ? "?" : "&";
					var new_url = root_url + current_url_separator + decodeURIComponent( $.param( url_params ) );
				}

				// Change URL.
				BSFCoreLicenseForm._changeURL( new_url );
			}
		},

		/**
		 * Get URL param.
		 */
		_getParamFromURL: function(name, url)
		{
		    if (!url) url = window.location.href;
		    name = name.replace(/[\[\]]/g, "\\$&");
		    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
		        results = regex.exec(url);
		    if (!results) return null;
		    if (!results[2]) return '';
		    return decodeURIComponent(results[2].replace(/\+/g, " "));
		},

		/**
		 * Get query strings.
		 */
		_getQueryStrings( str )
		{
			return (str || document.location.search).replace(/(^\?)/,'').split("&").map(function(n){return n = n.split("="),this[n[0]] = n[1],this}.bind({}))[0];
		}

	};

	/**
	 * Initialization
	 */
	$(function(){
		BSFCoreLicenseForm.init();
	});

})(jQuery);