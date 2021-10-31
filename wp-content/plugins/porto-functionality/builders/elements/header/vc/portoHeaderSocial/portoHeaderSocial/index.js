/* eslint-disable import/no-webpack-loader-syntax */
import vcCake from 'vc-cake'
import PortoHeaderSocial from './component'

const vcvAddElement = vcCake.getService('cook').add

vcvAddElement(
  require('./settings.json'),
  // Component callback
  function (component) {
    component.add(PortoHeaderSocial)
  },
  // css settings // css for element
  {
    css: false,
    editorCss: false,
    mixins: {
      social: {
        mixin: require('raw-loader!./cssMixins/social.pcss')
      }
    }
  }
)
