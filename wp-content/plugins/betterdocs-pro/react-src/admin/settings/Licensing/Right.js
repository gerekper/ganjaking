import React from 'react'

const Right = ({ logoURL, buttonText }) => {
    return (
        <div className="wpdeveloper-licensing-right-inner-container">
            <img src={logoURL} alt={buttonText} />
            <a href="https://store.wpdeveloper.com" target="_blank" rel='nofollow' className="wpdeveloper-store-url">{ buttonText }</a>
        </div>
    )
}

export default Right
