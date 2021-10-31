/* eslint-disable import/no-webpack-loader-syntax */
import vcCake from 'vc-cake'
import PortoHeaderDivider from './component'

const vcvAddElement = vcCake.getService('cook').add

vcvAddElement(
  require('./settings.json'),
  // Component callback
  function (component) {
    component.add(PortoHeaderDivider)
  },
  // css settings // css for element
  {
    css: false,
    editorCss: false,
    mixins: {
      divider: {
        mixin: require('raw-loader!./cssMixins/divider.pcss')
      }
    }
  }
)
