import Property from "./Property";

class AlignProperty extends Property {
    generate() {
        this.data = { desktop: `text-align: ${this.attributes[this.id]};` };
    }
}

export default AlignProperty;
