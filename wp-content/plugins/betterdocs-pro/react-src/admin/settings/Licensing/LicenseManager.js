import { __ } from "@wordpress/i18n";
import htmlparser from "html-react-parser";
import React, { useState } from "react";

import Form from "./Form";

const steps = [
    <>
        {htmlparser(
            sprintf(
                __(
                    "Log in to <a href='%s' target='_blank'>your account</a> to get your license key.",
                    "betterdocs"
                ),
                "https://store.wpdeveloper.com/"
            )
        )}
    </>,
    <>
        {htmlparser(
            sprintf(
                __(
                    "If you don't yet have a license key, get <a href='%s' target='_blank'>BetterDocs Pro</a> now.",
                    "betterdocs"
                ),
                "https://betterdocs.co/upgrade"
            )
        )}
    </>,
    <>
        {__(
            "Copy the license key from your account and paste it below.",
            "betterdocs"
        )}
    </>,
    <>{__('Click on "Activate License" button.', "betterdocs")}</>,
];

const LicenseManager = ({ apiFetch, licenseData, config, ...props }) => {
    const [isActive, setIsActive] = useState(
        licenseData?.license_status === "valid"
    );
    const [showSteps, setShowSteps] = useState(false);

    return (
        <div
            className={`wpdeveloper-licensing-wrapper ${
                props?.className ?? ""
            }`}
        >
            <div className="wpdeveloper-licensing-header">
                <div className="wpdeveloper-licensing-left-container">
                    <div
                        className={`icon ${
                            isActive ? "activated" : "deactivated"
                        }`}
                    >
                        <i
                            className={`btd-icon btd-${
                                isActive ? "lock-open" : "lock"
                            }`}
                        ></i>
                    </div>
                    <div className="content">
                        <h3 className="heading">
                            {isActive
                                ? __(
                                      "You Have Activated BetterDocs PRO",
                                      "betterdocs-pro"
                                  )
                                : __(
                                      "Unlock With Your License Key",
                                      "betterdocs-pro"
                                  )}
                        </h3>
                        <p className="description">
                            {isActive
                                ? __(
                                      "Congratulations! Enjoy premium features, and get automatic updates & priority support!",
                                      "betterdocs-pro"
                                  )
                                : __(
                                      "Enter your license key in the input field below to activate BetterDocs PRO to unlock all the premium features.",
                                      "betterdocs-pro"
                                  )}
                        </p>
                    </div>
                </div>
                <div className="wpdeveloper-licensing-right-container">
                    {isActive ? (
                        <p className="active-badge">
                            <i className="btd-icon btd-tick"></i>
                            {__("Activated", "betterdocs-pro")}
                        </p>
                    ) : (
                        <p
                            className={`step-button ${
                                showSteps ? "show" : "hide"
                            }`}
                            onClick={() => setShowSteps(!showSteps)}
                        >
                            <span className="text">
                                {__(
                                    "How to get license key?",
                                    "betterdocs-pro"
                                )}
                            </span>
                            <span className="icon">
                                <i className="btd-icon btd-arrow-up"></i>
                            </span>
                        </p>
                    )}
                </div>
            </div>
            <div
                className={`wpdeveloper-licensing-steps-wrapper ${
                    showSteps ? "show" : "hide"
                } ${isActive ? "hidden" : ""}`}
            >
                <ul className="wpdeveloper-licensing-steps">
                    {steps?.map((step, index) => (
                        <li key={index} className="wpdeveloper-licensing-step">
                            <span className="wpdeveloper-licensing-step-count">
                                {++index}
                            </span>
                            <span className="wpdeveloper-licensing-step-content">
                                {step}
                            </span>
                        </li>
                    ))}
                </ul>
            </div>
            <div
                className={`wpdeveloper-licensing-form ${
                    isActive ? "activated" : "deactivated"
                }`}
            >
                <Form
                    apiFetch={apiFetch}
                    licenseData={licenseData}
                    textdomain={config.textdomain}
                    setIsLicenseActive={setIsActive}
                />
            </div>
        </div>
    );
};

export default LicenseManager;
