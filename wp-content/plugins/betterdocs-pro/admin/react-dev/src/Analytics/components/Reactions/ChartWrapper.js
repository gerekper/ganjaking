import React, { useState, useEffect } from "react";
import { getFeedbackChartData } from "../../function";
import moment from "moment";
import Checkbox from "../utilities/Checkbox";
import DatePicker from "../utilities/DatePicker";
import ReactApexChart from "react-apexcharts";
import AllReaction from "./AllReaction";
import ChartLoader from "../utilities/ChartLoader";
import { ReactComponent as EmptyDataIcon } from "../../images/empty-data.svg";

const ChartWrapper = () => {
  const [feedback, setFeedback] = useState([]);
  const [filteredFeedback, setFilteredFeedback] = useState({});
  const [dateRange, setDateRange] = useState({});
  const [isLoading, setIsLoading] = useState(true);

  // this is for setting the filter up of the chart ["happy", "neutral", "unhappy"]
  const [filterState, setFilterState] = useState([
    {
      id: "happy",
      label: "Happy",
      checked: true,
      color: "#36D692",
    },
    {
      id: "normal",
      label: "Neutral",
      checked: true,
      color: "#FF8A1E",
    },
    {
      id: "sad",
      label: "Unhappy",
      checked: true,
      color: "#F01919",
    },
  ]);

  // this is for getting the chart data
  useEffect(() => {
    if (dateRange == -1) {
      setIsLoading(true);
      getFeedbackChartData()
        .then((res) => {
          setFeedback(res);
        })
        .catch((err) => console.log(err))
        .finally(() => setIsLoading(false));
    } else if (dateRange && Object.entries(dateRange).length) {
      let start_date = moment(dateRange?.start).format("YYYY-MM-DD"),
        end_date = moment(dateRange?.end).format("YYYY-MM-DD");
      setIsLoading(true);
      getFeedbackChartData(start_date, end_date)
        .then((res) => {
          setFeedback(res);
        })
        .catch((err) => console.log(err))
        .finally(() => setIsLoading(false));
    }
  }, [dateRange]);

  // this is for set the filtered feedback value
  useEffect(() => {
    const feedbackDataForChart = {};
    if (feedback && feedback?.length) {
      feedbackDataForChart.labels = feedback
        .map((obj) => moment(obj.created_at).format("MMM D YYYY"))
        .reverse();
      feedbackDataForChart.colors = filterState
        .filter((item) => item.checked)
        .map((item) => item.color);
      feedbackDataForChart.count = filterState
        .filter((item) => item.checked)
        .map((item) => {
          return {
            name: item.label,
            data: feedback
              .map((data) => data.hasOwnProperty(item.id) && data[item.id])
              .reverse(),
          };
        });
    }
    setFilteredFeedback(feedbackDataForChart);
  }, [feedback, filterState, dateRange]);

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
              {filteredFeedback &&
              filteredFeedback?.labels &&
              filteredFeedback?.labels?.length ? (
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
                    colors: filteredFeedback?.colors,
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
                      categories: filteredFeedback?.labels,
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
                    filteredFeedback && filteredFeedback.count
                      ? filteredFeedback.count
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
        <AllReaction data={feedback} dateRange={dateRange} />
      </div>
    </div>
  );
};

export default ChartWrapper;
