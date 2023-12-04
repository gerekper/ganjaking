import { useQuery } from "@tanstack/react-query";
import { __ } from "@wordpress/i18n";
import React, { useEffect, useState } from "react";
import ReactApexChart from "react-apexcharts";
import { formatDataForChart, getFeedbackChartData } from "../../function";
import { ReactComponent as EmptyDataIcon } from "../../images/empty-data.svg";
import ChartLoader from "../utilities/ChartLoader";
import Checkbox from "../utilities/Checkbox";
import DatePicker from "../utilities/DatePicker";
import AllReaction from "./AllReaction";

const ChartWrapper = ({ dateRange, setDateRange }) => {
    const [filteredFeedback, setFilteredFeedback] = useState({});

    // this is for setting the filter up of the chart ["happy", "neutral", "unhappy"]
    const [filterState, setFilterState] = useState([
        {
            id: "happy",
            label: __("Happy", "betterdocs-pro"),
            checked: true,
            color: "#36D692",
            enabled: true,
        },
        {
            id: "normal",
            label: __("Neutral", "betterdocs-pro"),
            checked: true,
            color: "#FF8A1E",
            enabled: true,
        },
        {
            id: "sad",
            label: __("Unhappy", "betterdocs-pro"),
            checked: true,
            color: "#F01919",
            enabled: true,
        },
    ]);

    // this is for getting the chart data
    const feedbackData = useQuery(
        ["feedbackChartData", dateRange],
        getFeedbackChartData
    );

    // this is for set the filtered feedback value
    useEffect(() => {
        setFilteredFeedback(formatDataForChart(feedbackData?.data, filterState));
    }, [feedbackData?.data, filterState]);

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
                    {!feedbackData?.isLoading ? (
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
                                                tools: {
                                                    download: `<img src="${betterdocs.dir_url}assets/admin/images/download.svg" width="14">`,
                                                    reset: `<img src="${betterdocs.dir_url}assets/admin/images/house.svg" width="14">`,
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
                                    <h3 className="title">
                                        {__("Sorry, No Data Found.", "betterdocs-pro")}
                                    </h3>
                                    <p className="text">
                                        {__(
                                            "Please try applying different filters.",
                                            "betterdocs-pro"
                                        )}
                                    </p>
                                </div>
                            )}
                        </>
                    ) : (
                        <>
                            <ChartLoader />
                        </>
                    )}
                </div>
                <AllReaction dateRange={dateRange} />
            </div>
        </div>
    );
};

export default ChartWrapper;
