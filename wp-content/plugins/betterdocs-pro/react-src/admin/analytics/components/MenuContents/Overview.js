import React, { useState, forwardRef } from "react";
import { __ } from "@wordpress/i18n";
import { useQuery } from "@tanstack/react-query";
import NestedMenu from "../utilities/NestedMenu";
import LeadingDocs from "../Overview/LeadingDocs";
import LeadingCategory from "../Overview/LeadingCategory";
import LeadingKnowledgeBase from "../Overview/LeadingKnowledgeBase";
import ChartWrapper from "../Overview/ChartWrapper";
import { getSettingOptions, Tooltip } from "../../function";
import { ReactComponent as Info } from "../../images/info.svg";

const Overview = forwardRef((props, mainComponentRef) => {
    const [postDetails, setPostDetails] = useState(undefined);
    const [dateRange, setDateRange] = useState({});

    const setting = useQuery(["pluginSetting"], getSettingOptions);

    return (
        <div className="betterdocs-analytics-overview">
            <ChartWrapper
                postDetails={postDetails}
                setPostDetails={setPostDetails}
                dateRange={dateRange}
                setDateRange={setDateRange}
            />
            {setting?.data ? (
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
                                            "This list shows the leading Docs based on most Views.",
                                            "betterdocs-pro"
                                        )}
                                    />
                                    {__("Leading Docs", "betterdocs-pro")}
                                </>
                            ),
                            content: (
                                <LeadingDocs
                                    dateRange={dateRange}
                                    setPostDetails={setPostDetails}
                                    ref={mainComponentRef}
                                />
                            ),
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
                                            "This list shows the leading Categories based on most Views.",
                                            "betterdocs-pro"
                                        )}
                                    />
                                    {__("Leading Category", "betterdocs-pro")}
                                </>
                            ),
                            content: <LeadingCategory dateRange={dateRange} />,
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
                                            "This list shows the leading Knowledge Base based on most Views.",
                                            "betterdocs-pro"
                                        )}
                                    />
                                    {__("Leading Knowledge Base", "betterdocs-pro")}
                                </>
                            ),
                            content: <LeadingKnowledgeBase dateRange={dateRange} />,
                            enable: setting?.data?.multiple_kb ?? false,
                        },
                    ]}
                />
            ) : (
                ""
            )}
        </div>
    );
});

export default Overview;
