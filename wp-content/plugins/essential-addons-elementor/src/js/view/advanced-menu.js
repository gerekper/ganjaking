var AdvancedMenu = function ($scope, $) {
    var $indicator_class = $('.eael-advanced-menu-container', $scope).data(
        'indicator-class'
    )

    let $hamburger_icon = $('.eael-advanced-menu-container', $scope).data(
        'hamburger-icon'
    )

    var $dropdown_indicator_class = $( '.eael-advanced-menu-container', $scope ).data('dropdown-indicator-class');

    var $horizontal = $('.eael-advanced-menu', $scope).hasClass(
        'eael-advanced-menu-horizontal'
    )

    let $hamburger_breakpoints = $('.eael-advanced-menu-container', $scope).data(
        'hamburger-breakpoints'
    )
    let $hamburger_device = $('.eael-advanced-menu-container', $scope).data(
        'hamburger-device'
    )

    if( typeof $hamburger_device === 'undefined' || $hamburger_device === '' || $hamburger_device === null ) {
        $hamburger_device = 'tablet';
    }

    let selectorByType = $horizontal ? '.eael-advanced-menu-horizontal' : '.eael-advanced-menu-vertical';
    let $hamburger_max_width = getHamburgerMaxWidth($hamburger_breakpoints, $hamburger_device);
    var $fullWidth = $('.eael-advanced-menu--stretch');
    let all_ids = [];

    // add menu active class
    $('.eael-advanced-menu li a', $scope).each(function () {
        let $this = $(this),
            hashURL = $this.attr('href'),
            thisURL = hashURL,
            isStartWithHash,
            splitURL = thisURL !== undefined ? thisURL.split('#') : [];

        hashURL = hashURL === undefined ? '' : hashURL;
        isStartWithHash = hashURL.startsWith('#');

        if ( hashURL !== '#' && splitURL.length > 1 && localize.page_permalink === splitURL[0] && splitURL[1] ){
            all_ids.push(splitURL[1]);
        }
        if ( !isStartWithHash && localize.page_permalink === thisURL ) {
            $this.addClass('eael-item-active');
        }
    });

    $(window).on('load resize scroll', function() {
        if ( all_ids.length > 0 ){
            $.each(all_ids,function (index, item){
                if ($('#'+item).isInViewport()) {
                    $('a[href="'+localize.page_permalink+'#'+item+'"]', $scope).addClass('eael-menu-'+item+' eael-item-active');
                } else {
                    $('.eael-menu-'+item).removeClass('eael-menu-'+item+' eael-item-active');
                }
            });
        }

    });

    if($horizontal){
        // insert indicator
        $('.eael-advanced-menu > li.menu-item-has-children', $scope).each(
            function () {
                $('> a', $(this)).append(
                    `<span>${$indicator_class}</span>`
                )
            }
        )

        $('.eael-advanced-menu > li ul li.menu-item-has-children', $scope).each(
            function () {
                $('> a', $(this)).append(
                    `<span class="eael-dropdown-indicator">${$dropdown_indicator_class}</span>`
                )
            }
        )
    }

    // insert responsive menu toggle, text
    $(selectorByType, $scope).before('<span class="eael-advanced-menu-toggle-text"></span>');
    eael_menu_resize($hamburger_max_width);

    // responsive menu slide
    $('.eael-advanced-menu-container', $scope).on(
        'click',
        '.eael-advanced-menu-toggle',
        function (e) {
            e.preventDefault()
            const $siblings = $(this).siblings('nav').children(selectorByType);

            $siblings.css('display') == 'none'
                ? $siblings.slideDown(300)
                : $siblings.slideUp(300)
        }
    )

    // clear responsive props
    $(window).on('resize load', function () {
        eael_menu_resize($hamburger_max_width);
    });

    function eael_menu_resize( max_width_value = 0 ) {
        if (window.matchMedia('(max-width: '+ max_width_value +'px)').matches) {
            $(selectorByType, $scope).addClass(
                'eael-advanced-menu-responsive'
            )
            $('.eael-advanced-menu-toggle-text', $scope).text(
                $(
                    selectorByType + ' .current-menu-item a',
                    $scope
                )
                    .eq(0)
                    .text()
            )

            // Mobile Dropdown Breakpoints
            $('.eael-advanced-menu-container', $scope).closest('.elementor-widget-eael-advanced-menu')
                .removeClass('eael-hamburger--not-responsive')
                .addClass('eael-hamburger--responsive');

            if ($fullWidth) {
                const css = {}
                if (!$(selectorByType, $scope).parent().hasClass('eael-nav-menu-wrapper')) {
                    $(selectorByType, $scope).wrap('<nav class="eael-nav-menu-wrapper"></nav>');
                }
                const $navMenu = $(".eael-advanced-menu-container nav", $scope);
                menu_size_reset($navMenu);


                if ($fullWidth.length > 0) {
                    css.width = parseFloat($('.elementor').width()) + 'px';
                    css.left = -parseFloat($navMenu.offset().left) + 'px';
                    css.position = 'absolute';
                }
                $navMenu.css(css);
            }
        } else {
            $(selectorByType, $scope).removeClass(
                'eael-advanced-menu-responsive'
            );
            $(
                selectorByType + ', ' + selectorByType + ' ul',
                $scope
            ).css('display', '');
            $(".eael-advanced-menu-container nav",$scope).removeAttr( 'style' );
                
            // Mobile Dropdown Breakpoints
            $('.eael-advanced-menu-container', $scope).closest('.elementor-widget-eael-advanced-menu')
                .removeClass('eael-hamburger--responsive')
                .addClass('eael-hamburger--not-responsive');
            }
    }

    function menu_size_reset(selector) {
        const css = {};
        css.width = '';
        css.left = '';
        css.position = 'inherit';
        selector.css(css);
    }

    function getHamburgerMaxWidth($breakpoints, $device) {
        let $max_width = 0;
        if( $device === 'none' || typeof $device === 'undefined' || $device === '' || $device === null ){
            return $max_width;
        }

        for (let $key in $breakpoints) {
            if ($key == $device) {
                $max_width = $breakpoints[$key];
            }
        }
        // fetch max width value from string like 'Mobile (> 767px)' to 767
        $max_width = $max_width.replace(/[^0-9]/g, '');
        return $max_width;
    }

    $('.eael-advanced-menu > li.menu-item-has-children', $scope).each(
        function () {
            // indicator position
            var $height = parseInt($('a', this).css('line-height')) / 2
            $(this).append(
                `<span class="eael-advanced-menu-indicator" style="top: ${$height}px">${$indicator_class}</span>`
            )

            // if current, keep indicator open
            // $(this).hasClass('current-menu-ancestor') ? $(this).addClass('eael-advanced-menu-indicator-open') : ''
        }
    )

    $('.eael-advanced-menu > li ul li.menu-item-has-children', $scope).each(
        function (e) {
            // indicator position
            var $height = parseInt($('a', this).css('line-height')) / 2
            $(this).append(
                `<span class="eael-advanced-menu-indicator eael-dropdown-indicator" style="top: ${$height}px">${$dropdown_indicator_class}</span>`
            )

            // if current, keep indicator open
            // $(this).hasClass('current-menu-ancestor') ? $(this).addClass('eael-advanced-menu-indicator-open') : ''
        }
    )

    // menu indent
    $(
        '.eael-advanced-menu-dropdown-align-left .eael-advanced-menu-vertical li.menu-item-has-children'
    ).each(function () {
        var $padding_left = parseInt($('a', $(this)).css('padding-left'))

        $('ul li a', this).css({
            'padding-left': $padding_left + 20 + 'px',
        })
    })

    $(
        '.eael-advanced-menu-dropdown-align-right .eael-advanced-menu-vertical li.menu-item-has-children'
    ).each(function () {
        var $padding_right = parseInt($('a', $(this)).css('padding-right'))

        $('ul li a', this).css({
            'padding-right': $padding_right + 20 + 'px',
        })
    })

    $(
        '.eael-advanced-menu-vertical li.menu-item-has-children.current-menu-ancestor .eael-advanced-menu-indicator'
    ).each(function () {
        // ToDo Alternate way: check eael_advanced_menu_submenu_expand settings and expand if enabled
        let isMenuOpen = $(this).siblings('ul.sub-menu').css('display');
        if (isMenuOpen !== 'none') {
            $(this).toggleClass('eael-advanced-menu-indicator-open');
        }
    });

    $('.eael-advanced-menu', $scope).on('click', 'a[href="#"]', function (e) {
        e.preventDefault();
        $(this).siblings('.eael-advanced-menu-indicator').trigger('click');
    });

    // menu dropdown toggle
    $('.eael-advanced-menu', $scope).on( 'click', '.eael-advanced-menu-indicator', function (e) {
            e.preventDefault();
            $(this).toggleClass('eael-advanced-menu-indicator-open')
            $(this).hasClass('eael-advanced-menu-indicator-open')
                ? $(this).siblings('ul').slideDown(300)
                : $(this).siblings('ul').slideUp(300)
            $('.eael-advanced-menu-indicator-open').not($(this).parents('.menu-item-has-children').children('span')).removeClass('eael-advanced-menu-indicator-open').siblings('ul').slideUp(300);
        }
    );

    // main menu toggle
    $('.eael-advanced-menu-container', $scope).on(
        'click',
        '.eael-advanced-menu-responsive li a:not([href="#"])',
        function (e) {
            $(this).parents(selectorByType).slideUp(300)
        }
    )

    if ( elementorFrontend.isEditMode() ) {
		elementor.channels.editor.on( 'change', function( view ) {
			let changed = view.elementSettingsModel.changed;
			if ( changed.eael_advanced_menu_dropdown ) {
                elementor.saver.update.apply().then(function () {
                    elementor.reloadPreview();
                });
                // let updated_max_width = getHamburgerMaxWidth( $hamburger_breakpoints, changed.eael_advanced_menu_dropdown );
				// eael_menu_resize( updated_max_width );

                // $hamburger_max_width = updated_max_width;
			}
		});
	}
}

