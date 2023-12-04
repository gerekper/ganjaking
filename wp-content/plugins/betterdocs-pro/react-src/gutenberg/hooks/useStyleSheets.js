import { useEffect } from '@wordpress/element';

import { generateProperty } from '../helpers';

export const StyleTag = ({ casecadeStyles, deviceType }) => {
    return (
        <style>
            {casecadeStyles?.desktop ?? ''}
            {casecadeStyles?.desktopHover ?? ''}
            {casecadeStyles?.extraStyles ?? ''}
            {deviceType === 'Tablet' && (casecadeStyles?.tab ?? '')}
            {deviceType === 'Tablet' && (casecadeStyles?.tabHover ?? '')}
            {deviceType === 'Mobile' && (casecadeStyles?.mobile ?? '')}
            {deviceType === 'Mobile' && (casecadeStyles?.mobileHover ?? '')}

            {
                casecadeStyles?.tab?.length != 0 &&
                `@media all and (max-width: 1024px) {
                    ${casecadeStyles?.tab ?? ''}
                    ${casecadeStyles?.tabHover ?? ''}
                }`
            }
            {
                casecadeStyles?.mobile?.length != 0 &&
                `@media all and (max-width: 767px) {
                    ${casecadeStyles?.mobile ?? ''}
                    ${casecadeStyles?.mobileHover ?? ''}
                }`
            }
        </style>
    )
};

const setDeviceTypeCSS = (cssStore, selectors, styles) => {
    cssStore[selectors] = [...(cssStore[selectors] || []), styles];
    return cssStore;
};

const useStyleSheets = (props) => {
    const { properties: propertiesArgs = [], setAttributes, attributes } = props;
    const deviceType = attributes?.resOption;

    const properties = propertiesArgs.reduce((prev, propertyArg) => {
        const property = generateProperty(propertyArg, attributes);
        prev[property.id] = property;

        return prev;
    }, {});

    const htmlAttributes = Object.values(properties)
        .filter(property => property?.attr)
        .map(property => property.htmlAttrs)
        .reduce((prev, value) => ({ ...prev, ...value }), {});

    const casecadeStylesObject = Object.values(properties)
        .filter(property => property.cssSelector !== undefined)
        .reduce((previous, property) => {
            const propertyCSS = property.getCssStrings();
            const hoverSelector = property?.hoverSelector != undefined ? property?.hoverSelector : `${property.cssSelector}:hover`;

            setDeviceTypeCSS(previous.desktop, property.cssSelector, propertyCSS?.desktop);
            setDeviceTypeCSS(previous.desktopHover, hoverSelector, propertyCSS?.desktopHover);
            setDeviceTypeCSS(previous.tab, property.cssSelector, propertyCSS?.tab);
            setDeviceTypeCSS(previous.tabHover, hoverSelector, propertyCSS?.tabHover);
            setDeviceTypeCSS(previous.mobile, property.cssSelector, propertyCSS?.mobile);
            setDeviceTypeCSS(previous.mobileHover, hoverSelector, propertyCSS?.mobileHover);

            const stylesProperty = property.getStylesProperty();

            if (Object.keys(stylesProperty).length > 0) {
                Object.entries(stylesProperty).forEach(([selector, css]) => {
                    previous.extraStyles[selector] = previous.extraStyles[selector] || {};
                    Object.entries(css).forEach(([property, styles]) => {
                        previous.extraStyles[selector][property] = [...(previous.extraStyles[selector][property] || []), ...styles];
                    });
                });
            }

            return previous;
        }, { desktop: {}, tab: {}, mobile: {}, desktopHover: {}, tabHover: {}, mobileHover: {}, extraStyles: {} });

    const casecadeStyles = Object.keys(casecadeStylesObject).reduce((prev, deviceType) => {
        if (deviceType === 'extraStyles') {
            const extraStyles = Object.entries(casecadeStylesObject[deviceType]).reduce((prevValue, [selector, styles]) => {
                const cssStyles = Object.entries(styles).reduce((prev, [p, value]) => {
                    return prev + `${p}:${value.join(', ')};`;
                }, '');
                return `${prevValue} ${selector}{ ${cssStyles} }`;
            }, '');
            return { ...prev, [deviceType]: extraStyles };
        } else {
            const deviceStyles = Object.entries(casecadeStylesObject[deviceType]).map(([selector, value]) => {
                value = value.filter( item => item != undefined && item != null && item.trim() != '' );
                return value?.length > 0 ? { [selector]: value.join('') } : {};
            }).reduce((allCss, item) => {
                return allCss + Object.entries(item).reduce((prev, [selector, cssProps]) => {
                    return prev + `${selector}{ ${cssProps} }`;
                }, '');
            }, '');
            return { ...prev, [deviceType]: deviceStyles };
        }
    }, {});

    useEffect(() => {
        if (JSON.stringify(attributes?.blockMeta) != JSON.stringify(casecadeStyles)) {
            setAttributes({ blockMeta: casecadeStyles });
        }
    }, [attributes]);

    return {
        properties,
        htmlAttributes,
        casecadeStyles,
        styleTag: () => <StyleTag casecadeStyles={casecadeStyles} deviceType={deviceType} />
    };
}

export default useStyleSheets
