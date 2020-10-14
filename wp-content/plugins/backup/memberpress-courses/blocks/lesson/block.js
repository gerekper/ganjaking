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
  var sprintf = wp.i18n.sprintf;

  /**
  * Inspector dependencies (Block config options in inspector panel)
  */
  var InspectorControls = wp.blocks.InspectorControls;
  var SelectControl     = wp.blocks.InspectorControls.SelectControl;
  var RangeControl      = wp.components.RangeControl;

  /**
  * Block control dependencies (bar above content)
  */
  var BlockControls     = wp.blocks.BlockControls;
  var BlockAlignmentToolbar  = wp.blocks.BlockAlignmentToolbar;

  var MediaUploadButton = wp.blocks.MediaUploadButton;
  var InnerBlocks       = wp.blocks.InnerBlocks;

  /**
  * Every block starts by registering a new block type definition.
  * @see https://wordpress.org/gutenberg/handbook/block-api/
  */
  registerBlockType( 'memberpress-courses/lesson', {
    /**
    * This is the display title for your block, which can be translated with `i18n` functions.
    * The block inserter will show this name.
    */
    title: __( 'Lesson Block' ),

    icon: 'columns',

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

    attributes: {
      columns: {
        type:    'number',
        default: 4,
      },
      rows: {
        type:    'number',
        default: 1,
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
      var columns    = props.attributes.columns;
      var rows       = props.attributes.rows;
      var isSelected = props.isSelected;

      function onChangeColNum( newValue ) {
        props.setAttributes( { columns: newValue } );
      }

      function onChangeRowNum( newValue ) {
        props.setAttributes( { rows: newValue } );
      }

      function renderCols( num ) {
        var layouts = [];
        for( i = 0; i < num; i++ ) {
          layouts.push(
            {
              name:  `column-${ i + 1 }`,
              label: sprintf( __( 'Column %d' ), i + 1 ),
              icon:  'columns',
            }
          );
        }

        return layouts;
      }

      return [
        isSelected && el(
          InspectorControls,
          {
            key: 'inspector',
          },
          el(
            RangeControl,
            {
              label:    __( 'Columns' ),
              value:    columns,
              onChange: onChangeColNum,
              min:      1,
              max:      4,
            }
          ),
          el(
            RangeControl,
            {
              label:    __( 'rows' ),
              value:    rows,
              onChange: onChangeRowNum,
              min:      1,
              max:      4,
            }
          ),
        ),
        el(
          'div',
          {
            key: 'lesson-container',
            className: props.className
          },
          el(
            InnerBlocks,
            {
              layouts: renderCols( columns )
            }
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
    save: function() {
      return el(
        'p',
        {},
        __( 'Hello from the saved content!' )
      );
    }
  } );
} )(
  window.wp
);
