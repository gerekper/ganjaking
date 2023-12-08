jQuery(window).on('elementor/frontend/init', function () {
  function pafeAddCustomCss(css, context) {
    if (!context) {
      return DOMPurify.sanitize(css, { CSS: true });
    }
    var customCss = '';

    var model = context.model,
      customCss = model.get('settings').get('pafe_custom_css'),
      customCssSoftlite = model.get('settings').get('_custom_css'),
      customCssDesktop = model.get('settings').get('_custom_css_f_ele_css_desktop'),
      customCssTablet = model.get('settings').get('_custom_css_f_ele_css_tablet'),
      customCssMobile = model.get('settings').get('_custom_css_f_ele_css_mobile');

    customCss += customCssSoftlite ? customCssSoftlite : '';
    customCss += customCssDesktop ? customCssDesktop : '';
    customCss += customCssTablet ? ' @media (max-width: 768px) { ' + customCssTablet + '}' : '';
    customCss += customCssMobile ? ' @media (max-width: 425px) { ' + customCssMobile + '}' : '';

    if (!customCss || customCss.trim() === '') {
      return DOMPurify.sanitize(css, { CSS: true });
    }

    var selector = '.elementor-' + elementData.postID + ' .elementor-element.elementor-element-' + model.get('id');

    if ('document' === model.get('elType')) {
      selector = elementor.config.document.settings.cssWrapperSelector;
    }

    if (customCss) {
      css += customCss.replaceAll('selector', selector);
    }

    return DOMPurify.sanitize(css, { CSS: true });
  }

  if ((typeof elementor) !== 'undefined') {
    elementor.hooks.addFilter('editor/style/styleText', pafeAddCustomCss);
  }
});