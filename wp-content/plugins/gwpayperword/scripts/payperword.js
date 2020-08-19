/**
* GWPayPerWord Object
*
* @param formId
* @param ppwFields
*/

var GWPayPerWord = function(formId, ppwFields) {

    this.plainTextInputs = {};

    this.init = function(formId, ppwFields) {

        var gwppw = this;

        for(var i in ppwFields) {
            var ppwField = jQuery.extend({}, ppwFields[i]);
            gwppw.updatePrice(ppwField, formId);
            gwppw.bindEvents(ppwField, formId);
        }

    };

    this.updatePrice = function( ppwField, formId ) {

        var gwppw = this;

        // get field objects

        if( ppwField.useRichTextEditor ) {

            var editor      = tinymce.get( 'input_' + formId + '_' + ppwField.word_field ),
                text        = editor ? editor.getContent( { format: 'text' } ) : '',
                $plainInput = gwppw.getPlainTextInput( ppwField.word_field );

            $plainInput.val( text );

        } else {
            var wordField = jQuery('#input_' + formId + '_' + ppwField.word_field),
                text      = jQuery.trim( wordField.val() );
        }

        var priceField     = jQuery('#ginput_base_price_' + formId + '_' + ppwField.price_field),
            priceFieldSpan = jQuery('#input_' + formId + '_' + ppwField.price_field);

        var words      = text.match( /\S+/g ),
            wordCount  = gform.applyFilters( 'gpppw_word_count', words == null ? 0 : words.length, text, gwppw, ppwField, formId );

        var price = 0;
        var pricePerWord = parseFloat( ppwField.price_per_word );
        var basePrice = (isNaN(parseFloat(ppwField.base_price))) ? 0 : parseFloat( ppwField.base_price );
        var baseCount = (isNaN(parseFloat(ppwField.base_word_count))) ? 0 : parseFloat( ppwField.base_word_count );

        var isVisible = window['gf_check_field_rule'] ? gf_check_field_rule(formId, ppwField.word_field, true, '') == 'show' : true;

        // calculate price
        if( ! isVisible || ( wordCount <= 1 && words == null ) ) {
            price = 0;
        } else if(wordCount > baseCount) {
            extraWordsCount = wordCount - baseCount;
            price = basePrice;
            price += extraWordsCount * pricePerWord;
        } else {
            price = basePrice;
        }

        price = gform.applyFilters( 'gwppw_price', price, wordCount, pricePerWord, ppwField, formId );
        price = gform.applyFilters( 'gpppw_price', price, wordCount, pricePerWord, ppwField, formId );

        // format price
        labelPrice = gformFormatMoney(price);

        priceField.val( labelPrice ).change();
        priceFieldSpan.text( labelPrice );

        gformCalculateTotalPrice(formId);

    };

    this.bindEvents = function(ppwField, formId) {

        var gwppw = this;
        var wordField = jQuery('#input_' + formId + '_' + ppwField.word_field);

        if( window.tinymce && ppwField.useRichTextEditor ) {

            var gwppwEditorPoll = setInterval( function() {

                if( tinymce.editors.length == 0 ) {
                    return;
                } else {
                    clearInterval( gwppwEditorPoll );
                }

                for( var i = 0; i < tinymce.editors.length; i++ ) {

                    if( tinymce.editors[ i ].id != wordField.attr( 'id' ) ) {
                        continue;
                    }

                    var editor = tinymce.editors[ i ];

                    editor.on( 'keyup change', function() {
                        gwppw.updatePrice( ppwField, formId );
                    } );

                    break;

                }

                // update price if there is already a default value
                gwppw.updatePrice( ppwField, formId );

            }, 250 );

        } else {

            wordField.on( 'input propertychange', function() {
                gwppw.updatePrice( ppwField, formId );
            } );

        }

        jQuery(document).bind('gform_post_conditional_logic', function(){
            gwppw.updatePrice(ppwField, formId);
        });

    };

    this.getPlainTextInput = function( fieldId ) {

        if( typeof this.plainTextInputs[ fieldId ] == 'undefined' ) {

            var $plainTextInput = jQuery( '<textarea style="display:none;" id="gpppw_plain_text_' + formId + '_' + fieldId + '" name="gpppw_plain_text_' + fieldId + '"></textarea>' );
            $plainTextInput.appendTo( jQuery( '#field_' + formId + '_' + fieldId  ) );
            this.plainTextInputs[ fieldId ] = $plainTextInput;

        }

        return this.plainTextInputs[ fieldId ];
    };

    this.init(formId, ppwFields);

};