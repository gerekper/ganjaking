/**
 * Start tags cloud widget script
 */

(function($, elementor) {

    'use strict';

    var widgetTagsCloud = function($scope, $) {
        var $tags_cloud = $scope.find('.bdt-tags-cloud');
            
            if (!$tags_cloud.length) {
                return;
            }
            var $settings = $tags_cloud.data('settings');
            var $tags_color = $settings.basic_tags_bg_type;
            var tags_color_solid = $settings.basic_tags_solid_bg;
 

            jQuery.fn.prettyTag = function (options) {

                var setting = jQuery.extend({
                        randomColor: true, //false to off random color
                        tagicon: true, //false to turn off tags icon
                        tags_color: options.tags_color
                    }, options);


                return this.each(function () {
                    var target = this;
                        //add font awesome icon
                        if (setting.tagicon == true) {
                            var eachTag = $(target).find("a");
                            var ti = document.createElement("i");
                            $($tags_cloud).find(ti).addClass("fas fa-tags").prependTo(eachTag);
                        }

                        if( setting.tags_color == 'random' ){
                            coloredTags();
                        }else{
                            if (typeof(tags_color_solid) != "undefined"){
                                $($tags_cloud).find('.bdt-tags-list li a').css('background-color', tags_color_solid); 
                            }else{
                               $($tags_cloud).find('.bdt-tags-list li a').css('background-color', '#3FB8FD'); 
                           }
                       }

                        //function to make tags colorful
                        function coloredTags() {

                        var totalTags = $($tags_cloud).find("li").length; //to find total cloud tags
                        // console.log(totalTags);
                        var mct = $($tags_cloud).find("a"); //select all tags links to make them colorful
                        /*Array of Colors */
                        var tagColor = ["#ff0084", "#ff66ff", "#43cea2", "#D38312", "#73C8A9", "#9D50BB",
                        "#780206", "#FF4E50", "#ADD100",
                        "#0F2027", "#00c6ff", "#81D8D0", "#5CB3FF", "#95B9C7", "#C11B17", "#3B9C9C", "#FF7F50", "#FFD801", "#79BAEC", "#F660AB", "#3D3C3A", "#3EA055"
                        ];

                        var tag = 0;
                        var color = 0; //assign colors to tags with loop, unlimited number of tags can be added
                        do {
                            if (color > 21) {
                                color = 0;
                        } //Start again array index if it reaches at last

                        if (setting.randomColor == true) {
                            var $rc = Math.floor(Math.random() * 22);
                            $(mct).eq(tag).css({
                            //tags random color
                            'background': tagColor[$rc]
                        });
                        } else {
                            $(mct).eq(tag).css({
                        //tags color in a sequence
                        'background': tagColor[color]
                    });
                        }
                        tag++;
                        color++;
                    } while (tag <= totalTags)

                }
            });
            };


            /*   End */

            $($tags_cloud).find(".bdt-tags-list").prettyTag({'tags_color': $tags_color});

        };


        var widgetSkinAnimated = function($scope, $) {
            var $tags_globe = $scope.find('.bdt-tags-cloud');
            if (!$tags_globe.length) {
                return;
            }
            var $settings = $tags_globe.data('settings');
 
                try {
                    TagCanvas.Start($settings.idmyCanvas, $settings.idTags, { 
                        textColour         :  $settings.textColour,
                        outlineColour      :  $settings.outlineColour,
                        reverse            :  true,
                        depth              :  $settings.depth, 
                        maxSpeed           :  $settings.maxSpeed, 
                        activeCursor       :  $settings.activeCursor,
                        bgColour           :  $settings.bgColour, 
                        bgOutlineThickness :  $settings.bgOutlineThickness, 
                        bgRadius           :  $settings.bgRadius, 
                        dragControl        :  $settings.dragControl, 
                        fadeIn             :  $settings.fadeIn, 
                        freezeActive       :  $settings.freezeActive,
                        outlineDash        :  $settings.outlineDash,
                        outlineDashSpace   :  $settings.globe_outline_dash_space,
                        outlineDashSpeed   :  $settings.globe_outline_dash_speed,
                        outlineIncrease    :  $settings.outlineIncrease,
                        outlineMethod      :  $settings.outlineMethod, 
                        outlineRadius      :  $settings.outlineRadius,
                        outlineThickness   :  $settings.outlineThickness,
                        shadow             :  $settings.shadow,
                        shadowBlur         :  $settings.shadowBlur,
                        wheelZoom          :  $settings.wheelZoom

                    });
                } catch (e) {
                    document.getElementById($settings.idCanvas).style.display = 'none';
                }
           
        };


        var widgetSkinCloud = function($scope, $) {
            var $tags_cloud = $scope.find('.bdt-tags-cloud');

            if (!$tags_cloud.length) {
                return;
            }
            var $settings = $tags_cloud.data('settings');

            jQuery(document).ready(function($) {
                function resizeAwesomeCloud() {
                    jQuery("#"+$settings.idCloud).awesomeCloud({
                        "size": {
                            "grid": 9,
                            "factor": 1
                        },
                        "color" : {
                        "background" : "rgba(156,145,255,0)", // background color, transparent by default
                        // "background" : "rgba(156,145,255,0)", // background color, transparent by default
                        "start" : "#20f", // color of the smallest font, if options.color = "gradient""
                        "end" : "rgb(200,0,0)" // color of the largest font, if options.color = "gradient"
                    },
                    "options": {
                        "background" :"rgba(165,184,255,0)",
                        "color": $settings.cloudColor,  
                            "sort": "highest" // highest, lowest or random
                        },
                        "font": "'Times New Roman', Times, serif",
                        "shape": $settings.cloudStyle // default 
                    });
                }
                resizeAwesomeCloud();
                jQuery(window).on("resize", function($) { 
                    jQuery($tags_cloud).find('#awesomeCloud'+$settings.idCloud).remove();
                    resizeAwesomeCloud();
                });
            });

        };


        jQuery(window).on('elementor/frontend/init', function() {
            elementorFrontend.hooks.addAction('frontend/element_ready/bdt-tags-cloud.default', widgetTagsCloud);
            elementorFrontend.hooks.addAction('frontend/element_ready/bdt-tags-cloud.bdt-animated', widgetSkinAnimated); 
            elementorFrontend.hooks.addAction('frontend/element_ready/bdt-tags-cloud.bdt-cloud', widgetSkinCloud); 
        });

    }(jQuery, window.elementorFrontend));

/**
 * End tags cloud widget script
 */

 