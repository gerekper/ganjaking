import React, { useState, useEffect } from "react";
import { getSearchChartData } from "../../function";
import moment from "moment";
import Checkbox from "../utilities/Checkbox";
import DatePicker from "../utilities/DatePicker";
import ReactApexChart from "react-apexcharts";
import SearchPieChart from "./SearchPieChart";
import ChartLoader from "../utilities/ChartLoader";
import { ReactComponent as EmptyDataIcon } from "../../images/empty-data.svg";

const ChartWrapper = () => {
  const [search, setSearch] = useState([]);
  const [filteredOverview, setFilteredOverview] = useState({});
  const [dateRange, setDateRange] = useState({});
  const [isLoading, setIsLoading] = useState(true);

  // this is for setting the filter up of the chart ["all search", "result found", "no result found"]
  const [filterState, setFilterState] = useState([
    {
      id: "search_count",
      label: "Total Search",
      checked: true,
      color: "#FF8A1E",
    },
    {
      id: "search_found_count",
      label: "Result Found",
      checked: true,
      color: "#36D692",
    },
    {
      id: "search_not_found_count",
      label: "No Result Found",
      checked: true,
      color: "#5A6BFF",
    },
  ]);

  // this is for getting the chart data
  useEffect(() => {
    if (dateRange == -1) {
      setIsLoading(true);
      getSearchChartData()
        .then((res) => {
          setSearch(res);
        })
        .catch((err) => console.log(err))
        .finally(() => setIsLoading(false));
    } else if (dateRange && Object.entries(dateRange).length) {
      let start_date = moment(dateRange?.start).format("YYYY-MM-DD"),
        end_date = moment(dateRange?.end).format("YYYY-MM-DD");
      setIsLoading(true);
      getSearchChartData(start_date, end_date)
        .then((res) => {
          setSearch(res);
        })
        .catch((err) => console.log(err))
        .finally(() => setIsLoading(false));
    }
  }, [dateRange]);

  // this is for set the filtered search value
  useEffect(() => {
    const overviewDataForChart = {};

    if (search && search?.length) {
      overviewDataForChart.labels = search
        .map((obj) => moment(obj.search_date).format("MMM D YYYY"))
        .reverse();
      overviewDataForChart.colors = filterState
        .filter((item) => item.checked)
        .map((item) => item.color);
      overviewDataForChart.count = filterState
        .filter((item) => item.checked)
        .map((item) => {
          return {
            name: item?.label,
            data: search
              .map((data) => {
                return data.hasOwnProperty(item.id)
                  ? data[item?.id]
                  : item?.id == "search_found_count"
                  ? data?.search_count - data?.search_not_found_count
                  : 0.0;
              })
              .reverse(),
          };
        });
    }
    setFilteredOverview(overviewDataForChart);
  }, [search, filterState, dateRange]);

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
          {!isLoading ? (
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
        <SearchPieChart data={search} isLoading={isLoading} />
      </div>
    </div>
  );
};

export default ChartWrapper;
