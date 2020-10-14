const CustomResultsOrderI18n = {};

CustomResultsOrderI18n.install = function(Vue, options) {
  Vue.CustomResultsOrderI18n = function(key, placeholders = []) {
    let strings = _SEARCHWP_CRO_VARS.i18n;

    let string = strings.hasOwnProperty(key) ? strings[key] : key;

    if (placeholders.length) {
      placeholders.forEach(function(placeholder, placeholderIndex) {
        string = string.replace(
          "{{ searchwpCroPlaceholder" +
            parseInt(placeholderIndex + 1, 10) +
            " }}",
          placeholder
        );
      });
    }

    return string;
  };
};

export default CustomResultsOrderI18n;
