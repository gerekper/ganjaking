import React from 'react'
import { getService } from 'vc-cake'

const portoComponent = getService('portoComponent')

export default class PortoHeaderMyaccount extends portoComponent.shortcodeComponent {
  constructor (props) {
    super(props)
  }

  render () {
    const { id, editor, atts } = this.props
    const doAll = this.applyDO('all')
    const { icon_cl, el_class } = atts

    let wrapClass = 'my-account'
    if (el_class) {
      wrapClass += ' ' + el_class
    }
    const mixinData = this.getMixinData('myaccount')
    if (mixinData) {
      wrapClass += ` porto-hb-myaccount-${mixinData.selector}`
    }
    const iconCls = icon_cl ? icon_cl : 'porto-icon-user-2'

    return (
      <a className={wrapClass} href={porto_vc_vars.myaccount_url} {...editor} id={'el-' + id} {...doAll}>
        <i className={iconCls}></i>
      </a>
    )
  }
}
