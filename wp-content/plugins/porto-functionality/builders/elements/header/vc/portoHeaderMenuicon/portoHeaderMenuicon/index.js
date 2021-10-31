/* eslint-disable import/no-webpack-loader-syntax */
import vcCake from 'vc-cake'
import PortoHeaderMenuicon from './component'

const vcvAddElement = vcCake.getService('cook').add

vcvAddElement(
  require('./settings.json'),
  // Component callback
  function (component) {
    component.add(PortoHeaderMenuicon)
  },
  // css settings // css for element
  {
    css: false,
    editorCss: false,
    mixins: {
      menuicon: {
        mixin: require('raw-loader!./cssMixins/menuicon.pcss')
      }
    }
  }
)
