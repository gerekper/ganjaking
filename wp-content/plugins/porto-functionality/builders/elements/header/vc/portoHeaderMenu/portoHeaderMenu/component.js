import React from 'react'
import { getService } from 'vc-cake'

const portoComponent = getService('portoComponent')

export default class PortoHeaderMenu extends portoComponent.shortcodeComponent {
  constructor (props) {
    super(props)
  }

  componentDidMount () {
    const atts = this.props.atts
    super.updateShortcodeToHtml(this.getHBMenuShortcode(atts.location), this.ref)
  }

  componentDidUpdate (prevProps, prevState) {
    const atts = this.props.atts,
      shortcode = this.getHBMenuShortcode(atts.location)
    if (shortcode !== this.getHBMenuShortcode(prevProps.atts.location)) {
      super.updateShortcodeToHtml(shortcode, this.ref)
    }
  }

  shouldComponentUpdate(nextProps, nextState) {
    return true
  }

  getHBMenuShortcode (location) {
    return `[porto_hb_menu location="${location}"]`
  }

  render () {
    const { id, editor, atts } = this.props
    const doAll = this.applyDO('all')
    const { el_class } = atts

    let wrapCls = 'vce-porto-hb-menu'
    if (el_class) {
      wrapCls += ' ' + el_class
    }
    let mixinData = this.getMixinData('topnav')
    if (mixinData) {
      wrapCls += ` porto-hb-menu-${mixinData.selector}`
    }

    return (
      <div className={wrapCls} {...editor} id={'el-' + id} {...doAll}>
        {
          ! atts.location && "Select a location"
        }
        <div className="porto-hb-menu vcvhelper"  ref={(ref) => { this.ref = ref }} data-vcvs-html={this.getHBMenuShortcode(atts.location)}>
        </div>
      </div>
    )
  }
}
