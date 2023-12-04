import Property from "./Property";
import { generateTypographyStyles } from "../util/helpers";

class TypographyProperty extends Property {
    generate(){
		this.data = generateTypographyStyles(this.args);
	}
}

export default TypographyProperty;