import React, { useState, useEffect } from "react";
import { __ } from "@wordpress/i18n";
import { useQuery } from "@tanstack/react-query";
import {
    getTotalDataCount,
    intToString,
    comparisonFactor,
} from "../../function";
import moment from "moment";
import { subDays } from "date-fns";
import AllOverviewLoader from "../utilities/AllOverviewLoader";

const AllViews = ({ dateRange, postID }) => {
    const [previousDateRange, setPreviousDateRange] = useState(undefined);
    const [dateDifference, setDateDifference] = useState(0);

    useEffect(() => {
        if (dateRange == -1) {
            setPreviousDateRange(-1);
        } else if (dateRange && Object.entries(dateRange).length) {
            let dateDiff = moment(dateRange?.end).diff(
                moment(dateRange?.start),
                "days"
            );
            setDateDifference(dateDiff);
            setPreviousDateRange({
                start: dateRange?.start
                    ? moment(subDays(dateRange?.start, dateDiff + 1)).format("YYYY-MM-DD")
                    : undefined,
                end: dateRange?.start
                    ? moment(subDays(dateRange?.start, 1)).format("YYYY-MM-DD")
                    : undefined,
            });
        }
    }, [dateRange]);

    // this is for getting the chart data
    const currentTotalCount = useQuery(
        ["totalDataCount", dateRange, postID],
        getTotalDataCount
    );

    // this is for getting the chart data
    const prevTotalCount = useQuery(
        ["prevTotalDataCount", previousDateRange, postID],
        getTotalDataCount
    );

    return (
        <div className="btd-chart-counter-wrapper btd-chart-counter-format-1">
            {!currentTotalCount.isLoading && !prevTotalCount?.isLoading ? (
                <>
                    <div className="btd-chart-counter green">
                        <h4 className="btd-chart-counter-title">
                            {__("Total Views", "betterdocs-pro")}
                        </h4>
                        <h3 className="btd-chart-counter-count">
                            {currentTotalCount &&
                                currentTotalCount?.data &&
                                currentTotalCount?.data?.totalViews
                                ? intToString(currentTotalCount?.data?.totalViews)
                                : "0"}
                        </h3>
                        {prevTotalCount &&
                            prevTotalCount?.data &&
                            Object.entries(prevTotalCount?.data).length
                            ? comparisonFactor(
                                currentTotalCount?.data?.totalViews,
                                prevTotalCount?.data?.totalViews,
                                dateDifference
                            )
                            : ""}
                    </div>
                    <div className="btd-chart-counter blue">
                        <h4 className="btd-chart-counter-title">
                            {__("Unique Views", "betterdocs-pro")}
                        </h4>
                        <h3 className="btd-chart-counter-count">
                            {currentTotalCount &&
                                currentTotalCount?.data &&
                                currentTotalCount?.data?.totalUniqueViews
                                ? intToString(currentTotalCount?.data?.totalUniqueViews)
                                : "0"}
                        </h3>
                        {prevTotalCount &&
                            prevTotalCount?.data &&
                            Object.entries(prevTotalCount?.data).length
                            ? comparisonFactor(
                                currentTotalCount?.data?.totalUniqueViews,
                                prevTotalCount?.data?.totalUniqueViews,
                                dateDifference
                            )
                            : ""}
                    </div>
                </>
            ) : (
                <>
                    <AllOverviewLoader />
                    <AllOverviewLoader />
                </>
            )}
        </div>
    );
};

export default AllViews;
