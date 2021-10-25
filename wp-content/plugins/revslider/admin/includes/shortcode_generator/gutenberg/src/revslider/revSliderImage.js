/**
 * RevSlider Editor Element
 */


/**
 * Internal block libraries
*/
const { Component } = wp.element;

/**
 * Component RevSlider for usage in block
*/
export class RevSliderImage extends Component {
    
  constructor() {	 
      super( ...arguments );
      this.state = {
        response : undefined,
        alias : this.props.attributes.alias,
        slidertitle: this.props.attributes.slidertitle,
      };

  }

  // Load Slider Image before it is mounted
  componentWillMount(){
    this.loadSliderImage();
  }

  // Load Slider Image when it is mounted
  componentDidMount() {
    //this.loadSliderImage();
  }

  // When new Props are send to the Block it will reload the image when the alias has changed
  componentWillReceiveProps(){
    if(this.state.alias != this.props.attributes.alias)
    this.loadSliderImage();
  }

  // Loads the Slider Admin Thumb via Ajax Call
  loadSliderImage(){
    this.setState({response: undefined});
    this.setState({alias: this.props.attributes.alias});
    var self = this;
    if(!this.props.attributes.alias){
      if(this.props.attributes.content!==undefined || this.props.attributes.text!==undefined){
        let shortcode = this.props.attributes.content!==undefined ? RVS.SC.parseShortCode(this.props.attributes.content) :  RVS.SC.parseShortCode(this.props.attributes.text); 
        if(shortcode.attributes.alias) {
          this.props.attributes.alias = shortcode.attributes.alias;
          }
      }
    }
    if(this.props.attributes.alias){
      RVS.F.ajaxRequest('getSliderImage', { alias : this.props.attributes.alias }, function(response) {    
        if(response.success) {  
            if (response!==undefined && response.image!==undefined) {
              self.setState({
                response
              });
            }          			          
            RVS.F.showWaitAMinute({fadeIn:0,text:RVS_LANG.loadingcontent});				
          }
      });
    }
  }

  // Renders the different states of the image (loading, loaded and no image)
  render() {  
    //Image Loaded
    if(this.state.response && this.state.response.image !== ""){
      return [
        <div className="sliderImage">
            <div style={{ backgroundImage : 'url(' +  this.state.response.image +')'}}></div>
        </div>   
      ]
    }
    else {
      //Image Loading
      if(!this.state.response)
        return  [ 
          <div className="sliderImageLoading"></div>
        ]
      //No Image
      else {
        return  [ 
          <div className="noSliderImage"></div> 
        ]
      }
    }
  }
}