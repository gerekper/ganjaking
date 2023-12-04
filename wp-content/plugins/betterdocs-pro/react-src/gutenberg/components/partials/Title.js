import React from 'react'

const Title = ({ title, titleTag = 'h2' }) => {
    return React.createElement(
        titleTag,
        { className: 'betterdocs-category-title' },
        title
    );
}

export default Title
