import React from 'react'
import { getService } from 'vc-cake'

const portoComponent = getService('portoComponent')

export default class PortoHeaderSwitcher extends portoComponent.shortcodeComponent {
  constructor (props) {
    super(props)
  }

  componentDidMount () {
    const atts = this.props.atts
    super.updateShortcodeToHtml(this.getHBSwitcherShortcode(atts.type), this.ref)
  }

  componentDidUpdate (prevProps, prevState) {
    const atts = this.props.atts,
      shortcode = this.getHBSwitcherShortcode(atts.type)
    if (shortcode !== this.getHBSwitcherShortcode(prevProps.atts.type)) {
      super.updateShortcodeToHtml(shortcode, this.ref)
    }
  }

  shouldComponentUpdate(nextProps, nextState) {
    return true
  }

  getHBSwitcherShortcode (type) {
    return `[porto_hb_switcher type="${type}"]`
  }

  render () {
    const { id, editor, atts } = this.props
    const doAll = this.applyDO('all')
    const { el_class } = atts

    let wrapCls = 'vce-porto-hb-switcher'
    let mixinData = this.getMixinData('switcher')
    if (mixinData) {
      wrapCls += ` porto-hb-${mixinData.selector}`
    }
    if (el_class) {
      wrapCls += ' ' + el_class
    }

    return (
      <div className={wrapCls} {...editor} id={'el-' + id} {...doAll}>
        <div className="porto-hb-switcher vcvhelper"  ref={(ref) => { this.ref = ref }} data-vcvs-html={this.getHBSwitcherShortcode(atts.type)}>
        </div>
      </div>
    )
  }
}
