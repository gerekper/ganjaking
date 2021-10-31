import React from 'react'
import { getService } from 'vc-cake'

const portoComponent = getService('portoComponent')

export default class PortoHeaderSearchform extends portoComponent.shortcodeComponent {
  constructor (props) {
    super(props)
  }

  componentDidMount () {
    const atts = this.props.atts
    super.updateShortcodeToHtml(this.getHBSearchformShortcode(atts.placeholder_text, atts.category_filter, atts.category_filter_mobile, atts.popup_pos), this.ref)
  }

  componentDidUpdate (prevProps, prevState) {
    const atts = this.props.atts,
      shortcode = this.getHBSearchformShortcode(atts.placeholder_text, atts.category_filter, atts.category_filter_mobile, atts.popup_pos)
    if (shortcode !== this.getHBSearchformShortcode(prevProps.atts.placeholder_text, prevProps.atts.category_filter, prevProps.atts.category_filter_mobile, prevProps.atts.popup_pos)) {
      super.updateShortcodeToHtml(shortcode, this.ref)
    }
  }

  shouldComponentUpdate(nextProps, nextState) {
    return true
  }

  getHBSearchformShortcode (placeholder_text, category_filter, category_filter_mobile, popup_pos) {
    if (!category_filter) {
      category_filter = ''
    }
    if (!category_filter_mobile) {
      category_filter_mobile = ''
    }
    return `[porto_hb_search_form placeholder_text="${placeholder_text}" category_filter="${category_filter}" category_filter_mobile="${category_filter_mobile}" popup_pos="${popup_pos}"]`
  }

  render () {
    const { id, editor, atts } = this.props
    const doAll = this.applyDO('all')
    const { placeholder_text, category_filter, category_filter_mobile, popup_pos, el_class } = atts

    let wrapClass = 'vce-porto-hb-search-form'

    return (
      <div className={wrapClass + (el_class ? ' ' + el_class : '')} {...editor} id={'el-' + id} {...doAll}>
        <div className="porto-hb-search-form vcvhelper"  ref={(ref) => { this.ref = ref }} data-vcvs-html={this.getHBSearchformShortcode(placeholder_text, category_filter, category_filter_mobile, popup_pos)}>
        </div>
      </div>
    )
  }
}
