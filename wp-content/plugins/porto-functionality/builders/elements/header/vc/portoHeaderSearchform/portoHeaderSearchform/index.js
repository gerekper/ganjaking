/* eslint-disable import/no-webpack-loader-syntax */
import vcCake from 'vc-cake'
import PortoHeaderSearchform from './component'

const vcvAddElement = vcCake.getService('cook').add

vcvAddElement(
  require('./settings.json'),
  // Component callback
  function (component) {
    component.add(PortoHeaderSearchform)
  },
  // css settings // css for element
  {
    css: false,
    editorCss: false,
    mixins: {
      searchForm: {
        mixin: require('raw-loader!./cssMixins/searchForm.pcss')
      }
    }
  }
)
