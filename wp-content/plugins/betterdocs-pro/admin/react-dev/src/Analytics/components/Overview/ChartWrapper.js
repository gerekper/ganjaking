import React, { useState, useEffect } from "react";
import { useQuery } from "@tanstack/react-query";
import {
  getOverviewChartData,
  getSearchChartData,
  formatDataForChart,
} from "../../function";
import Checkbox from "../utilities/Checkbox";
import DatePicker from "../utilities/DatePicker";
import ReactApexChart from "react-apexcharts";
import AllOverview from "./AllOverview";
import ChartLoader from "../utilities/ChartLoader";
import { ReactComponent as EmptyDataIcon } from "../../images/empty-data.svg";
import {ReactComponent as CloseIcon} from "../../images/close.svg";

const ChartWrapper = ({ postDetails, setPostDetails }) => {
  const [overview, setOverview] = useState([]);
  const [filteredOverview, setFilteredOverview] = useState({});
  const [dateRange, setDateRange] = useState({});
  const [postID, setPostID] = useState(undefined);

  // this is for setting the filter up of the chart ["views", "reactions"]
  const [filterState, setFilterState] = useState([
    {
      id: "views",
      label: "Views",
      checked: true,
      color: "#36D692",
      enabled: true,
    },
    {
      id: "reactions",
      label: "Reacts",
      checked: true,
      color: "#5A6BFF",
      enabled: true,
    },
    {
      id: "search_count",
      label: "Searches",
      checked: true,
      color: "#ff8a1e",
      enabled: true,
    },
  ]);

  // this is for getting the chart data
  const overviewData = useQuery(
    ["overviewChartData", dateRange, postID],
    getOverviewChartData
  );

  // this is for getting the chart data
  const searchData = useQuery(
    ["searchChartData", dateRange],
    getSearchChartData
  );

  // this is for getting the post id
  useEffect(() => {
    if (postDetails) {
      setPostID(postDetails?.ID);
    } else {
      setPostID(undefined);
    }
  }, [postDetails]);

  // this is for create the chart data in real formet
  useEffect(() => {
    if (overviewData?.data && searchData?.data) {
      let mergedOverview = overviewData?.data.map((item) => {
        return {
          date: item?.date || item?.search_date,
          views: item?.views || "0",
          unique_visit: item?.unique_visit || "0",
          reactions: item?.reactions || "0",
          search_count: item?.search_count || "0",
          search_not_found_count: item?.search_not_found_count || "0",
        };
      });
      searchData?.data.map((item) => {
        let index = mergedOverview.findIndex(
          (d) => d?.date == item?.search_date
        );
        if (index != -1) {
          mergedOverview[index].search_count = item?.search_count || 0;
          mergedOverview[index].search_not_found_count =
            item?.search_not_found_count || 0;
        } else {
          mergedOverview = [
            ...mergedOverview,
            {
              date: item?.date || item?.search_date,
              views: item?.views || "0",
              unique_visit: item?.unique_visit || "0",
              reactions: item?.reactions || "0",
              search_count: item?.search_count || "0",
              search_not_found_count: item?.search_not_found_count || "0",
            },
          ];
        }
      });
      setOverview(
        mergedOverview
          .sort((a, b) => new Date(a?.date) - new Date(b?.date))
          .reverse()
      );
    }
  }, [overviewData?.data, searchData?.data]);

  // this is for set the filtered overview value
  useEffect(() => {
    setFilteredOverview(formatDataForChart(overview, filterState));
  }, [overview, filterState]);

  useEffect(() => {
    setFilterState(
      filterState.map((item) => {
        if (item.id == "search_count") {
          if (postID) {
            return {
              ...item,
              enabled: false,
            };
          } else {
            return {
              ...item,
              enabled: true,
            };
          }
        }
        return item;
      })
    );
  }, [postID]);

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
      {postDetails ? (
        <div className="btd-post-details">
          <span className="btd-post-details-heading">
            <b>Currently showing Analytics for : </b>
          </span>
          <span className="btd-post-details-title">
            <span>{postDetails.title}</span>
            <button
                className="btd-post-details-reset-button"
                onClick={() => setPostDetails(undefined)}
            >
              <CloseIcon />
            </button>
          </span>
        </div>
      ) : (
        ""
      )}
      <div className="btd-chart-filter">
        <div className="btd-chart-data-filter">
          {filterState &&
            filterState.map((item, index) =>
              item?.enabled ? (
                <Checkbox
                  text={item?.label}
                  checked={item?.checked}
                  key={Math.random()}
                  onChange={() => handleFilterState(index, !item?.checked)}
                />
              ) : (
                ""
              )
            )}
        </div>
        <DatePicker setDateRange={setDateRange} />
      </div>
      <div className="btd-chart-wrapper">
        <div className="btd-chart">
          {!overviewData?.isLoading && !searchData?.isLoading ? (
            <>
              {filteredOverview &&
              filteredOverview?.labels &&
              filteredOverview?.labels?.length ? (
                <ReactApexChart
                  type="area"
                  height={postDetails ? 335 : 435}
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
        <AllOverview dateRange={dateRange} postID={postID} />
      </div>
    </div>
  );
};

export default ChartWrapper;
