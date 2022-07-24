import React, { useState, useEffect } from "react";
import { getOverviewChartData, getSearchChartData } from "../../function";
import moment from "moment";
import Checkbox from "../utilities/Checkbox";
import DatePicker from "../utilities/DatePicker";
import ReactApexChart from "react-apexcharts";
import AllOverview from "./AllOverview";
import ChartLoader from "../utilities/ChartLoader";
import { ReactComponent as EmptyDataIcon } from "../../images/empty-data.svg";

const ChartWrapper = () => {
  const [overviewData, setOverviewData] = useState(undefined);
  const [searchData, setSearchData] = useState(undefined);
  const [overview, setOverview] = useState([]);
  const [filteredOverview, setFilteredOverview] = useState({});
  const [dateRange, setDateRange] = useState({});
  const [isLoading, setIsLoading] = useState(true);

  // this is for setting the filter up of the chart ["views", "reactions"]
  const [filterState, setFilterState] = useState([
    {
      id: "views",
      label: "Views",
      checked: true,
      color: "#36D692",
    },
    {
      id: "reactions",
      label: "Reacts",
      checked: true,
      color: "#5A6BFF",
    },
    {
      id: "search_count",
      label: "Searches",
      checked: true,
      color: "#ff8a1e",
    },
  ]);

  // this is for getting the chart data
  useEffect(() => {
    if (dateRange == -1) {
      setIsLoading(true);
      getOverviewChartData()
        .then((res) => {
          setOverviewData(res);
        })
        .catch((err) => console.log(err));
      getSearchChartData()
        .then((res) => {
          setSearchData(res);
        })
        .catch((err) => console.log(err));
    } else if (dateRange && Object.entries(dateRange).length) {
      let start_date = moment(dateRange?.start).format("YYYY-MM-DD"),
        end_date = moment(dateRange?.end).format("YYYY-MM-DD");
      setIsLoading(true);
      getOverviewChartData(start_date, end_date)
        .then((res) => {
          setOverviewData(res);
        })
        .catch((err) => console.log(err));
      getSearchChartData(start_date, end_date)
        .then((res) => {
          setSearchData(res);
        })
        .catch((err) => console.log(err));
    }
  }, [dateRange]);

  // this is for create the chart data in real formet
  useEffect(() => {
    if (overviewData && searchData) {
      let mergedOverview = overviewData.map((item) => {
        return {
          date: item?.date || item?.search_date,
          views: item?.views || "0",
          reactions: item?.reactions || "0",
          search_count: item?.search_count || "0",
          search_not_found_count: item?.search_not_found_count || "0",
        };
      });

      searchData.map((item) => {
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
      setIsLoading(false);
    }
  }, [overviewData, searchData]);

  // this is for set the filtered overview value
  useEffect(() => {
    const overviewDataForChart = {};
    if (overview && overview?.length) {
      overviewDataForChart.labels = overview
        .map((obj) => moment(obj.date).format("MMM D YYYY"))
        .reverse();
      overviewDataForChart.colors = filterState
        .filter((item) => item.checked)
        .map((item) => item.color);
      overviewDataForChart.count = filterState
        .filter((item) => item.checked)
        .map((item) => {
          return {
            name: item.label,
            data: overview
              .map((data) => data.hasOwnProperty(item.id) && data[item.id])
              .reverse(),
          };
        });
    }
    setFilteredOverview(overviewDataForChart);
  }, [overview, filterState, dateRange]);

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
                key={Math.random()}
                onChange={() => handleFilterState(index, !item?.checked)}
              />
            ))}
        </div>
        <DatePicker setDateRange={setDateRange} />
      </div>
      <div className="btd-chart-wrapper">
        <div className="btd-chart">
          {!isLoading ? (
            <>
              {filteredOverview &&
              filteredOverview?.labels &&
              filteredOverview?.labels?.length ? (
                <ReactApexChart
                  type="area"
                  height={435}
                  className={"btd-chart-content"}
                  options={{
                    chart: {
                      id: "item-download-line",
                      toolbar: {
                        // show: false,
                        offsetY: -5,
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
        <AllOverview data={overview} dateRange={dateRange} />
      </div>
    </div>
  );
};

export default ChartWrapper;
