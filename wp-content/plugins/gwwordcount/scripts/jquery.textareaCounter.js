/**
 * jQuery.textareaCounter
 * Version 1.0
 * Copyright (c) 2011 c.bavota - http://bavotasan.com
 * Dual licensed under MIT and GPL.
 * Date: 10/20/2011
**/
(function($){
    
    $.fn.textareaCounter = function( options ) {

        var defaults = {
            showCount: true,
            limit:     100,
            min:       0,
            truncate:  true
        };
        
        var self          = this,
            previousCount = 0;

        options = $.extend( defaults, options );

        self.updateWordCount = function( $input ) {

            if( options.useRichTextEditor ) {

                var editor      = tinymce.get( $input.attr( 'id' ) ),
                    text        = editor ? editor.getContent( { format: 'text' } ) : '',
                    $plainInput = self.getPlainTextInput( gformGetFieldId( $input ) );

                $plainInput.val( text );

            } else {
                var text = $.trim( $input.val() );
            }

            var words         = text.match( /\S+/g ),
                wordCount     = gform.applyFilters( 'gpwc_word_count', words == null ? 0 : words.length, text, $input ),
                origWordCount = wordCount,
                label         = '';

            if( options.min && options.limit && wordCount == 0 ) {

                label  = self.prepareLabel( [ options.minDefaultLabelSingular, options.minDefaultLabel ], options.limit, options.min, wordCount );
                label += ', ' + self.prepareLabel( [ options.defaultLabelSingular, options.defaultLabel ], options.limit, options.min, wordCount );

            } else if( options.min && ( options.limit ? wordCount < options.min : true ) ) {

                if( wordCount == 0 ) {
                    label = [ options.minDefaultLabelSingular, options.minDefaultLabel ];
                } else if( wordCount < options.min ) {
                    label = [ options.minCounterLabelSingular, options.minCounterLabel ];
                } else {
                    label = options.minReachedLabel;
                }

            } else {

                if( wordCount == 0 ) {
                    label = [ options.defaultLabelSingular, options.defaultLabel ];
                } else if( wordCount >= options.limit ) {

                    if( options.truncate && ! options.useRichTextEditor ) {

                        wordCount = options.limit;
                        label     = options.limitReachedLabel;

                        $input.val( self.truncate( text ) );

                    } else {
                        label = wordCount > options.limit ? options.limitExceededLabel : options.limitReachedLabel;
                    }

                } else {
                    label = [ options.counterLabelSingular, options.counterLabel ];
                }

            }

            self.getCounterLabel( $input ).html( self.prepareLabel( label, options.limit, options.min, wordCount ) );

            if( previousCount != wordCount ) {
                $( document ).trigger( 'textareaCounterUpdate', [ wordCount, $input, origWordCount ] );
            }
            
            previousCount = wordCount;

        };

        self.prepareLabel = function( label, max, min, wordCount ) {

            var remaining, count;

            max = parseInt( max );
            min = parseInt( min );
            wordCount = parseInt( wordCount );

            if( min && wordCount < min ) {
                remaining = min - wordCount;
            } else {
                remaining = max - wordCount;
            }

            if( typeof label != 'string' ) {
                if( label[0].match( '{min}' ) ) {
                    count = min;
                } else if( label[0].match( '{max}|{limit}' ) ) {
                    count = max;
                } else if( label[0].match( '{remaining}' ) ) {
                    count = remaining;
                } else {
                    count = wordCount;
                }
                label = count > 1 ? label[1] : label[0];
            }

            return label
                    .replace( '{limit}', max )
                    .replace( '{max}', max )
                    .replace( '{min}', min )
                    .replace( '{remaining}', remaining )
                    .replace( '{count}', wordCount );
        };

        self.truncate = function( text ) {

            text = $.trim( text );

            var words        = text.match( /\S+/g ),
                whiteSpace   = text.split( /\S+/g ),
                limitedWords = words.slice( 0, options.limit ),
                limitedText  = '';

            for( var i = 0; i < limitedWords.length; i++ ) {
                limitedText += limitedWords[i] + whiteSpace[ i + 1 ];
            }

            return limitedText;
        };

        self.getPlainTextInput = function( fieldId ) {

            if( typeof self.plainTextInputs == 'undefined' ) {
                self.plainTextInputs = {};
            }

            if( typeof self.plainTextInputs[ fieldId ] == 'undefined' ) {

                var $plainTextInput = jQuery( '<textarea id="gpwc_plain_text_' + options.formId + '_' + fieldId + '" name="gpwc_plain_text_' + fieldId + '" style="display:none;"></textarea>' );
                $plainTextInput.appendTo( $( '#field_' + options.formId + '_' + fieldId  ) );
                self.plainTextInputs[ fieldId ] = $plainTextInput;

            }

            return self.plainTextInputs[ fieldId ];
        };

        self.bindEvents = function( $input ) {

            if( window.tinymce && options.useRichTextEditor ) {

                var gpwcEditorPoll = setInterval( function() {

                    if( tinymce.editors.length == 0 ) {
                        return;
                    } else {
                        clearInterval( gpwcEditorPoll );
                    }

                    for( var i = 0; i < tinymce.editors.length; i++ ) {

                        if( tinymce.editors[ i ].id != $input.attr( 'id' ) ) {
                            continue;
                        }

                        var editor = tinymce.editors[ i ];

                        editor.on( 'keyup change', function() {
                            self.updateWordCount( $input );
                        } );

                        break;

                    }

                    // update price if there is already a default value
                    self.updateWordCount( $input );

                }, 250 );

            } else {

                $input.on( 'input propertychange', function() {
                    self.updateWordCount( $input );
                } );

            }

            $( document ).bind( 'gform_post_conditional_logic', function() {
                self.updateWordCount( $input );
            } );

        };

        self.getCounterLabel = function( $input ) {

            var labelId      = $input.attr( 'id' ) + '-word-count',
                $counterLabel = $( '#' + labelId );

            // if showCount is enabled and $counterLabel does not exist, create it
            if( options.showCount && $counterLabel.length <= 0 ) {
                var style = 'font-size: 11px; clear: both; margin-top: 3px; display: block;';
                if( $( '.gform_fields' ).is( '.left_label, .right_label' ) ) {
                    style += 'margin-left: 29%;'
                }
                if( options.useRichTextEditor ) {
                    style += 'padding:3px 10px;border-top:1px dotted #ddd;'
                }
                $counterLabel = $('<span style="' + style + '" id="' + labelId + '" class="gp-word-count-label"></span>');
                $input.after($counterLabel);
            }

            return $counterLabel;
        };

        // and the plugin begins
        return self.each( function() {

            self.bindEvents( $( this ) );

        } );
    };
    
})(jQuery);
