import React from 'react'
import { getService } from 'vc-cake'

const portoComponent = getService('portoComponent')

export default class PortoHeaderDivider extends portoComponent.shortcodeComponent {
  constructor (props) {
    super(props)
  }

  render () {
    const { id, editor, atts } = this.props
    const doAll = this.applyDO('all')
    const { el_class } = atts

    let wrapClass = 'separator'
    if (el_class) {
      wrapClass += ' ' + el_class
    }
    let mixinData = this.getMixinData('divider')
    if (mixinData) {
      wrapClass += ` porto-hb-divider-${mixinData.selector}`
    }

    return (
      <span className={wrapClass} {...editor} id={'el-' + id} {...doAll}>
      </span>
    )
  }
}