jQuery(window).on('elementor/frontend/init', function () {

    if (ea.elementStatusCheck('eaelAdvancedMenu')) {
        return false;
    }

    elementorFrontend.hooks.addAction(
        'frontend/element_ready/eael-advanced-menu.default',
        AdvancedMenu
    )
    elementorFrontend.hooks.addAction(
        'frontend/element_ready/eael-advanced-menu.skin-one',
        AdvancedMenu
    )
    elementorFrontend.hooks.addAction(
        'frontend/element_ready/eael-advanced-menu.skin-two',
        AdvancedMenu
    )
    elementorFrontend.hooks.addAction(
        'frontend/element_ready/eael-advanced-menu.skin-three',
        AdvancedMenu
    )
    elementorFrontend.hooks.addAction(
        'frontend/element_ready/eael-advanced-menu.skin-four',
        AdvancedMenu
    )
    elementorFrontend.hooks.addAction(
        'frontend/element_ready/eael-advanced-menu.skin-five',
        AdvancedMenu
    )
    elementorFrontend.hooks.addAction(
        'frontend/element_ready/eael-advanced-menu.skin-six',
        AdvancedMenu
    )
    elementorFrontend.hooks.addAction(
        'frontend/element_ready/eael-advanced-menu.skin-seven',
        AdvancedMenu
    )
})