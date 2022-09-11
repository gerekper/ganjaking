import { useQuery } from "@tanstack/react-query";
import { getTotalDataCount, isWhatPercentOf } from "../../function";
import ReactApexChart from "react-apexcharts";
import PieChartLoader from "../utilities/PieChartLoader";
import { ReactComponent as EmptyDataIcon } from "../../images/empty-data.svg";

const SearchPieChart = ({ dateRange }) => {
  // this is for getting the chart data
  const currentTotalCount = useQuery(
    ["totalDataCount", dateRange, undefined],
    getTotalDataCount
  );

  return (
    <div className="btd-chart-counter-wrapper btd-chart-counter-formet-3">
      {!currentTotalCount?.isLoading ? (
        <>
          {currentTotalCount?.data &&
          (currentTotalCount?.data?.totalFound ||
            currentTotalCount?.data?.totalNotFound) ? (
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
                    parseInt(
                      currentTotalCount?.data?.totalFound == null
                        ? "0"
                        : currentTotalCount?.data?.totalFound
                    ),
                    parseInt(
                      currentTotalCount?.data?.totalNotFound == null
                        ? "0"
                        : currentTotalCount?.data?.totalNotFound
                    ),
                  ]}
                />
              </div>
              <div className="bdt-chart-counter-contet">
                <>
                  <h3 className="title">Search Overview</h3>
                  <p className="text green">
                    Result Found{" "}
                    {isWhatPercentOf(
                      parseInt(
                        currentTotalCount?.data?.totalFound == null
                          ? "0"
                          : currentTotalCount?.data?.totalFound
                      ),
                      parseInt(
                        currentTotalCount?.data?.totalSearch == null
                          ? "0"
                          : currentTotalCount?.data?.totalSearch
                      )
                    ).toFixed(1)}
                    %
                  </p>
                  <p className="text blue">
                    No Result Found{" "}
                    {isWhatPercentOf(
                      parseInt(
                        currentTotalCount?.data?.totalNotFound == null
                          ? "0"
                          : currentTotalCount?.data?.totalNotFound
                      ),
                      parseInt(
                        currentTotalCount?.data?.totalSearch == null
                          ? "0"
                          : currentTotalCount?.data?.totalSearch
                      )
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
