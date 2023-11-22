/**
 * Start hover video widget script
 */

 (function ($, elementor) {
    'use strict';

    // check video buffer 

    function videoBufferChecker(videoId) {
        var checkInterval = 50.0; // check every 50 ms (do not use lower values)
        var lastPlayPos = 0;
        var currentPlayPos = 0;
        var bufferingDetected = false;
        var player = document.getElementById(videoId);

        setInterval(checkBuffering, checkInterval)

        function checkBuffering() {
            currentPlayPos = player.currentTime;

            // checking offset should be at most the check interval
            // but allow for some margin
            var offset = (checkInterval - 20) / 2000;

            // if no buffering is currently detected,
            // and the position does not seem to increase
            // and the player isn't manually paused...
            if (!bufferingDetected &&
                currentPlayPos < (lastPlayPos + offset) &&
                !player.paused
                ) {
                // console.log("buffering = " + videoId);
            $('#' + videoId).closest('.bdt-hover-video').find('.hover-video-loader').addClass('active');

            bufferingDetected = true;
        }

            // if we were buffering but the player has advanced,
            // then there is no buffering
            if (
                bufferingDetected &&
                currentPlayPos > (lastPlayPos + offset) &&
                !player.paused
                ) {
                // console.log("not buffering anymore = " + videoId);
            $('#' + videoId).closest('.bdt-hover-video').find('.hover-video-loader').removeClass('active');
            bufferingDetected = false
        }
        lastPlayPos = currentPlayPos


    }
}


var widgetDefaultSkin = function ($scope, $) {
    var $instaVideo = $scope.find('.bdt-hover-video'),
    $settings = $instaVideo.data('settings');
    
    if (!$instaVideo.length) {
        return;
    }

    var video = $($instaVideo).find('.bdt-hover-wrapper-list  video');
    var videoProgress;
    setInterval(function () {
        videoProgress = $('.bdt-hover-progress.active');
    }, 100);

    $(video).on('mouseenter click', function (e) {

        if($settings.videoReplay == 'yes'){
            $(this)[0].currentTime = 0; 
        }

        $(this).trigger('play');

        videoBufferChecker($(this).attr('id'));

        var video = $($instaVideo).find('.bdt-hover-video  .bdt-hover-wrapper-list  video');

        var thisId = $(this).attr('id');

        $('#' + thisId).on('ended', function () {
            setTimeout(function (a) {
                $('#' + thisId).trigger('play');

                videoBufferChecker(thisId);

            }, 1500);
        });
    });


    $(video).on('mouseout', function (e) {
        // $(this).trigger('pause');

        if ($settings.posterAgain == 'yes') {
           $(this)[0].currentTime = 0;
           $(this).trigger('load');
        }else{
            $(this).trigger('pause');
        }
    });

    $(video).on('timeupdate', function () {
        var videoBarList = $(video).parent().find('video.active').attr('id');
        var ct = document.getElementById(videoBarList).currentTime;
        var dur = document.getElementById(videoBarList).duration;

        var videoProgressPos = ct / dur;
        $($instaVideo).find('.bdt-hover-bar-list').find("[data-id=" + videoBarList + "]").width(videoProgressPos * 100 + "%");
        $($instaVideo).find('.bdt-hover-btn-wrapper').find(".bdt-hover-progress[data-id=" + videoBarList + "]").width(videoProgressPos * 100 + "%");

            // if (video.ended) {
            // }


        });

    if ($($instaVideo).find('.autoplay').length > 0) {
        $($instaVideo).find(".bdt-hover-wrapper-list  video:first-child").trigger('play');

    }

    if ($($instaVideo).find('.autoplay').length > 0) {
        var playingVideo = $(video).parent().find('video.active');

        $(video).on('timeupdate', function () {
            playingVideo = $(video).parent().find('video.active');
        });

        setInterval(function () {
            $(playingVideo).on('ended', function () {

                var nextVideoId = $(playingVideo).next().attr('id');

                $('#' + nextVideoId).siblings().css("display", 'none').removeClass('active');
                $('#' + nextVideoId).css("display", 'block').addClass('active');

                $('#' + nextVideoId).trigger('play');


                if ($(playingVideo).next('video').length > 0) {
                    var firstVideo = $(playingVideo).siblings().first().attr('id');
                    $($instaVideo).find("[data-id=" + firstVideo + "]").closest('.bdt-hover-bar-list').find('.bdt-hover-progress').width(0 + '%');

                    $($instaVideo).find('.bdt-hover-btn-wrapper').find("[data-id=" + nextVideoId + "]").siblings().removeClass('active');
                    $($instaVideo).find('.bdt-hover-btn-wrapper').find("[data-id=" + nextVideoId + "]").addClass('active');

                    $($instaVideo).find('.bdt-hover-btn-wrapper').find(".bdt-hover-progress").width("0%");

                } else {
                    var firstVideo = $(playingVideo).siblings().first().attr('id');
                        // console.log("Dont exists"+firstVideo);
                        $('#' + firstVideo).siblings().css("display", 'none').removeClass('active');
                        $('#' + firstVideo).css("display", 'block').addClass('active');
                        $($instaVideo).find("[data-id=" + firstVideo + "]").closest('.bdt-hover-bar-list').find('.bdt-hover-progress').width(0 + '%');


                        $($instaVideo).find('.bdt-hover-btn-wrapper').find("[data-id=" + firstVideo + "]").siblings().removeClass('active');
                        $($instaVideo).find('.bdt-hover-btn-wrapper').find("[data-id=" + firstVideo + "]").addClass('active');

                        $($instaVideo).find('.bdt-hover-btn-wrapper').find(".bdt-hover-progress").width("0%");

                        $('#' + firstVideo).trigger('play');

                    }

                });
        }, 1000);

    }



    $('#'+$settings.id).find('.bdt-hover-btn').on('mouseenter click', function () {
        var videoId = $(this).attr('data-id');

        if($settings.videoReplay == 'yes'){
            $('#'+videoId)[0].currentTime = 0;
        }


        $('#' + videoId).trigger('play');

        videoBufferChecker(videoId);

        $('#' + videoId).siblings().css("display", 'none').removeClass('active');
        $('#' + videoId).css("display", 'block').addClass('active');
        $('.bdt-hover-bar-list .bdt-hover-progress').removeClass('active');
        $('.bdt-hover-bar-list').find("[data-id=" + videoId + "]").addClass('active');

        $('.bdt-hover-btn-wrapper').find("[data-id=" + videoId + "]")
        .siblings().removeClass('active');
        $('.bdt-hover-btn-wrapper').find("[data-id=" + videoId + "]")
        .addClass('active');


    });




};

var widgetVideoAccordion = function ($scope, $) {
    var $videoAccordion = $scope.find('.bdt-hover-video'),
    $settings = $videoAccordion.data('settings');

    if (!$videoAccordion.length) {
        return;
    }

    var video = $($videoAccordion).find('.bdt-hover-wrapper-list  video');

    var videoProgress;
    setInterval(function () {
        videoProgress = $('.bdt-hover-progress.active');
    }, 100);

    $(video).on('timeupdate', function () {
        var videoBarList = $(video).parent().find('video.active').attr('id');
        var ct = document.getElementById(videoBarList).currentTime;
        var dur = document.getElementById(videoBarList).duration;
        var videoProgressPos = ct / dur;
        $('.bdt-hover-bar-list').find("[data-id=" + videoBarList + "]").width(videoProgressPos * 100 + "%");
            // if (video.ended) {
            // }
        });

        // start autoplay 
        if ($($videoAccordion).find('.autoplay').length > 0) {
            $($videoAccordion).find(".hover-video-list  video:first-child").trigger('play');
        }

        if ($($videoAccordion).find('.autoplay').length > 0) {
            var playingVideo = $(video).parent().find('video.active');

            $(video).on('timeupdate', function () {
                playingVideo = $(video).parent().find('video.active');
            });

            setInterval(function () {
                $(playingVideo).on('ended', function () {

                    var nextVideoId = $(playingVideo).next().attr('id');

                    $('#' + nextVideoId).siblings().css("display", 'none').removeClass('active');
                    $('#' + nextVideoId).css("display", 'block').addClass('active');

                    // console.log('playingVideo = '+ $(playingVideo).attr('id'));

                    $('#' + nextVideoId).trigger('play');

                    if ($(playingVideo).next('video').length > 0) {
                        // console.log("Exists");
                        var firstVideo = $(playingVideo).siblings().first().attr('id');
                        $($videoAccordion).find("[data-id=" + firstVideo + "]").closest('.bdt-hover-bar-list').find('.bdt-hover-progress').width(0 + '%');

                    } else {
                        var firstVideo = $(playingVideo).siblings().first().attr('id');
                        // console.log("Dont exists"+firstVideo);
                        $('#' + firstVideo).siblings().css("display", 'none').removeClass('active');
                        $('#' + firstVideo).css("display", 'block').addClass('active');
                        $($videoAccordion).find("[data-id=" + firstVideo + "]").closest('.bdt-hover-bar-list').find('.bdt-hover-progress').width(0 + '%');



                        $('#' + firstVideo).trigger('play');
                    }

                });
            }, 1000);

        }
        // end autoplay 
        
        $('#'+$settings.id).find('.bdt-hover-mask-list .bdt-hover-mask').on('mouseenter click', function () {
            var videoId = $(this).attr('data-id');
            $('#' + videoId).siblings().css("display", 'none').removeClass('active');
            $('#' + videoId).css("display", 'block').addClass('active');
            $('#' + videoId).siblings().trigger('pause'); // play item on active
            
            if($settings.videoReplay == 'yes'){
                $('#'+videoId)[0].currentTime = 0;
                
            }
            
            $('#' + videoId).trigger('play'); // play item on active

            videoBufferChecker(videoId);

            $('.bdt-hover-bar-list .bdt-hover-progress').removeClass('active');
            $('.bdt-hover-bar-list').find("[data-id=" + videoId + "]").addClass('active');

            $('.bdt-hover-mask-list').find("[data-id=" + videoId + "]")
            .siblings().removeClass('active');
            $('.bdt-hover-mask-list').find("[data-id=" + videoId + "]")
            .addClass('active');


            $('#' + videoId).on('ended', function () {
                setTimeout(function (a) {
                    $('#' + videoId).trigger('play');

                    videoBufferChecker(videoId);

                }, 1500);
            });


        });
        $('.bdt-hover-mask-list').on('mouseout', function (e) {
            // $(this).siblings('.bdt-hover-wrapper-list .hover-video-list').find('video').trigger('pause');
            if ($settings.posterAgain == 'yes') {
                     $(this).siblings('.bdt-hover-wrapper-list .hover-video-list').find('video')[0].currentTime = 0;
                     $(this).siblings('.bdt-hover-wrapper-list .hover-video-list').find('video').trigger('load');
            } else {
                $(this).siblings('.bdt-hover-wrapper-list .hover-video-list').find('video').trigger('pause');
            }
        });


    };  
    
    
    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-hover-video.default', widgetDefaultSkin);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-hover-video.accordion', widgetVideoAccordion);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-hover-video.vertical', widgetVideoAccordion);
    });
}(jQuery, window.elementorFrontend));

/**
 * End hover video widget script
 */