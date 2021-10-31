/* eslint-disable import/no-webpack-loader-syntax */
import vcCake from 'vc-cake'
import PortoHeaderWishlist from './component'

const vcvAddElement = vcCake.getService('cook').add

vcvAddElement(
  require('./settings.json'),
  // Component callback
  function (component) {
    component.add(PortoHeaderWishlist)
  },
  // css settings // css for element
  {
    css: false,
    editorCss: false,
    mixins: {
      wishlist: {
        mixin: require('raw-loader!./cssMixins/wishlist.pcss')
      }
    }
  }
)
