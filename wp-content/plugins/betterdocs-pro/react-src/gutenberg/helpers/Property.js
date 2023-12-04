import { getDeviceType } from "./helper";
import { softMinifyCssStrings } from "../util/helpers";

// let stylesWithProperty = {}

class Property {
    hasNoProperty = [];
    mapCSSProperty = {};

    constructor(property, attributes) {
        // this.hasNoProperty = [];
        // this.mapCSSProperty = {};

        for (const key in property) {
            Object.defineProperty(this, key, {
                value: property[key],
                writable: (key != 'id' && key != 'type')
            })
        }

        this.attributes = attributes;
        this.args.attributes = attributes;

        this.generate();

        if (this?.class) {
            this.generateClasses();
        }

        if (this?.attr) {
            this.generateHTMLAttrs();
        }

        /**
         * this key will contains styles with selectors and property
         *
         * i.e:
         *
         * {
         *  '.css-selector' : {
         *      'desktop' : {
         *          'transition': [ '#fff', '#fff' ]
         *      }
         *  }
         * }
         *
         */
        this.stylesWithProperty = {};
    }

    getStylesProperty(){
        return this.stylesWithProperty ?? {};
    }

    deviceType(key) {
        return getDeviceType(key);
    }

    getCssStrings() {
        const cssStrings = {}
        let unknownStyles = {};

        this.forEach(function (cssProperties, key) {
            if (cssProperties == undefined) {
                return;
            }

            if( this.id == 'col' ) {
                cssProperties = cssProperties > 0 ? `--column: ${cssProperties};` : '';
            }

            let value = softMinifyCssStrings( String( cssProperties ) );

            let deviceType = this.deviceType(key);

            if( this.hasNoProperty.includes(key) ) {
                let property = this.mapCSSProperty[key];

                // if( this.mapCSSProperty[key] !== undefined ) {
                //     if( stylesWithProperty[ this.cssSelector ] && stylesWithProperty[ this.cssSelector ][ property ] && ! stylesWithProperty[ this.cssSelector ][ property ].includes( value ) ) {
                //         stylesWithProperty[ this.cssSelector ][ property ].push( value );
                //     } else if( stylesWithProperty[ this.cssSelector ] ) {
                //         stylesWithProperty[ this.cssSelector ][ property ] = [ value ];
                //     } else {
                //         stylesWithProperty[ this.cssSelector ] = { };
                //         stylesWithProperty[ this.cssSelector ][ property ] = [ value ];
                //     }
                // }


                unknownStyles[ this.cssSelector ] = unknownStyles[ this.cssSelector ] ?? {};

                if( this.mapCSSProperty[key] !== undefined ) {
                    unknownStyles[ this.cssSelector ][ property ] = unknownStyles[ this.cssSelector ][ property ] ?? [];
                    unknownStyles[ this.cssSelector ][ property ].push( value );

                    this.stylesWithProperty = unknownStyles;
                }
                return;
            }

            cssStrings[deviceType] = cssStrings[deviceType] ? cssStrings[deviceType] + value : value;
        });

        return cssStrings;
    }

    generateClasses() {
        this.classNames = '';
        let classes = {}
        this.forEach(function (value, key) {
            let deviceType = this.deviceType(key);
            classes[deviceType] = `${this.classPrefix}${deviceType}-${value != '' ? value : this.defaultData[deviceType]}`;
        })
        this.classNamesAsObject = classes;

        this.classNames += Object.values(classes).join(' ');
    }

    generateHTMLAttrs() {
        this.htmlAttrs = {};
        this.forEach(function (value, key) {
            let deviceType = this.deviceType(key);
            this.htmlAttrs[`data-${this.attrPrefix}_${deviceType}`] = value != '' ? value : this.defaultData[deviceType];
        })
    }

    getData() {
        return this.data;
    }

    generate() {
        this.data = {};
        return this;
    }

    filterData() {
        if (this.filter) {
            return this.forEach(this.filter);
        }

        return this;
    }

    forEach(callback, thisArgs) {
        for (const key in this.data) {
            callback.call(thisArgs ?? this, this.data[key], key, this.data);
        }

        return this;
    }
}

// Property.prototype.forEach = function (callback, thisArgs) {
//     for (const key in this.data) {
//         callback.call(thisArgs ?? this, this.data[key], key, this.data);
//     }
//     return this;
// }

export default Property;
