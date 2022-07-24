import React from "react";
import { __ } from "@wordpress/i18n";
import NestedMenu from "../utilities/NestedMenu";
import ChartWrapper from "../Reactions/ChartWrapper";
import MostHelpful from "../Reactions/MostHelpful";
import LeastHelpful from "../Reactions/LeastHelpful";
import { Tooltip } from "../../function";
import { ReactComponent as Info } from "../../images/info.svg";

const Reactions = () => {
  return (
    <div className="betterdocs-analytics-reactions">
      <ChartWrapper />
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
                  tooltipContent="This list shows the most helpful docs based on most happy reactions."
                />
                Most Helpful
              </>
            ),
            content: <MostHelpful />,
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
                  tooltipContent="This list shows the least helpful docs based on most unhappy reactions."
                />
                Least Helpful
              </>
            ),
            content: <LeastHelpful />,
            enable: true,
          },
        ]}
      />
    </div>
  );
};

export default Reactions;
