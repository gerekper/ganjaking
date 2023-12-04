import { __ } from "@wordpress/i18n";
import React, { useEffect, useState } from "react";

const Form = ({
    apiFetch,
    setIsLicenseActive,
    licenseData,
    textdomain,
    className = "",
}) => {
    const [licenseActive, setLicenseActive] = useState(
        licenseData?.license_status === "valid"
    );
    const [action, setAction] = useState(null);
    const [isDisable, setIsDisable] = useState(true);
    const [licenseKey, setLicenseKey] = useState(
        licenseData?.license_key ?? ""
    );
    const [isLoading, setIsLoading] = useState(null);

    useEffect(() => {
        if (licenseKey !== "") {
            setIsDisable(false);
        } else {
            setIsDisable(true);
        }
    }, [licenseKey]);

    useEffect(() => {
        if (action !== null) {
            setIsLoading(true);
            apiFetch
                .post(action ? "license/activate" : "license/deactivate", {
                    data: { license_key: licenseKey },
                })
                .then((res) => {
                    if (res?.license === "deactivated") {
                        setLicenseKey("");
                        setLicenseActive(false);
                        setIsLicenseActive(false);
                    }

                    if (res?.license === "valid") {
                        setLicenseKey(res?.license_key);
                        setLicenseActive(true);
                        setIsLicenseActive(true);
                    }

                    setIsLoading(false);
                });

            setAction(null);
        }
    }, [action]);

    return (
        <label className={`${className} wpdeveloper-licensing-form-inner`}>
            <div className="betterdocs-license-icon">
                <i className="btd-icon btd-key"></i>
            </div>
            <input
                type="text"
                className="wpdeveloper-licensing-form-input"
                value={licenseKey}
                disabled={licenseActive}
                placeholder={__(
                    "Place Your License Key and Activate",
                    textdomain
                )}
                onChange={({ target }) => setLicenseKey(target?.value)}
            />
            <button
                name="activate"
                className={`wpdeveloper-licensing-form-button ${
                    licenseActive ? "activated" : "deactivated"
                }`}
                disabled={isDisable || isLoading}
                onClick={() => setAction(!licenseActive)}
            >
                {!licenseActive &&
                    isLoading &&
                    __("Activating License...", textdomain)}
                {!licenseActive &&
                    !isLoading &&
                    __("Activate License", textdomain)}
                {licenseActive &&
                    isLoading &&
                    __("Deactivating License...", textdomain)}
                {licenseActive &&
                    !isLoading &&
                    __("Deactivate License", textdomain)}
            </button>
        </label>
    );
};

export default Form;
