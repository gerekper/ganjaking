import React, { useState, useEffect } from "react";
import { useQuery } from "@tanstack/react-query";
import { getSearchChartData, formatDataForChart } from "../../function";
import Checkbox from "../utilities/Checkbox";
import DatePicker from "../utilities/DatePicker";
import ReactApexChart from "react-apexcharts";
import SearchPieChart from "./SearchPieChart";
import ChartLoader from "../utilities/ChartLoader";
import { ReactComponent as EmptyDataIcon } from "../../images/empty-data.svg";

const ChartWrapper = () => {
  const [filteredOverview, setFilteredOverview] = useState({});
  const [dateRange, setDateRange] = useState({});

  // this is for setting the filter up of the chart ["all search", "result found", "no result found"]
  const [filterState, setFilterState] = useState([
    {
      id: "search_count",
      label: "Total Search",
      checked: true,
      color: "#FF8A1E",
      enabled: true,
    },
    {
      id: "search_found",
      label: "Result Found",
      checked: true,
      color: "#36D692",
      enabled: true,
    },
    {
      id: "search_not_found_count",
      label: "No Result Found",
      checked: true,
      color: "#5A6BFF",
      enabled: true,
    },
  ]);

  // this is for getting the chart data
  const searchData = useQuery(
    ["keywordSearchChartData", dateRange],
    getSearchChartData
  );

  // this is for set the filtered search value
  useEffect(() => {
    setFilteredOverview(formatDataForChart(searchData?.data, filterState));
  }, [searchData?.data, filterState]);

  // this function is for handle filter state
  const handleFilterState = (index, checked) => {
    let newArr = filterState.map((obj, position) => {
      if (position == index) {
        return { ...obj, checked: checked };
      }
      return obj;
    });
    setFilterState(newArr);
  };

  return (
    <div className="btd-chart-section">
      <div className="btd-chart-filter">
        <div className="btd-chart-data-filter">
          {filterState &&
            filterState.map((item, index) => (
              <Checkbox
                text={item?.label}
                checked={item?.checked}
                onChange={() => handleFilterState(index, !item?.checked)}
              />
            ))}
        </div>
        <DatePicker setDateRange={setDateRange} />
      </div>
      <div className="btd-chart-wrapper">
        <div className="btd-chart">
          {!searchData?.isLoading ? (
            <>
              {filteredOverview &&
              filteredOverview?.labels &&
              filteredOverview?.labels?.length ? (
                <ReactApexChart
                  type="area"
                  height={320}
                  className={"btd-chart-content"}
                  options={{
                    chart: {
                      id: "item-download-line",
                      toolbar: {
                        // show: false,
                        offsetY: -5,
                        tools: {
                          download: `<img src="${betterdocs.dir_url}admin/assets/img/download.svg" width="14">`,
                          reset: `<img src="${betterdocs.dir_url}admin/assets/img/house.svg" width="14">`,
                        },
                      },
                    },
                    tooltip: {
                      followCursor: false,
                      theme: "dark",
                      style: {
                        fontSize: "14px",
                      },
                    },
                    legend: {
                      show: false,
                    },
                    colors: filteredOverview?.colors,
                    grid: {
                      borderColor: "#E7EBF3",
                      xaxis: {
                        lines: {
                          show: true,
                        },
                      },
                      yaxis: {
                        lines: {
                          show: true,
                        },
                      },
                    },
                    dataLabels: {
                      enabled: false,
                    },
                    xaxis: {
                      type: "datetime",
                      categories: filteredOverview?.labels,
                      labels: {
                        datetimeUTC: false,
                      },
                    },
                    noData: {
                      text: "Finding Data...",
                      align: "center",
                      verticalAlign: "middle",
                      offsetX: 0,
                      offsetY: 0,
                      style: {
                        fontSize: "20px",
                      },
                    },
                    stroke: {
                      width: 3,
                      curve: "smooth",
                    },
                  }}
                  series={
                    filteredOverview && filteredOverview.count
                      ? filteredOverview.count
                      : []
                  }
                />
              ) : (
                <div className="btd-chart-empty-data">
                  <span className="icon">
                    <EmptyDataIcon />
                  </span>
                  <h3 className="title">Sorry, No Data Found.</h3>
                  <p className="text">Please try applying different filters.</p>
                </div>
              )}
            </>
          ) : (
            <>
              <ChartLoader />
            </>
          )}
        </div>
        <SearchPieChart dateRange={dateRange} />
      </div>
    </div>
  );
};

export default ChartWrapper;
