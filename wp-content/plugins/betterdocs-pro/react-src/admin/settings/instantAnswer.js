import { addFilter, addAction, doAction } from "@wordpress/hooks";

export const iaStyleSettings = {
    ia_luncher_bg: {
        settings: [
            {
                selector:
                    ".betterdocs-ia-launcher-wrapper .betterdocs-ia-launcher",
                property: "backgroundColor"
            },
            {
                selector:".betterdocs-ia-common-header",
                property: "backgroundColor"
            },
            {
                selector:".betterdocs-ia-common-header .betterdocs-ia-search .betterdocs-ia-search-icon",
                property: "backgroundColor"
            },
            {
                selector:".betterdocs-ia-docs-content .content-icon svg path",
                property:"fill"
            },
            {
                selector:".betterdocs-ia-single-docs-wrapper .betterdocs-ia-singleDoc-header .content-icon-back svg path",
                property:"fill"
            },
            {
                selector:".betterdocs-ia-single-docs-wrapper .betterdocs-ia-singleDoc-header .content-icon-expand svg path",
                property:"fill"
            },
            {
                selector:".betterdocs-ia-tabs li.active svg g path",
                property:"fill"
            },
            {
                selector:".betterdocs-ia-tab-message-container .betterdocs-ia-feedback-form .betterdocs-ia-submit button",
                property:"backgroundColor"
            },
            {
                selector:".betterdocs-ia-single-docs-wrapper .betterdocs-ia-singleDoc-footer .betterdocs-ia-footer-feedback",
                property:"backgroundColor"
            },
            {
                selector:".betterdocs-ia-tabs .active p",
                property:"color"
            }
        ],
    },
    ia_accent_color: {
        settings: [
            {
                selector:
                    ".betterdocs-conversation-container, .betterdocs-footer-wrapper, .betterdocs-launcher, .betterdocs-ask-wrapper .betterdocs-ask-submit",
                property: "backgroundColor",
            },
            {
                selector:
                    ".betterdocs-footer-wrapper .bd-ia-feedback-wrap, .betterdocs-footer-wrapper .bd-ia-feedback-response",
                property: "backgroundColor",
            },
            {
                selector:
                    ".betterdocs-header-wrapper .betterdocs-header .inner-container.betterdocs-active-answer .toggle:first-of-type > p, .betterdocs-header-wrapper .betterdocs-header .inner-container.betterdocs-active-ask .toggle:last-of-type > p",
                property: "color",
            },
            {
                selector:
                    ".betterdocs-header-wrapper .betterdocs-header .inner-container.betterdocs-active-answer .toggle:first-of-type svg, .betterdocs-header-wrapper .betterdocs-header .inner-container.betterdocs-active-ask .toggle:last-of-type svg",
                property: "fill",
            },
        ],
    },
    ia_sub_accent_color: {
        settings: [
            {
                selector:
                    ".betterdocs-header-wrapper .betterdocs-header .inner-container, .betterdocs-footer-wrapper .betterdocs-footer-emo > div",
                property: "background-color",
            },
        ],
    },
    // ia_heading_color: {
    //     settings: [
    //         {
    //             selector:
    //                 ".betterdocs-header-wrapper .betterdocs-sub-header.betterdocs-ans-header > h3, .betterdocs-header-wrapper .betterdocs-sub-header.betterdocs-ask-header > h3",
    //             property: "color",
    //         },
    //     ],
    // },
    ia_heading_font_size: {
        event: "keyup",
        suffix: "px",
        settings: [
            {
                selector:
                    ".betterdocs-header-wrapper .betterdocs-sub-header.betterdocs-ans-header > h3, .betterdocs-header-wrapper .betterdocs-sub-header.betterdocs-ask-header > h3",
                property: "font-size",
            },
        ],
    },
    // ia_sub_heading_color: {
    //     settings: [
    //         {
    //             selector:
    //                 ".betterdocs-header-wrapper .betterdocs-sub-header.betterdocs-ans-header > p, .betterdocs-header-wrapper .betterdocs-sub-header.betterdocs-ask-header > p",
    //             property: "color",
    //         },
    //     ],
    // },
    ia_sub_heading_size: {
        event: "keyup",
        suffix: "px",
        settings: [
            {
                selector:
                    ".betterdocs-header-wrapper .betterdocs-sub-header.betterdocs-ans-header > p, .betterdocs-header-wrapper .betterdocs-sub-header.betterdocs-ask-header > p",
                property: "font-size",
            },
        ],
    },
    // ia_searchbox_bg: {
    //     settings: [
    //         {
    //             selector:
    //                 ".betterdocs-tab-content-wrapper .bdc-search-box, .betterdocs-tab-content-wrapper .bdc-search-box .search-button, .betterdocs-tab-content-wrapper .bdc-search-box input",
    //             property: "background-color",
    //         },
    //     ],
    // },
    ia_searchbox_text: {
        settings: [
            {
                selector:
                    ".betterdocs-tab-content-wrapper .bdc-search-box input",
                property: "color",
            },
        ],
    },
    ia_searc_icon_color: {
        settings: [
            {
                selector:
                    ".betterdocs-tab-content-wrapper .bdc-search-box .search-button svg",
                property: "fill",
            },
        ],
    },
    iac_article_bg: {
        settings: [
            {
                selector:
                    ".betterdocs-messages-container .betterdocs-card-link",
                property: "background-color",
            },
        ],
    },
    iac_article_title: {
        settings: [
            {
                selector:
                    ".betterdocs-messages-container .betterdocs-card-link .betterdocs-card-title-wrapper .betterdocs-card-title",
                property: "color",
            },
        ],
    },
    iac_article_title_size: {
        event: "keyup",
        suffix: "px",
        settings: [
            {
                selector:
                    ".betterdocs-messages-container .betterdocs-card-link .betterdocs-card-title-wrapper .betterdocs-card-title",
                property: "font-size",
            },
        ],
    },
    iac_article_content: {
        settings: [
            {
                selector:
                    ".betterdocs-messages-container .betterdocs-card-link .betterdocs-card-body-wrapper .betterdocs-card-body",
                property: "color",
            },
        ],
    },
    iac_article_content_size: {
        event: "keyup",
        suffix: "px",
        settings: [
            {
                selector:
                    ".betterdocs-messages-container .betterdocs-card-link .betterdocs-card-body-wrapper .betterdocs-card-body",
                property: "font-size",
            },
        ],
    },
    ia_feedback_title_size: {
        event: "keyup",
        suffix: "px",
        settings: [
            {
                selector:
                    ".betterdocs-footer-wrapper .betterdocs-footer-label p",
                property: "font-size",
            },
        ],
    },
    ia_feedback_title_color: {
        settings: [
            {
                selector:
                    ".betterdocs-footer-wrapper .betterdocs-footer-label p",
                property: "color",
            },
        ],
    },
    ia_feedback_icon_color: {
        settings: [
            {
                selector: ".betterdocs-footer-wrapper .betterdocs-emo",
                property: "fill",
            },
        ],
    },
    ia_feedback_icon_size: {
        suffix: "px",
        settings: [
            {
                selector:
                    ".betterdocs-footer-wrapper .betterdocs-footer-emo > div",
                property: "width",
                multiple: 2,
            },
            {
                selector:
                    ".betterdocs-footer-wrapper .betterdocs-footer-emo > div",
                property: "height",
                multiple: 2,
            },
            {
                selector: ".betterdocs-footer-wrapper .betterdocs-emo",
                property: "width",
            },
            {
                selector: ".betterdocs-footer-wrapper .betterdocs-emo",
                property: "height",
            },
        ],
    },
    ia_response_icon_size: {
        suffix: "px",
        settings: [
            {
                selector:
                    ".betterdocs-footer-wrapper .bd-ia-feedback-response .feedback-success-icon",
                property: "width",
            },
        ],
    },
    ia_response_icon_color: {
        settings: [
            {
                selector:
                    ".betterdocs-footer-wrapper .bd-ia-feedback-response .feedback-success-icon",
                property: "fill",
            },
        ],
    },
    ia_response_title_size: {
        suffix: "px",
        settings: [
            {
                selector:
                    ".betterdocs-footer-wrapper .bd-ia-feedback-response .feedback-success-title",
                property: "font-size",
            },
        ],
    },
    ia_response_title_color: {
        settings: [
            {
                selector:
                    ".betterdocs-footer-wrapper .bd-ia-feedback-response .feedback-success-title",
                property: "color",
            },
        ],
    },
    ia_ask_bg_color: {
        settings: [
            {
                selector:
                    '.betterdocs-tab-ask .betterdocs-ask-wrapper input[type="text"], .betterdocs-tab-ask .betterdocs-ask-wrapper input[type="email"], .betterdocs-tab-ask .betterdocs-ask-wrapper textarea',
                property: "background-color",
            },
        ],
    },
    ia_ask_input_foreground: {
        settings: [
            {
                selector:
                    '.betterdocs-ask-wrapper input:not([type="submit"]), .betterdocs-ask-wrapper textarea, .betterdocs-ask-wrapper .betterdocs-attach-button',
                property: "color",
            },
        ],
    },
    ia_ask_send_disable_button_bg: {
        settings: [
            {
                selector:
                    ".betterdocs-ask-wrapper .betterdocs-ask-submit button:disabled",
                property: "background-color",
            },
        ],
    },
    ia_ask_send_disable_button_hover_bg: {
        settings: [
            {
                selector:
                    ".betterdocs-ask-wrapper .betterdocs-ask-submit button:disabled:hover",
                property: "background-color",
            },
        ],
    },
    ia_ask_send_button_bg: {
        settings: [
            {
                selector:
                    ".betterdocs-ask-wrapper .betterdocs-ask-submit button",
                property: "background-color",
            },
        ],
    },
    ia_ask_send_button_bg: {
        settings: [
            {
                selector:
                    ".betterdocs-ask-wrapper .betterdocs-ask-submit button:hover",
                property: "background-color",
            },
        ],
    },
    ia_ask_send_button_color: {
        settings: [
            {
                selector:
                    ".betterdocs-ask-wrapper .betterdocs-ask-submit button",
                property: "color",
            },
        ],
    },
    // ia_luncher_bg: {
    //     settings: [
    //         {
    //             selector:
    //                 ".betterdocs-launcher[type=button], .betterdocs-launcher[type=button]:focus",
    //             property: "background-color",
    //         },
    //     ],
    // },
    ia_luncher_bg_hover: {
        settings: [
            {
                selector: ".betterdocs-ia-launcher-wrapper",
                property: "background-color",
                type: "html",
                callback: (value, node) => {
                    if (node.querySelector("style") == null) {
                        let styleTag = document.createElement("style");
                        styleTag.textContent = `.betterdocs-ia-launcher-wrapper .betterdocs-ia-launcher:hover{background-color:${value};}`;
                        node.prepend(styleTag);
                    } else {
                        let existingStyleTag = node.querySelector("style");
                        existingStyleTag.textContent = `.betterdocs-ia-launcher-wrapper .betterdocs-ia-launcher:hover{background-color:${value};}`;
                    }
                },
            },
        ],
    },
    iac_docs_title_font_size: {
        event: "keyup",
        suffix: "px",
        settings: [
            {
                selector:
                    ".betterdocs-modal-content-container .betterdocs-entry-title .betterdocs-entry-title-link",
                property: "font-size",
            },
        ],
    },
    iac_article_content: {
        event: "keyup",
        suffix: "px",
        settings: [
            {
                selector:
                    ".betterdocs-messages-container .betterdocs-modal-wrapper .betterdocs-modal-content-container .betterdocs-ia-content h1",
                property: "font-size",
            },
        ],
    },
    iac_article_content_h1: {
        event: "keyup",
        suffix: "px",
        settings: [
            {
                selector:
                    ".betterdocs-messages-container .betterdocs-modal-wrapper .betterdocs-modal-content-container .betterdocs-ia-content h1",
                property: "font-size",
            },
        ],
    },
    iac_article_content_h2: {
        event: "keyup",
        suffix: "px",
        settings: [
            {
                selector:
                    ".betterdocs-messages-container .betterdocs-modal-wrapper .betterdocs-modal-content-container .betterdocs-ia-content h2",
                property: "font-size",
            },
        ],
    },
    iac_article_content_h3: {
        event: "keyup",
        suffix: "px",
        settings: [
            {
                selector:
                    ".betterdocs-messages-container .betterdocs-modal-wrapper .betterdocs-modal-content-container .betterdocs-ia-content h3",
                property: "font-size",
            },
        ],
    },
    iac_article_content_h4: {
        event: "keyup",
        suffix: "px",
        settings: [
            {
                selector:
                    ".betterdocs-messages-container .betterdocs-modal-wrapper .betterdocs-modal-content-container .betterdocs-ia-content h4",
                property: "font-size",
            },
        ],
    },
    iac_article_content_h5: {
        event: "keyup",
        suffix: "px",
        settings: [
            {
                selector:
                    ".betterdocs-messages-container .betterdocs-modal-wrapper .betterdocs-modal-content-container .betterdocs-ia-content h5",
                property: "font-size",
            },
        ],
    },
    iac_article_content_h6: {
        event: "keyup",
        suffix: "px",
        settings: [
            {
                selector:
                    ".betterdocs-messages-container .betterdocs-modal-wrapper .betterdocs-modal-content-container .betterdocs-ia-content h6",
                property: "font-size",
            },
        ],
    },
    iac_article_content_p: {
        event: "keyup",
        suffix: "px",
        settings: [
            {
                selector:
                    ".betterdocs-messages-container .betterdocs-modal-wrapper .betterdocs-modal-content-container .betterdocs-ia-content, .betterdocs-messages-container .betterdocs-modal-wrapper .betterdocs-modal-content-container .betterdocs-ia-content p",
                property: "font-size",
            },
        ],
    },
    // header_background_color: {
    //     settings: [
    //         {
    //             selector: ".betterdocs-ia-common-header",
    //             property: "backgroundColor",
    //         },
    //         {
    //             selector:
    //                 ".betterdocs-ia-tab-message-container .message__header",
    //             property: "backgroundColor",
    //         },
    //     ],
    // },
    ia_heading_color: {
        settings: [
            {
                selector:
                    ".betterdocs-ia-common-header .betterdocs-title:not(:last-child)",
                property: "color",
            },
            {
                selector: ".betterdocs-ia-common-header h2",
                property: "color",
            },
        ],
    },
    ia_sub_heading_color: {
        settings: [
            {
                selector: ".betterdocs-ia-common-header .betterdocs-info",
                property: "color",
            },
        ],
    },
    header_background_image: {
        settings: [
            {
                selector: ".betterdocs-ia-common-header",
                property: "backgroundImage",
                callback: (value) => {
                    if (value?.url == "") {
                        return "none";
                    }
                    return `url(${value?.url})`;
                },
            },
        ],
    },
    // upload_header_logo: {
    //     settings: [
    //         {
    //             selector: ".betterdocs-logo",
    //             property: "backgroundImage",
    //             type: "html",
    //             callback: (value, node) => {
    //                 const img = document.createElement("img");
    //                 const defaultLogo =
    //                     '<svg width="190" viewBox="0 0 366 85" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M63.063 32.6198H44.0562C42.1943 32.6198 40.6873 31.1128 40.6873 29.2509C40.6873 27.389 42.1943 25.882 44.0562 25.882H63.063C64.9249 25.882 66.4319 27.389 66.4319 29.2509C66.4319 31.1128 64.9249 32.6198 63.063 32.6198Z" fill="white"></path><path d="M55.4024 45.7461H36.4065C34.5446 45.7461 33.0376 44.2391 33.0376 42.3772C33.0376 40.5152 34.5446 39.0082 36.4065 39.0082H55.4024C57.2643 39.0082 58.7713 40.5152 58.7713 42.3772C58.7713 44.2391 57.2643 45.7461 55.4024 45.7461Z" fill="white"></path><path d="M47.6545 59.1016H28.6476C26.7857 59.1016 25.2787 57.5946 25.2787 55.7327C25.2787 53.8708 26.7857 52.3638 28.6476 52.3638H47.6545C49.5164 52.3638 51.0234 53.8708 51.0234 55.7327C51.0234 57.5946 49.5164 59.1016 47.6545 59.1016Z" fill="white"></path><path d="M77.0901 40.2531C76.97 40.002 76.8171 39.7727 76.6479 39.5597L81.7204 30.8235C84.4832 26.0731 84.4996 20.3891 81.7586 15.6224C79.0121 10.8502 74.0925 8 68.5832 8H45.6342C40.2559 8 35.2217 10.8993 32.497 15.5678L10.0831 54.1765C7.32026 58.9269 7.30388 64.6109 10.0449 69.3776C12.7913 74.1498 17.7109 77 23.2202 77H62.5443C69.5497 77 75.9599 73.4618 79.6892 67.5266C83.4185 61.5969 83.8334 54.2857 80.7976 47.9738L77.0901 40.2531ZM29.0189 69.8745H23.2148C20.2936 69.8745 17.6782 68.362 16.2203 65.8285C14.7679 63.3005 14.7734 60.2865 16.2422 57.7639L38.667 19.1496C40.114 16.6653 42.7894 15.1255 45.6451 15.1255H68.5941C71.5153 15.1255 74.1307 16.638 75.5886 19.1715C77.041 21.6995 77.0355 24.7135 75.5668 27.2361L53.131 65.8558C51.7004 68.3293 49.0359 69.869 46.1747 69.869H29.0189V69.8745Z" fill="white"></path><path d="M124.384 42.1533C125.82 41.4325 126.913 40.4279 127.65 39.1338C128.387 37.8397 128.758 36.3819 128.758 34.7657C128.758 33.5044 128.534 32.3359 128.081 31.2603C127.633 30.1791 126.929 29.2455 125.979 28.4537C125.023 27.662 123.822 27.045 122.364 26.5918C120.906 26.1441 119.17 25.9148 117.155 25.9148H104.433C104.127 25.9148 103.882 26.1605 103.882 26.4662V59.4183C103.882 59.6749 104.056 59.8988 104.302 59.9534C105.672 60.2646 107.212 60.5212 108.927 60.7178C110.816 60.9362 112.82 61.0399 114.944 61.0399C117.641 61.0399 119.951 60.7997 121.878 60.3137C123.8 59.8278 125.378 59.1343 126.601 58.2389C127.824 57.3379 128.725 56.235 129.299 54.9191C129.872 53.6087 130.161 52.1399 130.161 50.5237C130.161 48.4707 129.659 46.7398 128.649 45.3147C127.639 43.895 126.219 42.8412 124.384 42.1587V42.1533ZM110.516 31.9209C110.516 31.6152 110.767 31.364 111.073 31.364H116.129C118.214 31.3312 119.732 31.708 120.688 32.4997C121.638 33.2914 122.118 34.3725 122.118 35.7376C122.118 37.1791 121.643 38.2984 120.688 39.112C119.732 39.9201 118.231 40.3241 116.183 40.3241H111.078C110.772 40.3241 110.521 40.0729 110.521 39.7672V31.9264L110.516 31.9209ZM121.605 54.4004C120.328 55.2631 118.269 55.6944 115.43 55.6944C114.387 55.6944 113.486 55.6671 112.732 55.6125C112.126 55.5689 111.531 55.487 110.952 55.3614C110.701 55.3068 110.521 55.0774 110.521 54.8208V46.0464C110.521 45.7406 110.767 45.4949 111.073 45.4949H116.669C119.006 45.4949 120.732 45.9426 121.851 46.8435C122.965 47.7445 123.522 48.9839 123.522 50.5674C123.522 52.26 122.883 53.5322 121.605 54.4004Z" fill="white"></path><path d="M273.916 31.2275C272.278 29.715 270.302 28.5466 267.981 27.7221C265.66 26.8976 263.078 26.4826 260.239 26.4826H250.612C250.312 26.4826 250.066 26.7283 250.066 27.0287V60.5213C250.066 60.8216 250.312 61.0673 250.612 61.0673H260.239C263.078 61.0673 265.66 60.6523 267.981 59.8278C270.302 59.0033 272.278 57.8239 273.916 56.2951C275.554 54.7662 276.821 52.9426 277.722 50.8186C278.623 48.6946 279.071 46.3412 279.071 43.7531C279.071 41.165 278.623 38.8062 277.722 36.6877C276.821 34.5637 275.554 32.7509 273.916 31.2384V31.2275ZM271.541 48.5471C270.984 49.9504 270.176 51.1571 269.111 52.1617C268.052 53.1719 266.747 53.9527 265.202 54.5096C263.657 55.0666 261.877 55.345 259.862 55.345H257.257C256.952 55.345 256.706 55.0993 256.706 54.7935V32.7564C256.706 32.4506 256.952 32.2049 257.257 32.2049H259.862C261.877 32.2049 263.657 32.4833 265.202 33.0403C266.747 33.5972 268.052 34.3889 269.111 35.4155C270.171 36.442 270.979 37.6705 271.541 39.112C272.098 40.5535 272.376 42.1314 272.376 43.8623C272.376 45.5932 272.098 47.1548 271.541 48.558V48.5471Z" fill="white"></path><path d="M305.798 38.7516C304.581 37.5995 303.172 36.7095 301.567 36.0816C299.961 35.4537 298.225 35.137 296.358 35.137C294.49 35.137 292.797 35.4591 291.176 36.1089C289.554 36.7586 288.14 37.665 286.944 38.8335C285.743 40.002 284.793 41.3779 284.089 42.9614C283.384 44.5448 283.029 46.2538 283.029 48.0885C283.029 50.0323 283.373 51.7959 284.061 53.3739C284.749 54.9573 285.699 56.3224 286.917 57.4745C288.135 58.6266 289.554 59.5166 291.176 60.1445C292.797 60.7724 294.528 61.0891 296.358 61.0891C298.187 61.0891 299.912 60.7669 301.517 60.1172C303.123 59.4674 304.531 58.572 305.749 57.4199C306.967 56.2678 307.928 54.8918 308.632 53.292C309.336 51.6922 309.691 49.9558 309.691 48.083C309.691 46.2102 309.347 44.4301 308.659 42.8467C307.971 41.2633 307.021 39.8982 305.804 38.7461L305.798 38.7516ZM302.675 50.8895C302.342 51.7522 301.861 52.5057 301.244 53.1555C300.627 53.8052 299.901 54.3076 299.077 54.6679C298.247 55.0283 297.34 55.2085 296.352 55.2085C295.364 55.2085 294.457 55.0283 293.627 54.6679C292.798 54.3076 292.077 53.8052 291.46 53.1555C290.843 52.5057 290.368 51.7522 290.029 50.8895C289.696 50.0268 289.527 49.0931 289.527 48.083C289.527 47.0729 289.696 46.1392 290.029 45.2765C290.362 44.4138 290.843 43.6603 291.46 43.0105C292.077 42.3608 292.798 41.8584 293.627 41.498C294.457 41.1377 295.364 40.9575 296.352 40.9575C297.34 40.9575 298.247 41.1377 299.077 41.498C299.907 41.8584 300.627 42.3608 301.244 43.0105C301.861 43.6603 302.336 44.4138 302.675 45.2765C303.008 46.1392 303.177 47.0729 303.177 48.083C303.177 49.0931 303.008 50.0268 302.675 50.8895Z" fill="white"></path><path d="M331.603 54.7226C330.576 55.0447 329.31 55.2085 327.797 55.2085C326.683 55.2085 325.657 55.0283 324.723 54.668C323.789 54.3076 322.976 53.8162 322.293 53.1828C321.611 52.5549 321.081 51.7959 320.699 50.9168C320.322 50.0377 320.131 49.0931 320.131 48.083C320.131 47.0729 320.322 46.0791 320.699 45.1946C321.076 44.3155 321.605 43.5565 322.293 42.9286C322.976 42.3007 323.795 41.8038 324.75 41.4434C325.7 41.0831 326.754 40.9029 327.906 40.9029C329.31 40.9029 330.522 41.0722 331.548 41.4161C331.92 41.5417 332.264 41.6728 332.586 41.8147C332.952 41.9731 333.356 41.7165 333.356 41.3179V36.5402C333.356 36.3109 333.214 36.1034 333.001 36.027C332.264 35.7649 331.461 35.5574 330.582 35.4045C329.555 35.2243 328.414 35.137 327.158 35.137C325.253 35.137 323.473 35.47 321.818 36.1362C320.164 36.8023 318.733 37.7196 317.527 38.8881C316.32 40.0566 315.37 41.4216 314.665 42.9887C313.967 44.5557 313.612 46.2539 313.612 48.0885C313.612 49.9231 313.945 51.5775 314.611 53.161C315.277 54.7444 316.205 56.1204 317.39 57.2888C318.575 58.4573 320 59.3855 321.654 60.0681C323.309 60.7506 325.144 61.0946 327.158 61.0946C328.594 61.0946 329.79 60.9799 330.746 60.7451C331.537 60.5486 332.285 60.3356 332.99 60.1063C333.214 60.0353 333.361 59.8224 333.361 59.5876V54.8754C333.361 54.4878 332.968 54.2257 332.608 54.3731C332.296 54.4987 331.963 54.6188 331.608 54.728L331.603 54.7226Z" fill="white"></path><path d="M245.299 35.137H244.382C242.438 35.137 240.8 35.5956 239.473 36.5129C238.141 37.4303 237.207 38.5714 236.667 39.9365L236.334 35.7376C236.312 35.4373 236.044 35.208 235.739 35.2353L230.884 35.6775C230.59 35.7048 230.371 35.9615 230.388 36.2563L230.835 42.9013V60.5049C230.835 60.8106 231.081 61.0564 231.387 61.0564H236.76C237.065 61.0564 237.311 60.8106 237.311 60.5049V48.4052C237.311 46.2812 237.884 44.5558 239.036 43.2235C240.189 41.8912 241.805 41.2251 243.891 41.2251C244.338 41.2251 244.77 41.2469 245.174 41.296C245.501 41.3343 245.78 41.0776 245.78 40.75V35.4919C245.78 35.4919 245.758 35.1261 245.294 35.1261L245.299 35.137Z" fill="white"></path><path d="M223.481 54.0236C222.907 54.3458 222.296 54.6188 221.646 54.8317C220.996 55.0501 220.281 55.2194 219.489 55.345C218.698 55.4706 217.819 55.5361 216.847 55.5361C214.362 55.5361 212.424 54.9682 211.021 53.838C209.617 52.7023 208.809 51.2226 208.591 49.388C209.132 50.0377 209.978 50.5401 211.124 50.9004C212.277 51.2608 213.609 51.441 215.116 51.441C218.496 51.4082 221.111 50.6656 222.968 49.2296C224.819 47.7936 225.747 45.757 225.747 43.1306C225.747 40.8264 224.911 38.9209 223.241 37.4138C221.57 35.9014 219.113 35.1479 215.875 35.1479C213.898 35.1479 212.064 35.4919 210.371 36.1744C208.678 36.8569 207.215 37.8015 205.976 39.0082C204.736 40.2149 203.77 41.6345 203.087 43.2726C202.405 44.9106 202.061 46.6961 202.061 48.6399C202.061 50.5837 202.394 52.2709 203.06 53.8216C203.726 55.3668 204.654 56.6827 205.839 57.7584C207.024 58.8395 208.449 59.664 210.103 60.2427C211.758 60.8161 213.576 61.1054 215.553 61.1054C217.709 61.1054 219.544 60.8816 221.056 60.4284C222.422 60.0243 223.519 59.5657 224.354 59.0579C224.518 58.9596 224.606 58.7849 224.606 58.5938V54.3294C224.606 53.909 224.147 53.6414 223.781 53.8598C223.683 53.9199 223.579 53.9799 223.481 54.0345V54.0236ZM211.07 42.2898C212.435 41.1213 214.035 40.5371 215.869 40.5371C216.95 40.5371 217.813 40.7773 218.457 41.2633C219.107 41.7492 219.429 42.4426 219.429 43.3381C219.429 43.9879 219.238 44.5612 218.862 45.0635C218.485 45.5659 217.873 45.9808 217.027 46.303C216.181 46.6251 215.067 46.8599 213.68 47.0019C212.293 47.1438 210.595 47.1821 208.58 47.1111C208.869 45.0635 209.694 43.4528 211.064 42.2843L211.07 42.2898Z" fill="white"></path><path d="M355.655 48.225C354.393 46.9855 352.253 46.0409 349.234 45.3912C348.049 45.14 347.077 44.9161 346.318 44.7141C345.564 44.5175 344.975 44.3101 344.565 44.0917C344.15 43.8732 343.866 43.6494 343.702 43.4146C343.539 43.1798 343.462 42.9013 343.462 42.5792C343.462 41.105 344.975 40.3678 347.994 40.3678C349.359 40.3678 350.719 40.5316 352.067 40.8538C352.974 41.0722 353.858 41.3834 354.721 41.782C355.081 41.9458 355.491 41.6783 355.491 41.2797V36.846C355.491 36.6222 355.36 36.4201 355.158 36.3328C354.202 35.9396 353.165 35.6448 352.04 35.4537C350.779 35.2353 349.43 35.1315 347.994 35.1315C346.378 35.1315 344.909 35.3117 343.599 35.6721C342.283 36.0325 341.153 36.5457 340.197 37.2119C339.242 37.878 338.515 38.6861 338.013 39.6416C337.511 40.5972 337.26 41.6455 337.26 42.7976C337.26 44.8506 337.909 46.4777 339.203 47.679C340.497 48.8857 342.55 49.7757 345.351 50.349C346.575 50.5674 347.574 50.7803 348.344 50.9987C349.119 51.2171 349.731 51.4465 350.178 51.6976C350.626 51.9488 350.932 52.2218 351.096 52.5057C351.259 52.7951 351.336 53.1337 351.336 53.5323C351.336 55.0775 349.698 55.8528 346.427 55.8528C344.811 55.8528 343.178 55.6017 341.546 55.0993C340.465 54.7662 339.433 54.3076 338.455 53.7343C338.089 53.5213 337.636 53.7834 337.636 54.2093V58.6976C337.636 58.905 337.751 59.1016 337.937 59.1944C339.209 59.8333 340.53 60.3028 341.895 60.6031C343.369 60.9253 345.007 61.0891 346.804 61.0891C350.042 61.0891 352.641 60.3957 354.601 59.0142C356.561 57.6274 357.544 55.7327 357.544 53.3193C357.544 51.1625 356.916 49.459 355.655 48.2195V48.225Z" fill="white"></path><path d="M196.158 54.941C195.405 55.2304 194.433 55.3723 193.242 55.3723C192.052 55.3723 190.987 55.0229 190.141 54.3185C189.295 53.6196 188.874 52.402 188.874 50.6766V41.1814H196.961C197.261 41.1814 197.507 40.9357 197.507 40.6354V36.2236C197.507 35.9232 197.261 35.6775 196.961 35.6775H188.874V28.421C188.874 28.0934 188.585 27.8368 188.257 27.875L182.934 28.5466C182.661 28.5793 182.453 28.8141 182.453 29.0871L182.399 51.7522C182.399 54.8809 183.245 57.2179 184.932 58.7685C186.625 60.3138 188.869 61.0891 191.675 61.0891C193.04 61.0891 194.203 60.9635 195.154 60.7124C196.104 60.4612 196.89 60.1554 197.501 59.7951V59.7732V55.1921C197.501 54.7935 197.092 54.5315 196.732 54.6953C196.551 54.7772 196.36 54.8591 196.153 54.941H196.158Z" fill="white"></path><path d="M176.212 54.941C175.459 55.2304 174.487 55.3723 173.297 55.3723C172.106 55.3723 171.041 55.0229 170.195 54.3185C169.349 53.6196 168.928 52.402 168.928 50.6766V41.1814H176.993C177.293 41.1814 177.539 40.9357 177.539 40.6354V36.229C177.539 35.9287 177.293 35.683 176.993 35.683H168.928V28.4264C168.928 28.0988 168.639 27.8422 168.311 27.8804L162.988 28.552C162.715 28.5848 162.507 28.8196 162.507 29.0926L162.453 51.7577C162.453 54.8864 163.299 57.2233 164.986 58.774C166.679 60.3192 168.923 61.0946 171.729 61.0946C173.094 61.0946 174.257 60.969 175.208 60.7178C176.005 60.5103 176.682 60.2592 177.239 59.9752C177.419 59.8824 177.523 59.6913 177.523 59.4893V55.214C177.523 54.8154 177.113 54.5478 176.747 54.7171C176.578 54.7935 176.392 54.87 176.201 54.9464L176.212 54.941Z" fill="white"></path><path d="M155.819 54.0236C155.245 54.3458 154.634 54.6188 153.984 54.8317C153.334 55.0501 152.619 55.2194 151.827 55.345C151.035 55.4706 150.156 55.5361 149.184 55.5361C146.7 55.5361 144.762 54.9682 143.358 53.838C141.955 52.7023 141.147 51.2226 140.929 49.388C141.469 50.0377 142.316 50.5401 143.462 50.9004C144.614 51.2608 145.947 51.441 147.454 51.441C150.833 51.4082 153.449 50.6656 155.305 49.2296C157.156 47.7936 158.085 45.757 158.085 43.1306C158.085 40.8264 157.249 38.9209 155.578 37.4138C153.908 35.9014 151.45 35.1479 148.213 35.1479C146.236 35.1479 144.401 35.4919 142.709 36.1744C141.016 36.8569 139.553 37.8015 138.313 39.0082C137.074 40.2149 136.107 41.6345 135.425 43.2726C134.742 44.9106 134.398 46.6961 134.398 48.6399C134.398 50.5837 134.731 52.2709 135.398 53.8216C136.064 55.3668 136.992 56.6827 138.177 57.7584C139.362 58.8395 140.787 59.664 142.441 60.2427C144.096 60.8161 145.914 61.1054 147.89 61.1054C150.047 61.1054 151.882 60.8816 153.394 60.4284C154.765 60.0189 155.868 59.5602 156.703 59.0524C156.861 58.9541 156.954 58.7794 156.954 58.5883V54.3185C156.954 53.898 156.496 53.6305 156.135 53.8489C156.032 53.9144 155.928 53.9745 155.824 54.0345L155.819 54.0236ZM143.408 42.2898C144.773 41.1213 146.372 40.5371 148.207 40.5371C149.288 40.5371 150.151 40.7773 150.795 41.2633C151.445 41.7492 151.767 42.4426 151.767 43.3381C151.767 43.9879 151.576 44.5612 151.199 45.0635C150.823 45.5659 150.211 45.9808 149.365 46.303C148.518 46.6251 147.404 46.8599 146.018 47.0019C144.631 47.1438 142.933 47.1821 140.918 47.1111C141.207 45.0635 142.032 43.4528 143.402 42.2843L143.408 42.2898Z" fill="white"></path></svg>';
    //                 img.src = value?.url;
    //                 img.width = 100;
    //                 img.height = 20;

    //                 if (value != null) {
    //                     if (node.querySelector("svg") != null) {
    //                         node.querySelector("svg").remove();
    //                         node.prepend(img);
    //                     } else if (node.querySelector("img") != null) {
    //                         return;
    //                     }
    //                 } else {
    //                     if (node.querySelector("svg") != null) {
    //                         return;
    //                     } else if (node.querySelector("img") != null) {
    //                         node.querySelector("img").remove();
    //                         let existingDom = defaultLogo + "" + node.innerHTML;
    //                         node.innerHTML = existingDom;
    //                     }
    //                 }

    //                 return `url(${value?.url})`;
    //             },
    //         },
    //     ],
    // },
    ia_card_title_color: {
        settings: [
            {
                selector:
                    ".betterdocs-ia-docs .betterdocs-ia-docs-heading .doc-title",
                property: "color",
            },
        ],
    },
    ia_card_title_background_color: {
        settings: [
            {
                selector: ".betterdocs-ia-docs .betterdocs-ia-docs-heading",
                property: "background-color",
            },
        ],
    },
    ia_card_title_list_color: {
        settings: [
            {
                selector: ".betterdocs-ia-docs-content .content-item h4",
                property: "color",
            },
        ],
    },
    ia_card_list_description_color: {
        settings: [
            {
                selector: ".betterdocs-ia-docs-content .content-item p",
                property: "color",
            },
        ],
    },
    ia_card_list_background_color: {
        settings: [
            {
                selector: ".betterdocs-ia-docs-content",
                property: "background-color",
            },
        ],
    },
    // ia_card_list_arrow_color: {
    //     settings: [
    //         {
    //             selector: ".betterdocs-ia-docs-content .content-icon i",
    //             property: "color",
    //         },
    //     ],
    // },
    ia_searchbox_bg: {
        settings: [
            {
                selector: ".betterdocs-ia-common-header .betterdocs-ia-search, .betterdocs-ia-common-header .betterdocs-ia-search .betterdocs-ia-search-field",
                property: "background-color",
            },
        ],
    },
    ia_search_box_placeholder_text_color: {
        settings: [
            {
                selector:
                    ".betterdocs-ia-common-header .betterdocs-ia-search .betterdocs-ia-search-field",
                property: "color",
                type: "html",
                callback: (value, node) => {
                    if (node.parentElement.querySelector("style") == null) {
                        let styleTag = document.createElement("style");
                        styleTag.textContent = `.betterdocs-ia-common-header .betterdocs-ia-search .betterdocs-ia-search-field::placeholder{color:${value};}`;
                        node.parentElement.append(styleTag);
                    } else {
                        let existingStyleTag =
                            node.parentElement.querySelector("style");
                        existingStyleTag.textContent = `.betterdocs-ia-common-header .betterdocs-ia-search .betterdocs-ia-search-field::placeholder{color:${value};}`;
                    }
                },
            },
        ],
    },
    ia_search_box_input_text_color: {
        settings: [
            {
                selector:
                    ".betterdocs-ia-common-header .betterdocs-ia-search .betterdocs-ia-search-field",
                property: "color",
            },
        ],
    },
    ia_searc_icon_color: {
        settings: [
            {
                selector:
                    ".betterdocs-ia-common-header .betterdocs-ia-search .betterdocs-ia-search-icon svg",
                property: "fill",
            },
        ],
    },
    ia_launcher_tabs_background_color: {
        settings: [
            {
                selector: ".betterdocs-ia-tabs",
                property: "background-color",
            },
        ],
    },
    ia_launcher_tabs_text_color: {
        settings: [
            {
                selector: ".betterdocs-ia-tabs li p",
                property: "color",
            },
        ],
    },
    upload_home_icon: {
        settings: [
            {
                selector: ".betterdocs-ia-home",
                property: "backgroundImage",
                type: "html",
                callback: (value, node) => {
                    const defaultHomeSvg =
                        '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"><g clip-path="url(#a)"><path fill="#000" d="m23.4 10.392-.002-.002L13.608.6a2.194 2.194 0 0 0-1.562-.647c-.59 0-1.145.23-1.563.647L.698 10.385a2.212 2.212 0 0 0-.006 3.13 2.197 2.197 0 0 0 1.535.648h.39v7.204a2.589 2.589 0 0 0 2.586 2.586h3.83a.703.703 0 0 0 .703-.703v-5.648c0-.651.53-1.18 1.18-1.18h2.26c.65 0 1.18.529 1.18 1.18v5.648c0 .388.314.703.702.703h3.83a2.589 2.589 0 0 0 2.586-2.586v-7.204h.362c.59 0 1.145-.23 1.563-.648.86-.86.86-2.261.001-3.123Zm-.996 2.13a.798.798 0 0 1-.568.235h-1.065a.703.703 0 0 0-.703.703v7.907c0 .65-.529 1.18-1.18 1.18h-3.127v-4.945a2.589 2.589 0 0 0-2.586-2.586h-2.259a2.59 2.59 0 0 0-2.586 2.586v4.945H5.203c-.65 0-1.18-.53-1.18-1.18V13.46a.703.703 0 0 0-.703-.703H2.273a.797.797 0 0 1-.586-.236.804.804 0 0 1 0-1.136h.001l9.79-9.79a.797.797 0 0 1 .568-.236c.214 0 .416.084.568.236l9.787 9.787a.805.805 0 0 1 .003 1.14Z"></path></g><defs><clipPath id="a"><path fill="#fff" d="M0 0h24v24H0z"></path></clipPath></defs></svg>';
                    const img = document.createElement("img");
                    img.src = value?.url;
                    img.width = 24;
                    img.height = 24;

                    if (value != null) {
                        if (node.querySelector("svg") != null) {
                            node.querySelector("svg").remove();
                            node.prepend(img);
                        } else if (node.querySelector("img") != null) {
                            return;
                        }
                    } else {
                        if (node.querySelector("svg") != null) {
                            return;
                        } else if (node.querySelector("img") != null) {
                            node.querySelector("img").remove();
                            let existingDom =
                                defaultHomeSvg + "" + node.innerHTML;
                            node.innerHTML = existingDom;
                        }
                    }

                    return `url(${value?.url})`;
                },
            },
        ],
    },
    upload_sendmessage_icon: {
        settings: [
            {
                selector: ".betterdocs-ia-message",
                property: "backgroundImage",
                type: "html",
                callback: (value, node) => {
                    const defaultMessageSvg =
                        '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"><g fill="#000" clip-path="url(#a)"><path d="M2.768 18.719a4.668 4.668 0 0 1-2.296-.603.723.723 0 0 1-.288-1.013c.725-1.16.987-2.576.655-3.908C.495 11.815-.003 10.651 0 9.19.013 4.06 4.282-.098 9.406.002c4.949.1 9.027 4.26 9.027 9.21 0 6.465-6.775 11.004-12.757 8.508-.824.647-1.86.999-2.908.999Zm-.975-1.579c1.127.35 2.394.07 3.263-.77a.713.713 0 0 1 .803-.13c5.121 2.449 11.149-1.39 11.149-7.028 0-4.208-3.424-7.7-7.631-7.785-4.336-.086-7.94 3.426-7.951 7.766-.003 1.388.538 2.498.834 3.814a6.362 6.362 0 0 1-.467 4.133Z"></path><path d="M21.232 24a4.734 4.734 0 0 1-2.908-1c-3.181 1.328-6.965.724-9.573-1.529a.713.713 0 0 1 .931-1.079c2.314 1.998 5.702 2.447 8.459 1.13a.713.713 0 0 1 .803.13 3.305 3.305 0 0 0 3.263.77 6.335 6.335 0 0 1-.248-4.892c.41-.968.618-1.996.615-3.056-.004-1.87-.626-3.599-1.798-5.001a.713.713 0 1 1 1.094-.914A9.26 9.26 0 0 1 24 14.47a9.15 9.15 0 0 1-.719 3.594c-.503 1.459-.272 3.02.535 4.32a.723.723 0 0 1-.288 1.013 4.67 4.67 0 0 1-2.296.603ZM9.217 10.375a1.128 1.128 0 1 0 0-2.255 1.128 1.128 0 0 0 0 2.255ZM5.061 10.375a1.128 1.128 0 1 0 0-2.255 1.128 1.128 0 0 0 0 2.255ZM13.373 10.375a1.128 1.128 0 1 0 0-2.255 1.128 1.128 0 0 0 0 2.255Z"></path></g><defs><clipPath id="a"><path fill="#fff" d="M0 0h24v24H0z"></path></clipPath></defs></svg>';
                    const img = document.createElement("img");
                    img.src = value?.url;
                    img.width = 24;
                    img.height = 24;

                    if (value != null) {
                        if (node.querySelector("svg") != null) {
                            node.querySelector("svg").remove();
                            node.prepend(img);
                        } else if (node.querySelector("img") != null) {
                            return;
                        }
                    } else {
                        if (node.querySelector("svg") != null) {
                            return;
                        } else if (node.querySelector("img") != null) {
                            node.querySelector("img").remove();
                            let existingDom =
                                defaultMessageSvg + "" + node.innerHTML;
                            node.innerHTML = existingDom;
                        }
                    }

                    return `url(${value?.url})`;
                },
            },
        ],
    },
    upload_resource_icon: {
        settings: [
            {
                selector: ".betterdocs-ia-faq",
                property: "backgroundImage",
                type: "html",
                callback: (value, node) => {
                    const defaultMessageSvg =
                        '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"><g clip-path="url(#a)"><mask id="b" width="24" height="24" x="0" y="0" maskUnits="userSpaceOnUse" style="mask-type: luminance;"><path fill="#fff" d="M0 0h24v24H0V0Z"></path></mask><g fill="#202223" mask="url(#b)"><path fill-rule="evenodd" d="M16.242 22.547a6.307 6.307 0 0 1-6.183-5.07l-1.472.292a7.807 7.807 0 0 0 11.61 5.203l3.837 1.061-1.062-3.837A7.807 7.807 0 0 0 17.77 8.587l-.292 1.472a6.307 6.307 0 0 1 4.056 9.613l-.184.283.533 1.927-1.927-.533-.283.184a6.271 6.271 0 0 1-3.43 1.014Z" clip-rule="evenodd"></path><path fill-rule="evenodd" d="M-.047 9.164A9.211 9.211 0 1 1 4.5 17.108L-.033 18.36 1.22 13.83A9.172 9.172 0 0 1-.047 9.164Zm9.211-7.71A7.711 7.711 0 0 0 2.662 13.31l.18.281-.724 2.618 2.618-.724.281.18A7.71 7.71 0 1 0 9.164 1.453Z" clip-rule="evenodd"></path><path d="M9.867 14.11H8.461v-1.407h1.406v1.406Z"></path><path fill-rule="evenodd" d="M9.914 10.22v1.077h-1.5V9.56l1.667-1.525a1.36 1.36 0 1 0-2.276-1.003h-1.5a2.86 2.86 0 1 1 4.789 2.11l-1.18 1.079Z" clip-rule="evenodd"></path></g></g><defs><clipPath id="a"><path fill="#fff" d="M0 0h24v24H0z"></path></clipPath></defs></svg>';
                    const img = document.createElement("img");
                    img.src = value?.url;
                    img.width = 24;
                    img.height = 24;

                    if (value != null) {
                        if (node.querySelector("svg") != null) {
                            node.querySelector("svg").remove();
                            node.prepend(img);
                        } else if (node.querySelector("img") != null) {
                            return;
                        }
                    } else {
                        if (node.querySelector("svg") != null) {
                            return;
                        } else if (node.querySelector("img") != null) {
                            node.querySelector("img").remove();
                            let existingDom =
                                defaultMessageSvg + "" + node.innerHTML;
                            node.innerHTML = existingDom;
                        }
                    }

                    return `url(${value?.url})`;
                },
            },
        ],
    },
    launcher_open_icon: {
        settings: [
            {
                selector:
                    ".betterdocs-ia-launcher-wrapper .betterdocs-ia-launcher",
                property: "backgroundImage",
                type: "html",
                callback: (value, node) => {
                    doAction("openIconPreviewAction", {
                        img_url: value?.url,
                        preview: true,
                    });
                },
            },
        ],
    },
    launcher_close_icon: {
        settings: [
            {
                selector:
                    ".betterdocs-ia-launcher-wrapper .betterdocs-ia-launcher",
                property: "backgroundImage",
                type: "html",
                callback: (value, node) => {
                    doAction("closeIconPreviewAction", {
                        img_url: value?.url,
                        preview: true,
                    });
                },
            },
        ],
    },
    ia_message_tab_title_font_color: {
        settings: [
            {
                selector:
                    ".betterdocs-ia-tab-message-container .message__header .header__content h4",
                property: "color",
            },
        ],
    },
    ia_message_tab_subtitle_font_color: {
        settings: [
            {
                selector:
                    ".betterdocs-ia-tab-message-container .message__header .header__content p",
                property: "color",
            },
        ],
    },
    // ia_message_button_background_color: {
    //     settings: [
    //         {
    //             selector:
    //                 ".betterdocs-ia-tab-message-container .betterdocs-ia-feedback-form .betterdocs-ia-submit button",
    //             property: "background-color",
    //         },
    //     ],
    // },
    ia_message_button_text_color: {
        settings: [
            {
                selector:
                    ".betterdocs-ia-tab-message-container .betterdocs-ia-feedback-form .betterdocs-ia-submit button",
                property: "color",
            },
        ],
    },
    ia_ask_bg_color: {
        settings: [
            {
                selector:
                    ".betterdocs-ia-tab-message-container .betterdocs-ia-feedback-form .betterdocs-ia-group .ia-input, .betterdocs-ia-tab-message-container .betterdocs-ia-feedback-form .betterdocs-ia-group > textarea",
                property: "background-color",
            },
        ],
    },
    ia_message_input_label_text_color: {
        settings: [
            {
                selector:
                    ".betterdocs-ia-tab-message-container .betterdocs-ia-feedback-form .betterdocs-ia-email-group p, .betterdocs-ia-tab-message-container .betterdocs-ia-feedback-form .betterdocs-ia-name-group p, .betterdocs-ia-tab-message-container .betterdocs-ia-feedback-form .betterdocs-ia-subject-group p, .betterdocs-ia-tab-message-container .betterdocs-ia-feedback-form .betterdocs-ia-message-group p",
                property: "color",
            },
        ],
    },
    ia_message_upload_button_background_color: {
        settings: [
            {
                selector:
                    ".betterdocs-ia-tab-message-container .betterdocs-ia-feedback-form .betterdocs-ia-attachments-group button",
                property: "background-color",
            },
        ],
    },
    ia_message_upload_text_color: {
        settings: [
            {
                selector:
                    ".betterdocs-ia-tab-message-container .betterdocs-ia-feedback-form .betterdocs-ia-attachments-group p",
                property: "color",
            },
        ],
    },
    ia_single_doc_title_font_color: {
        settings: [
            {
                selector:
                    ".betterdocs-ia-single-docs-wrapper .betterdocs-ia-singleDoc-content .doc-title",
                property: "color",
            },
        ],
    },
    ia_single_title_header_font_color: {
        settings: [
            {
                selector:
                    ".betterdocs-ia-single-docs-wrapper .betterdocs-ia-singleDoc-header.on-scroll h2",
                property: "color",
            },
        ],
    },
    ia_single_doc_title_header_bg_color: {
        settings: [
            {
                selector:
                    ".betterdocs-ia-single-docs-wrapper .betterdocs-ia-singleDoc-header",
                property: "background-color",
            },
        ],
    },
    // ia_single_doc_back_icon_color: {
    //     settings: [
    //         {
    //             selector:
    //                 ".betterdocs-ia-single-docs-wrapper .betterdocs-ia-singleDoc-header .content-icon-back i",
    //             property: "color",
    //         },
    //     ],
    // },
    // ia_single_doc_back_icon_hover_color: {
    //     settings: [
    //         {
    //             selector:".betterdocs-ia-single-docs-wrapper .betterdocs-ia-singleDoc-header .content-icon-back .ia-angle-left",
    //             property: "color",
    //             type: "html",
    //             callback: (value, node) => {
    //                 if (node.parentElement.querySelector("style") == null) {
    //                     let styleTag = document.createElement("style");
    //                     styleTag.textContent = `.betterdocs-ia-single-docs-wrapper .betterdocs-ia-singleDoc-header .content-icon-back:hover{color:${value};}`;
    //                     node.parentElement.append(styleTag);
    //                 } else {
    //                     let existingStyleTag =
    //                         node.parentElement.querySelector("style");
    //                     existingStyleTag.textContent = `.betterdocs-ia-single-docs-wrapper .betterdocs-ia-singleDoc-header .content-icon-back:hover{color:${value};}`;
    //                 }
    //             },
    //         },
    //     ],
    // },
    // ia_single_expand_icon_color: {
    //     settings: [
    //         {
    //             selector:
    //                 ".betterdocs-ia-single-docs-wrapper .betterdocs-ia-singleDoc-header .content-icon-expand i",
    //             property: "color",
    //         },
    //     ],
    // },
    // ia_single_expand_icon_hover_color: {
    //     settings: [
    //         {
    //             selector:
    //                 ".betterdocs-ia-single-docs-wrapper .betterdocs-ia-singleDoc-header .content-icon-expand i:hover",
    //             property: "color",
    //         },
    //     ],
    // },
    ia_single_icons_bg_color: {
        settings: [
            {
                selector:
                    ".betterdocs-ia-single-docs-wrapper .betterdocs-ia-singleDoc-header .content-icon-back",
                property: "backgroundColor",
            },
            {
                selector:
                    ".betterdocs-ia-single-docs-wrapper .betterdocs-ia-singleDoc-header .content-icon-expand",
                property: "backgroundColor",
            },
        ],
    },
    ia_reaction_primary_color: {
        settings: [
            {
                selector:
                    ".betterdocs-ia-single-docs-wrapper .betterdocs-ia-singleDoc-footer .betterdocs-ia-footer-feedback .betterdocs-ia-reaction-group .ia-reaction",
                property: "backgroundColor",
            },
        ],
    },
    ia_reaction_secondary_color: {
        settings: [
            {
                selector:
                    ".betterdocs-ia-single-docs-wrapper .betterdocs-ia-singleDoc-footer .betterdocs-ia-footer-feedback .betterdocs-ia-reaction-group .ia-reaction .betterdocs-emo",
                property: "fill",
            },
        ],
    },
    ia_reaction_title_color: {
        settings: [
            {
                selector:
                    ".betterdocs-ia-single-docs-wrapper .betterdocs-ia-singleDoc-footer .betterdocs-ia-footer-feedback p",
                property: "color",
            },
        ],
    },
};
/**
 * Controls Left Are
 * Primary Color, Launcher Hover Background Color, Instant Answer Open Icon, Instant Answer Close Icon,
 */
const setIAStyles = (key, value) => {
    const originalValue = value;
    let keySettings = iaStyleSettings?.[key];
    if (keySettings == undefined) {
        return;
    }

    let isMultiplied = true;
    let { settings, suffix } = keySettings;
    suffix = suffix == undefined ? "" : suffix;

    settings.map(function (single, j) {
        let {
            multiple,
            property,
            selector,
            type = undefined,
            callback = undefined,
        } = single;
        multiple = multiple != undefined ? multiple : 1;

        if (multiple === 1) {
            value = originalValue;
        }

        if (suffix === "px" && isMultiplied) {
            value = value * multiple;
            isMultiplied = false;
        }

        let htmlNode = document.querySelectorAll(selector);
        if (htmlNode?.length > 0) {
            htmlNode.forEach((node) => {
                if (type != undefined && typeof callback == "function") {
                    callback(value, node);
                } else if (typeof callback == "function") {
                    node.style[property] = callback(value);
                } else {
                    node.style[property] = value + suffix;
                }
            });
        }
    });
};

export default setIAStyles;
