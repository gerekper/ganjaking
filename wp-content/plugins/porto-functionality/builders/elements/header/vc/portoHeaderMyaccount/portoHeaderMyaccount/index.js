/* eslint-disable import/no-webpack-loader-syntax */
import vcCake from 'vc-cake'
import PortoHeaderMyaccount from './component'

const vcvAddElement = vcCake.getService('cook').add

vcvAddElement(
  require('./settings.json'),
  // Component callback
  function (component) {
    component.add(PortoHeaderMyaccount)
  },
  // css settings // css for element
  {
    css: false,
    editorCss: false,
    mixins: {
      myaccount: {
        mixin: require('raw-loader!./cssMixins/myaccount.pcss')
      }
    }
  }
)
