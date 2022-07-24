import React, { useState, useEffect } from "react";
import {
  getOverviewChartData,
  getSearchChartData,
  intToString,
  comparisonFactor,
} from "../../function";
import moment from "moment";
import { subDays } from "date-fns";
import AllOverviewLoader from "../utilities/AllOverviewLoader";

const AllOverview = ({ data, dateRange }) => {
  const [totalOverview, setTotalOverview] = useState(undefined);
  const [prevOverviewData, setPrevOverviewData] = useState(undefined);
  const [prevSearchData, setPrevSearchData] = useState(undefined);
  const [prevTotalOverview, setPrevTotalOverview] = useState(undefined);
  const [isLoading, setIsLoading] = useState(true);
  const [dateDifference, setDateDifference] = useState(0);

  // this is for getting the chart data
  useEffect(() => {
    if (dateRange == -1) {
      setIsLoading(true);
      setPrevOverviewData(undefined);
      setPrevSearchData(undefined);
    } else if (dateRange && Object.entries(dateRange).length) {
      let dateDiff = moment(dateRange?.end).diff(
          moment(dateRange?.start),
          "days"
        ),
        last_end_date = dateRange?.start
          ? moment(subDays(dateRange?.start, 1)).format("YYYY-MM-DD")
          : undefined,
        last_start_date = dateRange?.start
          ? moment(subDays(dateRange?.start, dateDiff + 1)).format("YYYY-MM-DD")
          : undefined;
      setDateDifference(dateDiff);
      setIsLoading(true);
      if (last_start_date && last_end_date) {
        getOverviewChartData(last_start_date, last_end_date)
          .then((res) => {
            setPrevOverviewData(res);
          })
          .catch((err) => console.log(err));
        getSearchChartData(last_start_date, last_end_date)
          .then((res) => {
            setPrevSearchData(res);
          })
          .catch((err) => console.log(err));
      } else {
        setPrevOverviewData(undefined);
        setPrevSearchData(undefined);
      }
    }
  }, [dateRange]);

  // this is for create the chart data in real formet
  useEffect(() => {
    setPrevTotalOverview(undefined);
    if (prevOverviewData && prevSearchData) {
      let mergedOverview = prevOverviewData.map((item) => {
        return {
          date: item?.date || item?.search_date,
          views: item?.views || "0",
          reactions: item?.reactions || "0",
          search_count: item?.search_count || "0",
          search_not_found_count: item?.search_not_found_count || "0",
        };
      });

      prevSearchData.map((item) => {
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
      mergedOverview && mergedOverview.length
        ? setPrevTotalOverview(
            mergedOverview
              .sort((a, b) => new Date(a?.date) - new Date(b?.date))
              .reverse()
              .reduce((previousValue, currentValue) => {
                return {
                  views:
                    parseInt(previousValue?.views) +
                    parseInt(currentValue?.views),
                  search_count:
                    parseInt(previousValue?.search_count) +
                    parseInt(currentValue?.search_count),
                  reactions:
                    parseInt(previousValue?.reactions) +
                    parseInt(currentValue?.reactions),
                };
              })
          )
        : setPrevTotalOverview({});
    } else {
      setPrevTotalOverview({});
    }
  }, [prevOverviewData, prevSearchData]);

  // this is for get total view, search and reacts
  useEffect(() => {
    setTotalOverview(undefined);
    data && data.length
      ? setTotalOverview(
          data?.reduce((previousValue, currentValue) => {
            return {
              views:
                parseInt(previousValue?.views) + parseInt(currentValue?.views),
              reactions:
                parseInt(previousValue?.reactions) +
                parseInt(currentValue?.reactions),
              search_count:
                parseInt(previousValue?.search_count) +
                parseInt(currentValue?.search_count),
            };
          })
        )
      : setTotalOverview({});
  }, [data]);

  useEffect(() => {
    if (dateRange == -1 && totalOverview !== undefined) {
      setIsLoading(false);
    }
    if (totalOverview !== undefined && prevTotalOverview !== undefined) {
      setIsLoading(false);
    }
  }, [totalOverview, prevTotalOverview]);

  return (
    <div className="btd-chart-counter-wrapper btd-chart-counter-formet-1">
      {!isLoading ? (
        <>
          <div className="btd-chart-counter green">
            <h4 className="btd-chart-counter-title">Total Views</h4>
            <h3 className="btd-chart-counter-count">
              {totalOverview && totalOverview?.views
                ? intToString(totalOverview?.views)
                : "0"}
            </h3>
            {prevTotalOverview && Object.entries(prevTotalOverview).length
              ? comparisonFactor(
                  totalOverview?.views,
                  prevTotalOverview?.views,
                  dateDifference
                )
              : ""}
          </div>
          <div className="btd-chart-counter brown">
            <h4 className="btd-chart-counter-title">Total Searches</h4>
            <h3 className="btd-chart-counter-count">
              {totalOverview && totalOverview?.search_count
                ? intToString(totalOverview?.search_count)
                : "0"}
            </h3>
            {prevTotalOverview && Object.entries(prevTotalOverview).length
              ? comparisonFactor(
                  totalOverview?.search_count,
                  prevTotalOverview?.search_count,
                  dateDifference
                )
              : ""}
          </div>
          <div className="btd-chart-counter blue">
            <h4 className="btd-chart-counter-title">Total Reactions</h4>
            <h3 className="btd-chart-counter-count">
              {totalOverview && totalOverview?.reactions
                ? intToString(totalOverview?.reactions)
                : "0"}
            </h3>
            {prevTotalOverview && Object.entries(prevTotalOverview).length
              ? comparisonFactor(
                  totalOverview?.reactions,
                  prevTotalOverview?.reactions,
                  dateDifference
                )
              : ""}
          </div>
        </>
      ) : (
        <>
          <AllOverviewLoader />
          <AllOverviewLoader />
          <AllOverviewLoader />
        </>
      )}
    </div>
  );
};

export default AllOverview;
