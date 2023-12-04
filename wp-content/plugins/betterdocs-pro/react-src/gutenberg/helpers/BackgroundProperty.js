import Property from "./Property";
import { generateBackgroundControlStyles } from "../util/helpers";

class BackgroundProperty extends Property {
    hasNoProperty = [ 'bgTransitionStyle', 'ovlTransitionStyle' ];
    mapCSSProperty = {
        'bgTransitionStyle': 'transition'
    };

    generate(){
		this.data = generateBackgroundControlStyles(this.args);
	}
}

export default BackgroundProperty;
