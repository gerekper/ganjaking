import React, { useState, useEffect } from "react";
import { __ } from "@wordpress/i18n";
import NestedMenu from "../utilities/NestedMenu";
import LeadingDocs from "../Overview/LeadingDocs";
import LeadingCategory from "../Overview/LeadingCategory";
import LeadingKnowledgeBase from "../Overview/LeadingKnowledgeBase";
import ChartWrapper from "../Overview/ChartWrapper";
import { getSettingOptions, Tooltip } from "../../function";
import { ReactComponent as Info } from "../../images/info.svg";

const Overview = () => {
  const [setting, setSetting] = useState(undefined);

  useEffect(() => {
    getSettingOptions()
      .then((res) => setSetting(res))
      .catch((err) => console.log(err));
  }, []);

  return (
    <div className="betterdocs-analytics-overview">
      <ChartWrapper />
      {setting ? (
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
                    tooltipContent="This list shows the leading Docs based on most Views."
                  />
                  Leading Docs
                </>
              ),
              content: <LeadingDocs />,
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
                    tooltipContent="This list shows the leading Categories based on most Views."
                  />
                  Leading Category
                </>
              ),
              content: <LeadingCategory />,
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
                    tooltipContent="This list shows the leading Knowledge Base based on most Views."
                  />
                  Leading Knowledge Base
                </>
              ),
              content: <LeadingKnowledgeBase />,
              enable: setting?.multiple_kb != "off" ? true : false,
            },
          ]}
        />
      ) : (
        ""
      )}
    </div>
  );
};

export default Overview;
