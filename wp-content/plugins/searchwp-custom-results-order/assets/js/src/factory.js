const Factory = {};

Factory.install = function(Vue, options) {
  Vue.Factory = function(type) {
    let widget = {};

    switch(type) {
      case 'trigger':
        widget = {
          query: '',
          engine: {
            name: 'default',
            label: 'Default'
          },
          exact: true
        };
        break;
    }

    return widget;
  };
};

export default Factory;
