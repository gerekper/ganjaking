/* eslint-disable import/no-webpack-loader-syntax */
import vcCake from 'vc-cake'
import PortoHeaderSwitcher from './component'

const vcvAddElement = vcCake.getService('cook').add

vcvAddElement(
  require('./settings.json'),
  // Component callback
  function (component) {
    component.add(PortoHeaderSwitcher)
  },
  // css settings // css for element
  {
    css: false,
    editorCss: false,
    mixins: {
      switcher: {
        mixin: require('raw-loader!./cssMixins/switcher.pcss')
      }
    }
  }
)
