import React, { useState } from "react";
import { __ } from "@wordpress/i18n";
import NestedMenu from "../utilities/NestedMenu";
import ChartWrapper from "../Reactions/ChartWrapper";
import MostHelpful from "../Reactions/MostHelpful";
import LeastHelpful from "../Reactions/LeastHelpful";
import { Tooltip } from "../../function";
import { ReactComponent as Info } from "../../images/info.svg";

const Reactions = () => {
    const [dateRange, setDateRange] = useState({});

    return (
        <div className="betterdocs-analytics-reactions">
            <ChartWrapper dateRange={dateRange} setDateRange={setDateRange} />
            <NestedMenu
                tabWrapperClass="betterdocs-analytics-nested-tab-wrapper"
                tabListClass="betterdocs-analytics-nested-tab-menu"
                tabLinkClass="betterdocs-analytics-nested-tab-item"
                tabLinkActiveClass="active"
                tabContentClass="betterdocs-analytics-nested-tab-content"
                options={[
                    {
                        link: (
                            <>
                                <Tooltip
                                    className="btd-nested-tooltip"
                                    buttonClassName="btd-nested-tooltip-button"
                                    tooltipClassName="btd-nested-tooltip-content"
                                    buttonContent={<Info />}
                                    tooltipContent={__(
                                        "This list shows the most helpful docs based on most happy reactions.",
                                        "betterdocs-pro"
                                    )}
                                />
                                {__("Most Helpful", "betterdocs-pro")}
                            </>
                        ),
                        content: <MostHelpful dateRange={dateRange} />,
                        enable: true,
                    },
                    {
                        link: (
                            <>
                                <Tooltip
                                    className="btd-nested-tooltip"
                                    buttonClassName="btd-nested-tooltip-button"
                                    tooltipClassName="btd-nested-tooltip-content"
                                    buttonContent={<Info />}
                                    tooltipContent={__(
                                        "This list shows the least helpful docs based on most unhappy reactions.",
                                        "betterdocs-pro"
                                    )}
                                />
                                {__("Least Helpful", "betterdocs-pro")}
                            </>
                        ),
                        content: <LeastHelpful dateRange={dateRange} />,
                        enable: true,
                    },
                ]}
            />
        </div>
    );
};

export default Reactions;
