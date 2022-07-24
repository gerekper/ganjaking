import React, { useState } from "react";
import { Tab, Tabs, TabList, TabPanel } from "react-tabs";
import "react-tabs/style/react-tabs.css";

const NestedMenu = (props) => {
  const {
    tabWrapperClass,
    tabListClass,
    tabLinkClass,
    tabLinkActiveClass,
    tabContentClass,
    options,
  } = props;
  const [tabIndex, setTabIndex] = useState(0);
  const handleTab = (index) => {
    setTabIndex(index);
  };
  return (
    <>
      <Tabs
        selectedIndex={tabIndex}
        onSelect={(index) => handleTab(index)}
        className={tabWrapperClass}
      >
        <TabList className={tabListClass}>
          {options &&
            options.length &&
            options.map(
              (item) =>
                item?.enable && (
                  <Tab
                    className={tabLinkClass}
                    selectedClassName={tabLinkActiveClass}
                    key={Math.random()}
                  >
                    {item?.link}
                  </Tab>
                )
            )}
        </TabList>

        {options &&
          options.length &&
          options.map(
            (item) =>
              item?.enable && (
                <TabPanel className={tabContentClass} key={Math.random()}>
                  {item?.content}
                </TabPanel>
              )
          )}
      </Tabs>
    </>
  );
};

export default NestedMenu;
