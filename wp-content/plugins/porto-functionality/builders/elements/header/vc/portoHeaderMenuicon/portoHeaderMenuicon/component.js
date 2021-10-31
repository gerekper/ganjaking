import React from 'react'
import { getService } from 'vc-cake'

const portoComponent = getService('portoComponent')

export default class PortoHeaderMenuicon extends portoComponent.shortcodeComponent {
  constructor (props) {
    super(props)
  }

  render () {
    const { id, editor, atts } = this.props
    const doAll = this.applyDO('all')
    const { bg_color, color, icon_cl, el_class } = atts

    let wrapClass = 'vce-porto-hb-menuicon mobile-toggle'
    if (el_class) {
      wrapClass += ' ' + el_class
    }
    const iconCls = icon_cl ? icon_cl : 'fas fa-bars'

    return (
      <a className={wrapClass} {...editor} id={'el-' + id} {...doAll}>
        <i className={iconCls}></i>
      </a>
    )
  }
}
