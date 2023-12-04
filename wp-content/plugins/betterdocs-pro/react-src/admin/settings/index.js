import { addFilter, addAction } from "@wordpress/hooks";

import ManageLicense from "./licensing";
import setIAStyles, { iaStyleSettings } from "./instantAnswer";

const LicenseManager = () => {
    const license = new ManageLicense({
        logo: betterdocsAdminSettings?.logoURL,
    });

    return <div>{license.render()}</div>;
};

addFilter("betterdocs_settings_licnese", "BetterDocs", LicenseManager);

/**
 * This action below is responsible for reacting with setting update.
 * If any changes in state this action callback will trigger. And so the code will trigger some visual changes as well.
 */
addAction(
    "quickBuilder_setFieldValue",
    "betterdocsPro",
    (field, value, validProps) => {
        if (field === "multiple_kb") {
            if (value == false) {
                jQuery(
                    "#toplevel_page_betterdocs-admin .wp-submenu li:last-of-type"
                ).remove();
            } else {
                jQuery("#toplevel_page_betterdocs-admin .wp-submenu").append(
                    `<li><a href="${betterdocsProAdminSettings?.multiple_kb_url}">Multiple KB</a></li>`
                );
            }
        }

        /**
         * This snippets of codes apply style to Instant Answer.
         */
        if (iaStyleSettings?.[field] != undefined) {
            setIAStyles(field, value);
        }

        if ("ia_enable_preview" === field) {
            if (value) {
                jQuery("#betterdocs-ia").show();
            } else {
                jQuery("#betterdocs-ia").hide();
            }
        }
    }
);
