var EaelParticlesHandler = function ($scope, $) {
    var sectionId = $scope.data('id'),
        particle_switch = $scope.data('particle_enable'),
        particle_switch_for_mobile = $scope.data('particle-mobile-disabled'),
        mobile_device_width = 767,
        global_data = [];

        // Checking mobile device disable
        if(particle_switch_for_mobile !== undefined && particle_switch_for_mobile && ($(window).width() <= mobile_device_width)) return;
    
    
        // Checking if the section has enabled particles.
        if (typeof particle_switch == undefined || particle_switch != undefined && particle_switch == false) return;
    
        var preset_theme = $scope.data('preset_theme'),
            custom_style = $scope.data('custom_style'),
            source = $scope.data('eael_ptheme_source'),
            particle_opacity = $scope.data('particle_opacity'),
            particle_speed = $scope.data('particle_speed'),
            settings;
    
        // Checking custo style json is not empty.
        if(source == 'custom' && source == '') return;


    $scope.addClass('eael-particles-section');

    if (window.isEditMode) {
        var editorElements = null,
            particleArgs   = {},
            settings       = {};

        if (!window.elementor.hasOwnProperty('elements')) {
            return false;
        }

        editorElements = window.elementor.elements;

        if (!editorElements.models) {
            return false;
        }

        $.each(editorElements.models, function (i, el) {
            if (sectionId == el.id) {
                particleArgs = el.attributes.settings.attributes;
            } else if (el.id == $scope.closest('.elementor-top-section').data('id')) {
                $.each(el.attributes.elements.models, function (i, col) {
                    $.each(col.attributes.elements.models, function (i, subSec) {
                        particleArgs = subSec.attributes.settings.attributes;
                    });
                });
            }
        });

        settings.switch = particleArgs['eael_particle_switch'];
        settings.themeSource = particleArgs['eael_particle_theme_from'];
        global_data.opacity = particleArgs['eael_particle_opacity']['size'];
        global_data.speed = particleArgs['eael_particle_speed']['size'];

        if (settings.themeSource == 'presets') {
            settings.selected_theme = (localize.ParticleThemesData[particleArgs['eael_particle_preset_themes']]);
        }

        if ((settings.themeSource == 'custom') && ('' !== particleArgs['eael_particles_custom_style'])) {
            settings.selected_theme = particleArgs['eael_particles_custom_style'];
        }

        if (0 !== settings.length) {
            settings = settings;
        }
    }else {
        var div = $('.eael-section-particles-' + sectionId);
        div.each(function() {
            source = $(this).data('eael_ptheme_source');

            if(source == 'presets') {
                themes = JSON.parse(localize.ParticleThemesData[preset_theme]);
            }else {
                themes = (custom_style != '' ? custom_style : undefined);
            }

        var id = $(this).attr('id');
            if(id == undefined) {
                $(this).attr('id', 'eael-section-particles-' + sectionId);
                id = $(this).attr('id');
            }

            themes.particles.opacity.value = particle_opacity;
            themes.particles.move.speed = particle_speed;
            particlesJS(id, themes);
        });
    }

    if (!window.isEditMode || !settings) {
        return false;
    }

    if (settings.switch == 'yes') {
        if (settings.themeSource === 'presets' || settings.themeSource === 'custom' && '' !== settings.selected_theme) {
            if (typeof particlesJS !== 'undefined' && $.isFunction(particlesJS)) {
                $scope.attr('id', 'eael-section-particles-' + sectionId);
                let selected_theme = JSON.parse(settings.selected_theme);
                selected_theme.particles.opacity.value = global_data.opacity;
                selected_theme.particles.move.speed = global_data.speed;
                particlesJS('eael-section-particles-' + sectionId, selected_theme);
                $scope.children('canvas.particles-js-canvas-el').css({
                    position: 'absolute',
                    top: 0
                });
            }
        }
    } else {
        $scope.removeClass('eael-particles-section');
    }

};

jQuery(window).on('elementor/frontend/init', function () {
    elementorFrontend.hooks.addAction( 'frontend/element_ready/section', EaelParticlesHandler );
    elementorFrontend.hooks.addAction( 'frontend/element_ready/container', EaelParticlesHandler );
});
