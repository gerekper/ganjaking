/* eslint-disable import/no-webpack-loader-syntax */
import vcCake from 'vc-cake'
import PortoHeaderMinicart from './component'

const vcvAddElement = vcCake.getService('cook').add

vcvAddElement(
  require('./settings.json'),
  // Component callback
  function (component) {
    component.add(PortoHeaderMinicart)
  },
  // css settings // css for element
  {
    css: false,
    editorCss: false,
    mixins: {
      miniCart: {
        mixin: require('raw-loader!./cssMixins/miniCart.pcss')
      }
    }
  }
)
