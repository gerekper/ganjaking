import React, { useState, useEffect } from "react";
import { useQuery } from "@tanstack/react-query";
import { ReactComponent as HappyIcon } from "../../images/happy.svg";
import { ReactComponent as NeutralIcon } from "../../images/neutral.svg";
import { ReactComponent as UnhappyIcon } from "../../images/unhappy.svg";
import {
  getTotalDataCount,
  intToString,
  comparisonFactor,
} from "../../function";
import moment from "moment";
import { subDays } from "date-fns";
import AllReactionLoader from "../utilities/AllReactionLoader";

const AllReaction = ({ dateRange }) => {
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
    ["totalDataCount", dateRange, undefined],
    getTotalDataCount
  );

  // this is for getting the chart data
  const prevTotalCount = useQuery(
    ["prevTotalDataCount", previousDateRange, undefined],
    getTotalDataCount
  );

  return (
    <div className="btd-chart-counter-wrapper btd-chart-counter-formet-2">
      {!currentTotalCount?.isLoading && !prevTotalCount?.isLoading ? (
        <>
          <div className="btd-chart-counter happy">
            <div className="btd-chart-counter-icon-wrapper">
              <div className="btd-chart-counter-icon">
                <HappyIcon />
              </div>
            </div>
            <div className="btd-chart-counter-content">
              <h4 className="btd-chart-counter-title">Happy</h4>
              <h3 className="btd-chart-counter-count">
                {currentTotalCount &&
                currentTotalCount?.data &&
                currentTotalCount?.data?.totalHappy
                  ? intToString(currentTotalCount?.data?.totalHappy)
                  : "0"}
              </h3>
              {prevTotalCount &&
              prevTotalCount?.data &&
              Object.entries(prevTotalCount?.data).length
                ? comparisonFactor(
                    currentTotalCount?.data?.totalHappy,
                    prevTotalCount?.data?.totalHappy,
                    dateDifference
                  )
                : ""}
            </div>
          </div>
          <div className="btd-chart-counter neutral">
            <div className="btd-chart-counter-icon-wrapper">
              <div className="btd-chart-counter-icon">
                <NeutralIcon />
              </div>
            </div>
            <div className="btd-chart-counter-content">
              <h4 className="btd-chart-counter-title">Neutral</h4>
              <h3 className="btd-chart-counter-count">
                {currentTotalCount &&
                currentTotalCount?.data &&
                currentTotalCount?.data?.totalNormal
                  ? intToString(currentTotalCount?.data?.totalNormal)
                  : "0"}
              </h3>
              {prevTotalCount &&
              prevTotalCount?.data &&
              Object.entries(prevTotalCount?.data).length
                ? comparisonFactor(
                    currentTotalCount?.data?.totalNormal,
                    prevTotalCount?.data?.totalNormal,
                    dateDifference
                  )
                : ""}
            </div>
          </div>
          <div className="btd-chart-counter unhappy">
            <div className="btd-chart-counter-icon-wrapper">
              <div className="btd-chart-counter-icon">
                <UnhappyIcon />
              </div>
            </div>
            <div className="btd-chart-counter-content">
              <h4 className="btd-chart-counter-title">Unhappy</h4>
              <h3 className="btd-chart-counter-count">
                {currentTotalCount &&
                currentTotalCount?.data &&
                currentTotalCount?.data?.totalSad
                  ? intToString(currentTotalCount?.data?.totalSad)
                  : "0"}
              </h3>
              {prevTotalCount &&
              prevTotalCount?.data &&
              Object.entries(prevTotalCount?.data).length
                ? comparisonFactor(
                    currentTotalCount?.data?.totalSad,
                    prevTotalCount?.data?.totalSad,
                    dateDifference
                  )
                : ""}
            </div>
          </div>
        </>
      ) : (
        <>
          <AllReactionLoader />
          <AllReactionLoader />
          <AllReactionLoader />
        </>
      )}
    </div>
  );
};

export default AllReaction;
