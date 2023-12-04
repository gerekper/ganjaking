import { useQuery } from "@tanstack/react-query";
import { __ } from "@wordpress/i18n";
import React, { useEffect, useState } from "react";
import ReactApexChart from "react-apexcharts";
import { formatDataForChart, getOverviewChartData } from "../../function";
import { ReactComponent as CloseIcon } from "../../images/close.svg";
import { ReactComponent as EmptyDataIcon } from "../../images/empty-data.svg";
import ChartLoader from "../utilities/ChartLoader";
import Checkbox from "../utilities/Checkbox";
import DatePicker from "../utilities/DatePicker";
import AllViews from "./AllViews";

const ChartWrapper = ({
    postDetails,
    setPostDetails,
    dateRange,
    setDateRange,
}) => {
    const [filteredViews, setFilteredViews] = useState({});
    const [postID, setPostID] = useState(undefined);

    // this is for setting the filter up of the chart ["views", "reactions"]
    const [filterState, setFilterState] = useState([
        {
            id: "views",
            label: __("Views", "betterdocs-pro"),
            checked: true,
            color: "#36D692",
            enabled: true,
        },
        {
            id: "unique_visit",
            label: __("Unique Views", "betterdocs-pro"),
            checked: true,
            color: "#5A6BFF",
            enabled: true,
        },
    ]);

    // this is for getting the chart data
    const views = useQuery(
        ["overviewChartData", dateRange, postID],
        getOverviewChartData
    );

    // this is for getting the post id
    useEffect(() => {
        if (postDetails) {
            setPostID(postDetails?.ID);
        } else {
            setPostID(undefined);
        }
    }, [postDetails]);
    // this is for set the filtered views value
    useEffect(() => {
        setFilteredViews(formatDataForChart(views?.data, filterState));
    }, [views?.data, filterState]);
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
                        <b>
                            {__(
                                "Currently showing Analytics for : ",
                                "betterdocs-pro"
                            )}
                        </b>
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
                        filterState.map((item, index) => (
                            <Checkbox
                                text={item?.label}
                                checked={item?.checked}
                                key={Math.random()}
                                onChange={() =>
                                    handleFilterState(index, !item?.checked)
                                }
                            />
                        ))}
                </div>
                <DatePicker setDateRange={setDateRange} />
            </div>
            <div className="btd-chart-wrapper">
                <div className="btd-chart">
                    {!views?.isLoading ? (
                        <>
                            {filteredViews &&
                            filteredViews?.labels &&
                            filteredViews?.labels?.length ? (
                                <ReactApexChart
                                    type="area"
                                    height={335}
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
                                        colors: filteredViews?.colors,
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
                                            categories: filteredViews?.labels,
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
                                        filteredViews && filteredViews.count
                                            ? filteredViews.count
                                            : []
                                    }
                                />
                            ) : (
                                <div className="btd-chart-empty-data">
                                    <span className="icon">
                                        <EmptyDataIcon />
                                    </span>
                                    <h3 className="title">
                                        {__(
                                            "Sorry, No Data Found.",
                                            "betterdocs-pro"
                                        )}
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
                <AllViews dateRange={dateRange} postID={postID} />
            </div>
        </div>
    );
};

export default ChartWrapper;
