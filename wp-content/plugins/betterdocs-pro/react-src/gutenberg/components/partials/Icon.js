import React from 'react'

import ImporterIcon from "../images/betterdocs-cat-icon.svg";

const Icon = ({ icon }) => {
    const iconURL = icon ?? ImporterIcon;
    return (
        <div className="betterdocs-category-icon">
            <img alt="betterdocs-category-icon" className="betterdocs-category-icon-img" src={iconURL} />
        </div>
    )
}

export default Icon
