import React, { useState, useEffect } from "react";
import { __ } from "@wordpress/i18n";
import { getLeastHelpfulData } from "../../function";
import Pagination from "rc-pagination";
import localInfo from "rc-pagination/es/locale/en_US";
import { ReactComponent as HappyIcon } from "../../images/happy.svg";
import { ReactComponent as NeutralIcon } from "../../images/neutral.svg";
import { ReactComponent as UnhappyIcon } from "../../images/unhappy.svg";
import GridLoader from "../utilities/GridLoader";
// import Checkbox from "../utilities/Checkbox";
import { ReactComponent as EmptyDataIcon } from "../../images/empty-data.svg";

const LeastHelpful = () => {
  const [feedback, setFeedback] = useState({});
  const perPage = 20;
  const [page, setPage] = useState(1);
  const [isLoading, setIsLoading] = useState(true);
  // const [filterState, setFilterState] = useState([
  //   {
  //     id: "all",
  //     label: "All React",
  //     checked: true,
  //   },
  //   {
  //     id: "happy",
  //     label: "Happy",
  //     checked: false,
  //   },
  //   {
  //     id: "normal",
  //     label: "Neutral",
  //     checked: false,
  //   },
  //   {
  //     id: "sad",
  //     label: "Unhappy",
  //     checked: false,
  //   },
  // ]);
  // const [reactionType, setReactionType] = useState(["all"]); // ['all', 'happy', 'normal', 'sad']

  useEffect(() => {
    setIsLoading(true);
    getLeastHelpfulData(perPage, page)
      .then((res) => {
        if (res.length != 0) {
          setFeedback(res);
        }
      })
      .catch((err) => console.log(err))
      .finally(() => setIsLoading(false));
  }, [page]);

  // useEffect(() => {
  //   let filterArr;
  //   if (filterState && filterState.length) {
  //     if (filterState[0].checked) {
  //       filterArr = ["all"];
  //     } else {
  //       filterArr = filterState
  //         .filter((item, index) => index != 0 && item?.checked)
  //         .map((item) => item.id);
  //     }
  //   }
  //   setReactionType(filterArr);
  // }, [filterState]);

  // const handleAllChecked = (arr) => {
  //   let sortArr = arr.filter((obj, index) => index != 0 && obj.checked);
  //   if (sortArr.length != 0) {
  //     arr[0].checked = false;
  //   }
  //   if (sortArr.length == 0) {
  //     arr[0].checked = true;
  //   }
  //   return arr;
  // };

  // const handleFilterState = (index, checked) => {
  //   let newArr;
  //   if (index == 0 && checked) {
  //     newArr = filterState.map((obj, position) => {
  //       if (position == 0) {
  //         return { ...obj, checked: true };
  //       } else {
  //         return { ...obj, checked: false };
  //       }
  //     });
  //   } else {
  //     newArr = filterState.map((obj, position) => {
  //       if (position == index) {
  //         return { ...obj, checked: checked };
  //       }
  //       return obj;
  //     });
  //   }
  //   setFilterState(handleAllChecked(newArr));
  // };

  const handlePageChange = (page) => {
    setPage(page);
  };
  return (
    <div className="btd-reaction-list-wrapper">
      {!isLoading ? (
        <>
          {feedback && feedback.docs && feedback.docs.length ? (
            <>
              {/* <div className="btd-reaction-fliter">
            <div className="btd-inline-input-wrapper">
              {filterState &&
                filterState.map((item, index) => (
                  <Checkbox
                    text={item?.label}
                    checked={item?.checked}
                    onChange={() => handleFilterState(index, !item?.checked)}
                  />
                ))}
            </div>
          </div> */}
              <div className="btd-info-card-list">
                {feedback?.docs?.map((data) => (
                  <div className="btd-info-card" key={Math.random()}>
                    <a
                      className="btd-info-card-title"
                      href={data?.link}
                      target="_blank"
                      dangerouslySetInnerHTML={{
                        __html: data?.post_title,
                      }}
                    ></a>
                    <div className="btd-info-card-count-set">
                      <span className="btd-info-card-count">
                        <span className="icon">
                          <HappyIcon />
                        </span>
                        <span className="text">
                          Happy: <span className="count">{data?.happy}</span>
                        </span>
                      </span>
                      <span className="btd-info-card-count">
                        <span className="icon">
                          <NeutralIcon />
                        </span>
                        <span className="text">
                          Neutral: <span className="count">{data?.normal}</span>
                        </span>
                      </span>
                      <span className="btd-info-card-count">
                        <span className="icon">
                          <UnhappyIcon />
                        </span>
                        <span className="text">
                          Unhappy: <span className="count">{data?.sad}</span>
                        </span>
                      </span>
                    </div>
                  </div>
                ))}
              </div>
              {feedback?.pagination?.total_page > 1 ? (
                <div className="btd-pagination-wrapper">
                  <Pagination
                    defaultCurrent={1}
                    pageSize={perPage}
                    onChange={handlePageChange}
                    total={feedback?.pagination?.total_page * perPage}
                    prevIcon={"Prev"}
                    nextIcon={"Next"}
                    locale={localInfo}
                    current={page}
                  />
                </div>
              ) : (
                ""
              )}
            </>
          ) : (
            <div className="btd-empty-data">
              <div className="btd-empty-data-icon">
                <EmptyDataIcon />
              </div>
              <div className="btd-empty-data-content">
                <h3 className="title">Sorry, No Data Found.</h3>
                <p className="text">
                  Seems like you haven't got any reactions yet. You will see the
                  list once there's some data available.
                </p>
              </div>
            </div>
          )}
        </>
      ) : (
        <div className="btd-info-card-list">
          {[...Array(perPage)].map(() => (
            <GridLoader key={Math.random()} />
          ))}
        </div>
      )}
    </div>
  );
};

export default LeastHelpful;
