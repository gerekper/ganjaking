import React, { useState, useEffect } from "react";
import { ReactComponent as HappyIcon } from "../../images/happy.svg";
import { ReactComponent as NeutralIcon } from "../../images/neutral.svg";
import { ReactComponent as UnhappyIcon } from "../../images/unhappy.svg";
import {
  getFeedbackChartData,
  intToString,
  comparisonFactor,
} from "../../function";
import moment from "moment";
import { subDays } from "date-fns";
import AllReactionLoader from "../utilities/AllReactionLoader";

const AllReaction = ({ data, dateRange }) => {
  const [totalFeedback, setTotalFeedback] = useState(undefined);
  const [prevTotalFeedback, setPrevTotalFeedback] = useState(undefined);
  const [isLoading, setIsLoading] = useState(true);
  const [dateDifference, setDateDifference] = useState(0);

  // this is for getting the chart data
  useEffect(() => {
    setPrevTotalFeedback(undefined);
    if (dateRange == -1) {
      setIsLoading(true);
      setPrevTotalFeedback({});
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
        getFeedbackChartData(last_start_date, last_end_date)
          .then((res) => {
            res && res.length
              ? setPrevTotalFeedback(
                  res.reduce((previousValue, currentValue) => {
                    return {
                      happy:
                        parseInt(previousValue?.happy) +
                        parseInt(currentValue?.happy),
                      normal:
                        parseInt(previousValue?.normal) +
                        parseInt(currentValue?.normal),
                      sad:
                        parseInt(previousValue?.sad) +
                        parseInt(currentValue?.sad),
                    };
                  })
                )
              : setPrevTotalFeedback({});
          })
          .catch((err) => console.log(err));
      } else {
        setPrevTotalFeedback({});
      }
    }
  }, [dateRange]);

  // this is for get total happy, neutral and unhappy Feedback
  useEffect(() => {
    setTotalFeedback(undefined);
    data && data.length
      ? setTotalFeedback(
          data?.reduce((previousValue, currentValue) => {
            return {
              happy:
                parseInt(previousValue?.happy) + parseInt(currentValue?.happy),
              normal:
                parseInt(previousValue?.normal) +
                parseInt(currentValue?.normal),
              sad: parseInt(previousValue?.sad) + parseInt(currentValue?.sad),
            };
          })
        )
      : setTotalFeedback({});
  }, [data]);

  useEffect(() => {
    if (dateRange == -1 && totalFeedback !== undefined) {
      setIsLoading(false);
    }
    if (totalFeedback !== undefined && prevTotalFeedback !== undefined) {
      setIsLoading(false);
    }
  }, [totalFeedback, prevTotalFeedback]);

  return (
    <div className="btd-chart-counter-wrapper btd-chart-counter-formet-2">
      {!isLoading ? (
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
                {totalFeedback && totalFeedback?.happy
                  ? intToString(totalFeedback?.happy)
                  : "0"}
              </h3>
              {prevTotalFeedback && Object.entries(prevTotalFeedback).length
                ? comparisonFactor(
                    totalFeedback?.happy,
                    prevTotalFeedback?.happy,
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
                {totalFeedback && totalFeedback?.normal
                  ? intToString(totalFeedback?.normal)
                  : "0"}
              </h3>
              {prevTotalFeedback && Object.entries(prevTotalFeedback).length
                ? comparisonFactor(
                    totalFeedback?.normal,
                    prevTotalFeedback?.normal,
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
                {totalFeedback && totalFeedback?.sad
                  ? intToString(totalFeedback?.sad)
                  : "0"}
              </h3>
              {prevTotalFeedback && Object.entries(prevTotalFeedback).length
                ? comparisonFactor(
                    totalFeedback?.sad,
                    prevTotalFeedback?.sad,
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
