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
const { TextControl, Button, ToggleControl, PanelBody } = wp.components;
const { Component } = wp.element;
const { InspectorControls } = wp.blockEditor;

/**
 * RevSlider Editor Element
 */
export  class RevSlider extends Component {

    constructor() {
		 
        super( ...arguments );
        const { attributes: { text, sliderTitle, modal, sliderImage, hideSliderImage } } = this.props;
        this.state = {
          text,
          sliderTitle,
          modal,
          sliderImage,
          hideSliderImage,
          message : ''
        }
    }
	
	componentDidMount() {
		if(!this.props.attributes.text &&   wp.data.select( 'core/editor' ).hasChangedContent()) {
      window.revslider_react = this;
			//this.props.attributes.checked = true;
      RS_SC_WIZARD.openTemplateLibrary();
		}		
  }
  

   openDialog = () => {
    window.revslider_react = this;
    RS_SC_WIZARD.openTemplateLibrary();
  }

   openSliderEditor = () => {
    window.revslider_react = this;
    RS_SC_WIZARD.openSliderEditor();      
  };

  openOptimizer = () => {
    window.revslider_react = this;
    RS_SC_WIZARD.openOptimizer();
  }

   setSliderAttributes = (alias) => {
    setAttributes( { text: alias } );
    setAttributes( { sliderImage: this.state.sliderImage } );
  }

   hideSliderImagePreview = () => {
    window.revslider_react = this;
    window.revslider_react.props.setAttributes( { hideSliderImage: this.state.hideSliderImage ? false : true } );
    this.state.hideSliderImage = this.state.hideSliderImage ? false : true;
  }

   refreshSliderImage = () => {
    window.revslider_react = this;
    var alias_array = this.state.text.split('"');
    var data;

    RVS.F.ajaxRequest('getSliderImage', { alias : alias_array[1] }, function(response) {		
        if(response.success) {	        
            try {
              data = JSON.stringify(response.image);
              data = JSON.parse(data);
            }
            catch(e) {
                data = false;
            }
            
            if(data) {
              window.revslider_react.props.setAttributes( { sliderImage: data } );
              window.revslider_react.setState({ sliderImage: data });
              window.revslider_react.setState({ message : ''});
              //window.revslider_react.state.sliderImage = window.revslider_react.props.attributes.sliderImage = data;
              //window.revslider_react.forceUpdate();
            }
            else {
              //console.log('No image');
              window.revslider_react.setState({message: __('No Admin Thumb set')});
              window.setTimeout(function(){window.revslider_react.setState({ message : ''});},4000);
            }
        }
        else {
          window.revslider_react.setState({message: __('No Admin Thumb set')});
          window.setTimeout(function(){window.revslider_react.setState({ message : ''});},4000);
        }
    });
  }



    render() {

        const {
        attributes: { text, sliderTitle, sliderImage, modal },
        setAttributes  } = this.props;
      
        window.revslider_react = this;
		
        return [
          <InspectorControls>  
					<PanelBody title={ __('Admin Thumb') } initialOpen={ true }>
              <div className="showHideButtons">
                <Button 
                      isDefault
                      onClick = { this.refreshSliderImage }
                      className="hideSilderImage"
                >
                    
                    { this.state.sliderImage ? 'cached'  : 'insert_photo' }
                </Button>
                <span>{ this.state.sliderImage ? __('Refresh Thumb')  : __('Load Thumb') }</span>
              </div>
              <div className="sliderImageMessage">{this.state.message}</div>
              {
                this.state.sliderImage && (
                  <div className="showHideButtons">
                    <Button 
                          isDefault
                          onClick = { this.hideSliderImagePreview }
                          className={ 'hideSilderImage' }
                    >
                        { !this.state.hideSliderImage ? 'visibility_off' : 'visibility'}
                    </Button>
                    <span>{ this.state.hideSliderImage ? __('Show Thumb')  : __('Hide Thumb') }</span>
                  </div>
                )
                }
                 
          </PanelBody>
          <PanelBody title={ __('Optimization') } initialOpen={ true }>
          <div className="optimizerButtons">  
                    <Button 
                          isDefault
                          onClick = { this.openOptimizer }
                          className={ 'optimizerOpen' }
                    >
                        flash_on
                    </Button>
                    <span>Optimize File Sizes</span>
            </div>
          </PanelBody>
                   
                    
          </InspectorControls>  
          ,
          <div className="revslider_block" data-modal={ this.state.modal } >
                  <div class="sliderBar">
                    <span>{this.state.sliderTitle}&nbsp;</span>
                    <TextControl
                          className="slider_slug"
                          value={ this.state.text }
                          onChange={ ( text ) => setSliderAttributes ( this.state.text ) }
                    />
                    <Button 
                          isDefault
                          onClick = { this.openSliderEditor }
                          className="slider_editor_button"
                    >
                        edit
                    </Button>
                    <Button 
                          isDefault
                          onClick = { this.openDialog } 
                          className="slider_edit_button"
                    >
                        Select Module
                    </Button>
                  </div>
                  {
									  this.state.sliderImage && !this.state.hideSliderImage && (
                      <div className="sliderImage">
                        <div style={{ backgroundImage : 'url(' + this.state.sliderImage +')'}}></div>
                      </div>
                    )
                  }
          </div>
        ]
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
          },
          sliderImage: {
             type:'string'
          },
          hideSliderImage:{
              boolean: false
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