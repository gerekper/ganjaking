import React, { useState } from "react";
import { Tab, Tabs, TabList, TabPanel } from "react-tabs";
import { __ } from "@wordpress/i18n";
import Overview from "./MenuContents/Overview";
import Views from "./MenuContents/Views";
import Reactions from "./MenuContents/Reactions";
import KeywordsSearch from "./MenuContents/KeywordsSearch";
import "react-tabs/style/react-tabs.css";

const AnalyticsMenu = () => {
  const [tabIndex, setTabIndex] = useState(0);
  const mainComponentRef = React.useRef();
  const handleTab = (index) => {
    setTabIndex(index);
  };
  return (
    <div ref={mainComponentRef}>
      <Tabs
        selectedIndex={tabIndex}
        onSelect={(index) => handleTab(index)}
        className="betterdocs-analytics-tab-wrapper"
      >
        <TabList className="betterdocs-analytics-tab-menu">
          <Tab
            className="betterdocs-analytics-tab-item"
            selectedClassName="active"
          >
            {__("Overview", "betterdocs-pro")}
          </Tab>
          <Tab
            className="betterdocs-analytics-tab-item"
            selectedClassName="active"
          >
            {__("Views", "betterdocs-pro")}
          </Tab>
          <Tab
            className="betterdocs-analytics-tab-item"
            selectedClassName="active"
          >
            {__("Reactions", "betterdocs-pro")}
          </Tab>
          <Tab
            className="betterdocs-analytics-tab-item"
            selectedClassName="active"
          >
            {__("Search", "betterdocs-pro")}
          </Tab>
        </TabList>

        <TabPanel className="betterdocs-analytics-tab-content">
          <Overview ref={mainComponentRef} />
        </TabPanel>
        <TabPanel className="betterdocs-analytics-tab-content">
          <Views ref={mainComponentRef} />
        </TabPanel>
        <TabPanel className="betterdocs-analytics-tab-content">
          <Reactions />
        </TabPanel>
        <TabPanel className="betterdocs-analytics-tab-content">
          <KeywordsSearch />
        </TabPanel>
      </Tabs>
    </div>
  );
};

export default AnalyticsMenu;
