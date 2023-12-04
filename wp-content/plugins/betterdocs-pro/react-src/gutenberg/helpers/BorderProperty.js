import Property from "./Property";
import { generateBorderShadowStyles } from "../util/helpers";

class BorderProperty extends Property {
    hasNoProperty = ['transitionStyle'];
    mapCSSProperty = {
        'transitionStyle': 'transition',
    };

    generate() {
        this.data = generateBorderShadowStyles(this.args);
    }
}

export default BorderProperty;
