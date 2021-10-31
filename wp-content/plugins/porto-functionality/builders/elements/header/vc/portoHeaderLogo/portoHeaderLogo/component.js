import React from 'react'
import { getService } from 'vc-cake'

const portoComponent = getService('portoComponent')

export default class PortoHeaderLogo extends portoComponent.shortcodeComponent {
  constructor (props) {
    super(props)
  }

  componentDidMount () {
    super.updateShortcodeToHtml(this.getHBLogoShortcode(), this.ref)
  }

  componentDidUpdate (prevProps, prevState) {
    const atts = this.props.atts,
      shortcode = this.getHBLogoShortcode()
    if (shortcode !== this.getHBLogoShortcode()) {
      super.updateShortcodeToHtml(shortcode, this.ref)
    }
  }

  shouldComponentUpdate(nextProps, nextState) {
    return true
  }

  getHBLogoShortcode () {
    return `[porto_hb_logo]`
  }

  render () {
    const { id, editor, atts } = this.props
    const doAll = this.applyDO('all')
    const { el_class } = atts

    return (
      <div className={'vce-porto-hb-logo' + (el_class ? ' ' + el_class : '')} {...editor} id={'el-' + id} {...doAll}>
        <div className="porto-hb-logo vcvhelper"  ref={(ref) => { this.ref = ref }} data-vcvs-html={this.getHBLogoShortcode()}>
        </div>
      </div>
    )
  }
}
