import React from 'react'
import { __ } from "@wordpress/i18n";
import htmlparser from 'html-react-parser'

const Instructions = ({ className = "", textdomain = "" }) => {
    return (
        <div className={`${className} wpdeveloper-licensing-instructions`}>
            <p>{htmlparser(__("Enter your license key here, to activate <strong>BetterDocs Pro</strong>, and get automatic updates and premium support.", textdomain))}</p>
            <p>Visit the <a rel="nofollow" href="https://betterdocs.co/docs/betterdocs-license/" target="_blank">Validation Guide</a> for help.</p>

            <ol>
                <li>Log in to <a rel="nofollow" href="https://store.wpdeveloper.com/" target="_blank">your account</a> to get your license key.</li>
                <li>If you don't yet have a license key, get <a rel="nofollow" href="https://betterdocs.co/upgrade" target="_blank">BetterDocs Pro now</a>.</li>
                <li>Copy the license key from your account and paste it below.</li>
                <li>Click on <strong>"Activate License"</strong> button.</li>
            </ol>
        </div>
    )
}

export default Instructions
