( function( wp ) {
  /**
  * Registers a new block provided a unique name and an object defining its behavior.
  * @see https://github.com/WordPress/gutenberg/tree/master/blocks#api
  */
  var registerBlockType = wp.blocks.registerBlockType;
  /**
  * Returns a new element of given type. Element is an abstraction layer atop React.
  * @see https://github.com/WordPress/gutenberg/tree/master/element#element
  */
  var el = wp.element.createElement;
  /**
  * Retrieves the translation of text.
  * @see https://github.com/WordPress/gutenberg/tree/master/i18n#api
  */
  var __ = wp.i18n.__;

  /**
  * Inspector dependencies
  */
  var InspectorControls = wp.blocks.InspectorControls;
  var ColorPalette      = wp.blocks.ColorPalette;
  var PanelColor        = wp.components.PanelColor;

  /**
  * Every block starts by registering a new block type definition.
  * @see https://wordpress.org/gutenberg/handbook/block-api/
  */
  registerBlockType( 'memberpress-courses/progress-bar', {
    /**
    * This is the display title for your block, which can be translated with `i18n` functions.
    * The block inserter will show this name.
    */
    title: __( 'Progress Bar Block' ),

    /**
    * Blocks are grouped into categories to help users browse and discover them.
    * The categories provided by core are `common`, `embed`, `formatting`, `layout` and `widgets`.
    */
    category: 'layout',

    /**
    * Optional block extended support features.
    */
    supports: {
      // Removes support for an HTML mode.
      html: false,
    },

    /**
    * Default attributes
    */
    attributes: {
      barColor: {
        type: 'string',
      }
    },

    /**
    * The edit function describes the structure of your block in the context of the editor.
    * This represents what the editor will render when the block is used.
    * @see https://wordpress.org/gutenberg/handbook/block-edit-save/#edit
    *
    * @param {Object} [props] Properties passed from the editor.
    * @return {Element}       Element to render.
    */
    edit: function( props ) {
      var barColor   = props.attributes.barColor;
      var isSelected = props.isSelected;

      // Set the barColor attribute onChange
      function onChangeBarColor( newColor ) {
        props.setAttributes( { barColor: newColor } );
      }

      // Arrays need unique keys
      return [
        isSelected && el(
          InspectorControls,
          { key: 'inspector' },
          el(
            PanelColor,
            {
              title: __( 'Progress Bar Color' ),
              colorValue: barColor,
            },
            el(
              ColorPalette,
              {
                value: barColor,
                onChange: onChangeBarColor,
              }
            )
          )
        ),
        el(
          'div',
          {
            key: 'progress-bar-container',
            className: props.className
          },
          el(
            'span',
            { style: { backgroundColor: barColor } },
          )
        )
      ]
    },

    /**
    * The save function defines the way in which the different attributes should be combined
    * into the final markup, which is then serialized by Gutenberg into `post_content`.
    * @see https://wordpress.org/gutenberg/handbook/block-edit-save/#save
    *
    * @return {Element}       Element to render.
    */
    save: function( props ) {
      var barColor   = props.attributes.barColor;
      return el(
        'div',
        {},
        el(
          'span',
          { style: { backgroundColor: barColor } },
        )
      );
    }
  } );
} )(
  window.wp
);
