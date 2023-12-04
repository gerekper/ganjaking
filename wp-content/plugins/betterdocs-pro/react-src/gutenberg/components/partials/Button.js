import react from 'react';
import { __ } from '@wordpress/i18n';


const ButtonIcon = ({ icon, position }) => {
    const newPosition = position == 'before' ? 'left' : 'right';
    return (
        <i className={`${icon} betterdocs-category-link-btn betterdocs-category-link-btn-${newPosition}`}></i>
    )
}

const Button = ({ label, permalink, showIcon, position, icon }) => {
    return (
        <a onClick={(e) => e.preventDefault()} className="docs-cat-link-btn betterdocs-category-link-btn" href={permalink}>
            {showIcon && position === 'before' && <ButtonIcon icon={icon} position={position} />}
            {label ?? __('Explore More', 'betterdocs')}
            {showIcon && position === 'after' && <ButtonIcon icon={icon} position={position} />}
        </a>
    )
}

export default Button;
