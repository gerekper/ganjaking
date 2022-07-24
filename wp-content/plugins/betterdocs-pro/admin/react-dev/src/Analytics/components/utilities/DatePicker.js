import React, { useState, useEffect } from "react";
import { __ } from "@wordpress/i18n";
import { subDays } from "date-fns";
import { DateRangePicker } from "react-date-range";
import { Tab, Tabs, TabList } from "react-tabs";
import "react-tabs/style/react-tabs.css";

const DatePicker = ({ setDateRange }) => {
  const [tabIndex, setTabIndex] = useState(1);
  const [isOpenCustomDateFilter, setOPenCustomDateFilter] = useState(false);

  const handleTab = (index) => {
    setTabIndex(index);
  };

  const [customDateFilter, setCustomDateFilter] = useState({
    startDate: subDays(new Date(), 7),
    endDate: new Date(),
    key: "selection",
  });

  useEffect(() => {
    if (tabIndex == 0) {
      setDateRange(-1);
    }
    if (tabIndex == 1) {
      setDateRange({
        start: subDays(new Date(), 30),
        end: new Date(),
      });
    }
    if (tabIndex == 2) {
      setDateRange({
        start: customDateFilter?.startDate,
        end: customDateFilter?.endDate,
      });
    }
  }, [customDateFilter, tabIndex]);

  const insertOverlayElement = () => {
    var newNode = document.createElement("div");
    newNode.className = "btd-overlay";
    document.body.appendChild(newNode);
  };

  const removeOverlayElement = () => {
    var elem = document.querySelector(".btd-overlay");
    elem.parentNode.removeChild(elem);
  };

  const dateRangePickerOnChangeHandler = (item) => {
    setCustomDateFilter(item?.selection);
    if (item?.selection?.endDate != item?.selection?.startDate) {
      removeOverlayElement();
      setOPenCustomDateFilter(false);
    }
  };

  const customCalendarToggleHandler = () => {
    insertOverlayElement();
    setOPenCustomDateFilter(!isOpenCustomDateFilter);
  };

  const closeDatePicker = () => {
    removeOverlayElement();
    setOPenCustomDateFilter(false);
  };

  return (
    <div className="btd-chart-date-filter">
      <Tabs
        selectedIndex={tabIndex}
        onSelect={(index) => handleTab(index)}
        className="betterdocs-analytics-chart-tab-wrapper"
      >
        <TabList className="betterdocs-analytics-chart-tab-menu">
          <Tab
            className="betterdocs-analytics-chart-tab-item"
            selectedClassName="active"
          >
            {__("All Time", "betterdocs-pro")}
          </Tab>
          <Tab
            className="betterdocs-analytics-chart-tab-item"
            selectedClassName="active"
          >
            {__("Last 30 Days", "betterdocs-pro")}
          </Tab>
          <Tab
            className="betterdocs-analytics-chart-tab-item"
            selectedClassName="active"
          >
            {__("Custom Date Range", "betterdocs-pro")}
          </Tab>
        </TabList>
      </Tabs>
      {tabIndex == 2 ? (
        <>
          <button
            onClick={customCalendarToggleHandler}
            className="btd-list-view-calendar"
          >
            {String(customDateFilter?.startDate).slice(4, 15)}
            {" - "}
            {String(customDateFilter?.endDate).slice(4, 15)}
          </button>
          {isOpenCustomDateFilter && (
            <div className="btd-date-range-picker-wrap">
              <div className="btd-date-range-picker">
                <button
                  onClick={closeDatePicker}
                  className="btn-date-range-close"
                >
                  <span className="dashicons dashicons-no-alt"></span>
                </button>
                <DateRangePicker
                  onChange={(item) => dateRangePickerOnChangeHandler(item)}
                  showSelectionPreview={true}
                  moveRangeOnFirstSelection={false}
                  months={2}
                  color="#36d692"
                  rangeColors={["#36d692"]}
                  ranges={[customDateFilter]}
                  direction="horizontal"
                />
              </div>
            </div>
          )}
        </>
      ) : (
        ""
      )}
    </div>
  );
};

export default DatePicker;
