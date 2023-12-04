import React from "react";
import { Header, Body, Footer,  TitleCounts} from ".";

const SingleCategory = (props) => {
    const { widgetType } = props;
    const { tagType } = props;
    const {
        showHeader = true,
        showList = false,
        showButton = false,
        layout,
    } = props?.attributes;
    const CustomTag = tagType == "a" ? "a" : "div";
    const attributes = {};

    if (tagType == "a") {
        attributes["href"] = "#";
        attributes[
            "className"
        ] = `betterdocs-single-category-wrapper ${widgetType} default`;
    } else {
        attributes[
            "className"
        ] = `betterdocs-single-category-wrapper ${widgetType} default`;
    }

    return (
        <CustomTag {...attributes}>
            <div className="betterdocs-single-category-inner">
                {showHeader && (layout == 'default' ? <Header {...props} /> : <TitleCounts {...props}/>)}
                {showList && <Body {...props} />}
                {showButton && <Footer {...props} />}
            </div>
        </CustomTag>
    );
};

export default SingleCategory;
