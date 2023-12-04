export const getDeviceType = (key) => {
    let type = 'desktop';
    key = key.toLowerCase();

    switch (true) {
        case key.indexOf('desktop') > -1:
            type = 'desktop';
            break;
        case key.indexOf('mobile') > -1:
            type = 'mobile';
            break;
        case key.indexOf('tab') > -1:
            type = 'tab';
            break;
    }

    const isHover = key.indexOf('hover') > -1;
    if( isHover ) {
        type += 'Hover';
    }

    return type;
}



export const addProperty = (type, id, options = {}) => {
    let defultArgs = {};
    const { class: hasClass = false, attr = false, filter = false, args = {}, ...optionalArgs } = options;

    if( hasClass ) {
        defultArgs.classPrefix = `betterdocs-${id}-`;
    }
    if( attr ) {
        defultArgs.attrPrefix = id;
    }

    let controlArgs = {
        controlName: id,
        ...args
    }

    if( filter === 'number' ) {
        defultArgs.filter = (value, key, data) => (data[key] = +value.replace(/\D/g, ""));
    }

    return {
        id,
        type,
        class: hasClass,
        attr: attr,
        args: controlArgs,
        ...defultArgs,
        ...optionalArgs
    };
}
