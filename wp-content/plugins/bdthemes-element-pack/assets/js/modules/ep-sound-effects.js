;
(function ($, elementor) {
    $(window).on('elementor/frontend/init', function () {
        var ModuleHandler = elementorModules.frontend.handlers.Base,
            SoundEffects;

        SoundEffects = ModuleHandler.extend({

            bindEvents: function () {
                this.run();
            },

            getDefaultSettings: function () {
                return {
                    event: 'hover',
                };
            },

            onElementChange: debounce(function (prop) {
                if (prop.indexOf('ep_sound_effects_') !== -1) {
                    this.run();
                }
            }, 400),

            settings: function (key) {
                return this.getElementSettings('ep_sound_effects_' + key);
            },

            run: function () {
                if (this.settings('active') != 'yes') {
                    return;
                }
                var options = this.getDefaultSettings(),
                    $element = '',
                    $widgetId = 'ep-sound-effects' + this.getID(),
                    $widgetIdSelect = '#' + $widgetId,
                    $soundAudioSource, $soundAudioSourceMp3;

                // selector creating...
                $(this.findElement('.elementor-widget-container').get(0)).attr('id', $widgetId);

                if (this.settings('select_type') === 'widget') {
                    $element = $($widgetIdSelect);
                } else if (this.settings('select_type') === 'anchor_tag') {
                    $element = $($widgetIdSelect + ' a');
                } else if (this.settings('select_type') === 'custom') {
                    if (this.settings('element_selector')) {
                        $element = $($widgetIdSelect).find(this.settings('element_selector'));
                    }
                } else {
                    // nothing...
                }

                // audio source

                if (this.settings('source') !== 'hosted_url') {
                    $soundAudioSource = this.settings('source_local_link') + this.settings('source');
                } else {
                    if (this.settings('hosted_url.url')) {
                        $soundAudioSource = this.settings('hosted_url.url').replace(/\.[^/.]+$/, "");
                        $soundAudioSourceMp3 = this.settings('hosted_url_mp3.url').replace(/\.[^/.]+$/, "");
                    }
                }

                if (!$soundAudioSource) {
                    return;
                }

                if (!document.createElement('audio').canPlayType) {
                    console.error('Oh man 😩! \nYour browser doesn\'t support audio awesomeness.');
                    return function () {}; // return an empty function if `loudLinks` is called again.
                }

                // Create audio element and make it awesome
                var audioPlayer = document.createElement('audio'),
                    mp3Source = document.createElement('source'),
                    oggSource = document.createElement('source'),
                    eventsSet = false;

                audioPlayer.setAttribute('preload', true); // audio element preload attribute

                mp3Source.setAttribute('type', 'audio/mpeg');
                oggSource.setAttribute('type', 'audio/ogg');

                // appending the sources to the player element
                audioPlayer.appendChild(mp3Source);
                audioPlayer.appendChild(oggSource);

                // appending audioplayer to body
                document.body.appendChild(audioPlayer);

                // Play audio
                function playAudio() {

                    // get the audio source and appending it to <audio>
                    var audioSrc = $soundAudioSource, //'http://192.168.1.100/cat-meow',
                        soundMp3Link,
                        soundOggLink;

                    if (!audioSrc) {
                        return;
                    }

                    if ($soundAudioSourceMp3) {
                        soundMp3Link = $soundAudioSourceMp3 + '.mp3';
                    }

                    soundOggLink = audioSrc + '.ogg';

                    if (!eventsSet) {
                        eventsSet = true;
                        // mp3Source.addEventListener('error', function () {
                        //     console.error('😶 D\'oh! The mp3 file URL is wrong!');
                        // });
                        oggSource.addEventListener('error', function () {
                            console.error('😶 D\'oh! The ogg file URL is wrong!');
                        });
                    }

                    // Only reset `src` and reload if source is different
                    if (soundMp3Link || soundOggLink) {
                        if ($soundAudioSourceMp3) {
                            mp3Source.setAttribute('src', soundMp3Link);
                        }
                        oggSource.setAttribute('src', soundOggLink);

                        audioPlayer.load();

                    }

                    audioPlayer.play();

                }

                // Stop audio
                function stopAudio() {
                    audioPlayer.pause();
                    audioPlayer.currentTime = 0; // reset to beginning
                }

                var initScope = this;

                jQuery(document).ready(function () {
                    //hover
                    if (initScope.settings('event') == 'hover') {

                        jQuery($element).on('mouseenter', function () {
                            playAudio();
                        });
                        jQuery($element).on('mouseleave', function () {
                            stopAudio();
                        });
                        // jQuery($element).on('touchmove', function () {
                        //     stopAudio();
                        // });
                        jQuery($element).on('click', function () {
                            stopAudio();
                        });

                        jQuery($element).on('touchstart', function () {
                            playAudio();
                        });

                    }

                    // click
                    if (initScope.settings('event') == 'click') {
                        $($element).on('click', function () {
                            playAudio();
                        });
                    }

                });

            }

        });

        elementorFrontend.hooks.addAction('frontend/element_ready/widget', function ($scope) {
            elementorFrontend.elementsHandler.addHandler(SoundEffects, {
                $element: $scope
            });
        });

    });

})(jQuery, window.elementorFrontend);