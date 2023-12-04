import React from 'react'
import { Button } from './partials'

const Footer = ({ attributes: { buttonText, buttonIcon, showButtonIcon, buttonIconPosition }, link }) => {
    return (
        <div className='betterdocs-footer'>
            <Button
                permalink={link}
                label={buttonText}
                showIcon={showButtonIcon}
                icon={buttonIcon}
                position={buttonIconPosition}
            />
        </div>
    )
}

export default Footer
