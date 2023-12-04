import Property from "./Property";
import { generateResponsiveRangeStyles } from "../util/helpers";

class ResponsiveProperty extends Property {
    generate(){
		this.data = generateResponsiveRangeStyles(this.args);
        return this.filterData();
	}
}

export default ResponsiveProperty;