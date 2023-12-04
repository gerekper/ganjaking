import {
    ResponsiveProperty,
    DimensionProperty,
    BackgroundProperty,
    BorderProperty,
    TypographyProperty,
    ColorProperty,
    AlignProperty
} from ".";

export default function (property, attributes) {
    switch (property.type) {
        case 'responsive':
            return new ResponsiveProperty(property, attributes);
        case 'dimension':
            return new DimensionProperty(property, attributes);
        case 'background':
            return new BackgroundProperty(property, attributes);
        case 'border':
            return new BorderProperty(property, attributes);
        case 'typography':
            return new TypographyProperty(property, attributes);
        case 'color':
            return new ColorProperty(property, attributes);
        case 'alignment':
            return new AlignProperty(property, attributes);
    }
}
