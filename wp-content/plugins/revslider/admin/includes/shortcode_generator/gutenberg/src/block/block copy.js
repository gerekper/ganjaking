/**
 * Block dependencies
 */     
import './style.scss';
import './editor.scss';

/**
 * Internal block libraries
 */
const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { TextControl, Button } = wp.components;
const { Component } = wp.element;

/**
 * RevSlider Editor Element
 */
export  class RevSlider extends Component {

    constructor() {
		 
        super( ...arguments );
        const { attributes: { text, sliderTitle, modal } } = this.props;
        this.state = {
          text,
          sliderTitle,
		  modal
        }
    }
	
	componentDidMount() {

		if(!this.props.attributes.checked) {
			
			this.props.attributes.checked = true;
			//RS_SC_WIZARD.openTemplateLibrary();
			
		}
		
	}

    render() {

        const {
        attributes: { text, sliderTitle, modal },
        setAttributes  } = this.props;
      
        window.revslider_react = this;

        const openDialog = () => {
		      RS_SC_WIZARD.openTemplateLibrary();
        }

        const openSliderEditor = () => {
          RS_SC_WIZARD.openSliderEditor();      
        };
		
        return (
          <div className="revslider_block" data-modal={ this.state.modal } >
                  <span><i class="material-icons">picture_in_picture</i>{this.state.sliderTitle}&nbsp;</span>
                  <TextControl
                        className="slider_slug"
                        value={ this.state.text }
                        onChange={ ( text ) => setAttributes( { text: this.state.text } ) }
                  />
                  <Button 
                        isDefault
                        onClick = { openSliderEditor }
                        className="slider_editor_button"
                  >
                      edit
                  </Button>
                  <Button 
                        isDefault
                        onClick = { openDialog } 
                        className="slider_edit_button"
                  >
                      Select Module
                  </Button>
          </div>
        )
    }
}


/**
 * Register block
 */
export default registerBlockType(
    'themepunch/revslider',
    {
        title: __( 'Slider Revolution', 'revslider' ),
        description: __( 'Add your Slider Revolution.', 'revslider' ),
        category: 'themepunch',
        icon: {
          src:  'update',
          background: 'rgb(94, 53, 177)',
          color: 'white',
          viewbox: "0 0 24 24"
        },        
        keywords: [
            __( 'Banner', 'revslider' ),
            __( 'CTA', 'revslider' ),
            __( 'Slider', 'revslider' ),
        ],
        attributes: {
		  checked: {
			  type: 'boolean',
			  default: false
		  },
		  modal: {
			  type: 'boolean',
			  default: false
		  },
          text: {
              selector: '.revslider',
              type: 'string',
              source: 'text',
          },
          sliderTitle: {
              selector: '.revslider',
              type: 'string',
              source: 'attribute',
              attribute: 'data-slidertitle',
          }
        },
        edit: props => {
          const { setAttributes } = props;
          return (
            <div>
              <RevSlider {...{ setAttributes, ...props }} />
            </div>
          );
        },
        save: props => {
          const { attributes: { text, sliderTitle, modal } } = props;
          return (
            <div className="revslider" data-modal={modal} data-slidertitle={sliderTitle}>
               {text} 
            </div>
          );
        },
        deprecated: [
          {
            attributes: {
                  checked: {
                    type: 'boolean',
                    default: false
                  },
                  text: {
                      selector: '.revslider',
                      type: 'string',
                      source: 'text',
                  },
                  sliderTitle: {
                      selector: '.revslider',
                      type: 'string',
                      source: 'attribute',
                      attribute: 'data-slidertitle',
                  }
             },
              save( props ) {
                return (
                  <div className="revslider" data-slidertitle={props.attributes.sliderTitle}>
                     {props.attributes.text} 
                  </div>
                );
              },
          }
        ],
    },
);