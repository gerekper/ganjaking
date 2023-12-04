import Property from "./Property";
import { generateDimensionsControlStyles } from "../util/helpers";

class DimensionProperty extends Property {
    generate(){
		this.data = generateDimensionsControlStyles(this.args);
	}
}

export default DimensionProperty;