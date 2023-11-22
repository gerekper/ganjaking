var urlAttuale;
var titoloAttuale;
let dceModalFilled = false;
let dceScrollBlocked = false;
var ajaxPage_init = function (elementSettings, scopeId, $scope) {

    var tid = elementSettings.ajax_page_template;
    if (jQuery('#dce-wrap').length == 0) {
        jQuery('body').addClass('dce-ajax-page-open');
        jQuery('body').wrapInner('<div id="dce-outer-wrap"><div id="dce-wrap"></div></div>');
    }
    $scope.find('.ajax-open[data-id=' + scopeId + ']').on('click', '.dce-wrapper a', function (e) {

        urlAttuale = location.pathname;
        titoloAttuale = document.title;
        jQuery('body').addClass('modal-p-' + scopeId);

        var modale = '<div class="modals-p modals-p-' + scopeId + '"><div class="wrap-p"><div class="modal-p"></div><a href="' + urlAttuale + '" class="close"><span class="dce-quit-ics"></span></a></div></div>';
        var loading = '<div class="load-p"></div>';
        var linkHref = jQuery(this).attr('href');

        jQuery('body').append(modale).append(loading);

        newLocation = linkHref;

        jQuery.ajax({
            url: dceAjaxPath.ajaxurl,
            dataType: "html",
            type: 'POST',
            data: {
                'action': 'modale_action',
                'post_href': linkHref,
                'template_id': tid
            },
            error: function () {
                erroreModale();
            },

            success: function (data, status, xhr) {
                var $result = data;
                riempiModale($result, linkHref, scopeId);
            },

        });
        // -------------------------------------------------
        jQuery('.modals-p .wrap-p').find('.close').on('click', function (e) {
            var linkHref = jQuery(this).attr('href');
            chiudiModale(linkHref, scopeId);
            return false;
        });
        jQuery(document).on('keyup', function (e) {
            if (e.keyCode == 27) { // escape key maps to keycode `27`
                chiudiModale(urlAttuale, scopeId);
            }
        });
        return false;
    });
};

function googleAnalytics_view(path, title, scopeId) {

    ga('set', {page: path, title: title});
    ga('send', 'pageview');
}
function riempiModale(data, url, scopeId) {
    if (0 != data) {
		if (dceModalFilled)
			return;
		dceModalFilled = true;
		dceScrollBlocked = false;
        var posScroll = jQuery('body').scrollTop();


        jQuery('.load-p').remove();
        var pageTitle = jQuery(data).find('.titolo-nativo').text();

        var elementSelected = jQuery(data).filter('.content-p');
        elementSelected.find('.titolo-nativo').remove();

        jQuery('body').addClass('modal-p-on');

        jQuery('.modals-p-' + scopeId + ' .modal-p').html(elementSelected);

        jQuery('body.modal-p-on.modal-p-' + scopeId + ' .wrap-p .modal-p').one('webkitAnimationEnd oanimationend msAnimationEnd animationend', function (el) {
			if (dceScrollBlocked) {
				return;
			}
			dceScrollBlocked = true;
            jQuery('html, body').addClass('no-scroll');
            jQuery('body').addClass('cancella-body');
        });

        var element_el = elementSelected.find('.elementor-element');
        element_el.each(function (i) {
            var el = jQuery(this).data('element_type');
            elementorFrontend.elementsHandler.runReadyTrigger(jQuery(this));
        });

        var stateObj = {url: "bar"};
        if (url != window.location) {
            var elementSettings = dceGetElementSettings(jQuery(this));
            if (elementSettings.change_url) {
                window.history.pushState(null, null, url);
            }
            document.title = pageTitle;
        }
    }
}

function chiudiModale(url, scopeId) {
	dceModalFilled = false;
    jQuery('html, body').removeClass('no-scroll');
    //
    jQuery('body').removeClass('modal-p-on cancella-body').addClass('modal-p-off');
    jQuery('body.modal-p-off.modal-p-' + scopeId + ' .wrap-p .modal-p').one('webkitAnimationEnd oanimationend msAnimationEnd animationend', function (el) {
        jQuery(document).off('keyup');
        jQuery('.modals-p .wrap-p').find('.close').off('click');
        jQuery('.modals-p-' + scopeId).remove();
        //
        jQuery(el.currentTarget).off('webkitAnimationEnd oanimationend msAnimationEnd animationend');
        //
        if (url != window.location) {
            window.history.pushState(null, null, url);
            document.title = titoloAttuale;
        }
    });
    jQuery('body.modal-p-off.modal-p-' + scopeId + ' #dce-wrap').one('webkitAnimationEnd oanimationend msAnimationEnd animationend', function (el) {

        jQuery('body').removeClass('modal-p-off modal-p-' + scopeId);
        jQuery(el.currentTarget).off('webkitAnimationEnd oanimationend msAnimationEnd animationend');

    });
}
function erroreModale() {
    jQuery('.modals-p').html('<p>An error has occurred</p>');
}
function requestContent(file) {
    jQuery('.content').load(file + ' .content');
}

( function( $ ) {
	$( window ).on( 'elementor/frontend/init', function() {
		jQuery('.ajax-open').each(function (i, el) {
			var elementSettings_ajaxOpen = dceGetElementSettings(jQuery(this));
			ajaxPage_init(elementSettings_ajaxOpen, jQuery(this).attr('data-id'), $(this));
		});
	} );
} )( jQuery );
