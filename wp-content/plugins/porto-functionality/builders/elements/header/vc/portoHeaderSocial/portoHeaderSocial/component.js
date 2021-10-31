import React from 'react'
import { getService } from 'vc-cake'

const portoComponent = getService('portoComponent')

export default class PortoHeaderSocial extends portoComponent.shortcodeComponent {
  constructor (props) {
    super(props)
  }

  componentDidMount () {
    const atts = this.props.atts
    super.updateShortcodeToHtml(this.getHBSocialShortcode(), this.ref)
  }

  componentDidUpdate (prevProps, prevState) {
    const atts = this.props.atts,
      shortcode = this.getHBSocialShortcode()
    if (shortcode !== this.getHBSocialShortcode()) {
      super.updateShortcodeToHtml(shortcode, this.ref)
    }
  }

  shouldComponentUpdate(nextProps, nextState) {
    return true
  }

  getHBSocialShortcode (placeholder_text, category_filter, category_filter_mobile) {
    return `[porto_hb_social]`
  }

  render () {
    const { id, editor, atts } = this.props
    const doAll = this.applyDO('all')
    const { el_class } = atts

    let wrapClass = 'vce-porto-hb-social'

    return (
      <div className={wrapClass + (el_class ? ' ' + el_class : '')} {...editor} id={'el-' + id} {...doAll}>
        <div className="porto-hb-social vcvhelper"  ref={(ref) => { this.ref = ref }} data-vcvs-html={this.getHBSocialShortcode()}>
        </div>
      </div>
    )
  }
}
