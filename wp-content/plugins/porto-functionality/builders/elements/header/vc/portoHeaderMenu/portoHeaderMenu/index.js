/* eslint-disable import/no-webpack-loader-syntax */
import vcCake from 'vc-cake'
import PortoHeaderMenu from './component'

const vcvAddElement = vcCake.getService('cook').add

vcvAddElement(
  require('./settings.json'),
  // Component callback
  function (component) {
    component.add(PortoHeaderMenu)
  },
  // css settings // css for element
  {
    css: false,
    editorCss: false,
    mixins: {
      topnav: {
        mixin: require('raw-loader!./cssMixins/topnav.pcss')
      }
    }
  }
)
