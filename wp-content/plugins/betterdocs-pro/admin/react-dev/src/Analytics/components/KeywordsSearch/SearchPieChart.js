import React, { useState, useEffect } from "react";
import { isWhatPercentOf } from "../../function";
import ReactApexChart from "react-apexcharts";
import PieChartLoader from "../utilities/PieChartLoader";
import { ReactComponent as EmptyDataIcon } from "../../images/empty-data.svg";

const SearchPieChart = ({ data, isLoading }) => {
  const [chartData, setChartData] = useState({});

  // this is for getting the chart data
  useEffect(() => {
    data && data.length
      ? setChartData(
          data?.reduce((previousValue, currentValue) => {
            return {
              search_count:
                parseInt(previousValue?.search_count) +
                parseInt(currentValue?.search_count),
              search_found:
                parseInt(previousValue?.search_found) +
                parseInt(currentValue?.search_found),
              search_not_found_count:
                parseInt(previousValue?.search_not_found_count) +
                parseInt(currentValue?.search_not_found_count),
            };
          })
        )
      : setChartData({});
  }, [data]);

  return (
    <div className="btd-chart-counter-wrapper btd-chart-counter-formet-3">
      {!isLoading ? (
        <>
          {chartData && Object.entries(chartData).length ? (
            <>
              <div className="bdt-chart-counter-pie-chart">
                <ReactApexChart
                  type="donut"
                  options={{
                    chart: {
                      width: 300,
                      type: "donut",
                    },
                    labels: ["Return Result", "No Result"],
                    plotOptions: {
                      pie: {
                        startAngle: -90,
                        endAngle: 90,
                        offsetY: 0,
                      },
                    },
                    grid: {
                      padding: {
                        bottom: -130,
                      },
                    },
                    legend: {
                      show: false,
                    },
                    stroke: {
                      width: 0,
                    },
                    dataLabels: {
                      enabled: false,
                    },
                    colors: ["#36D692", "#5A6BFF"],
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
                  }}
                  series={[
                    parseInt(chartData?.search_found),
                    parseInt(chartData?.search_not_found_count),
                  ]}
                />
              </div>
              <div className="bdt-chart-counter-contet">
                <>
                  <h3 className="title">Search Overview</h3>
                  <p className="text green">
                    Result Found{" "}
                    {isWhatPercentOf(
                      parseInt(chartData?.search_found),
                      parseInt(chartData?.search_count)
                    ).toFixed(1)}
                    %
                  </p>
                  <p className="text blue">
                    No Result Found{" "}
                    {isWhatPercentOf(
                      parseInt(chartData?.search_not_found_count),
                      parseInt(chartData?.search_count)
                    ).toFixed(1)}
                    %
                  </p>
                </>
              </div>
            </>
          ) : (
            <div className="btd-chart-empty-data">
              <span className="icon">
                <EmptyDataIcon />
              </span>
              <span className="text">No Data Found.</span>
            </div>
          )}
        </>
      ) : (
        <PieChartLoader />
      )}
    </div>
  );
};

export default SearchPieChart;
