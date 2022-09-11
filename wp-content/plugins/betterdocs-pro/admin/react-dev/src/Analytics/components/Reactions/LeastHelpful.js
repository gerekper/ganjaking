import React, { useState } from "react";
import { useQuery } from "@tanstack/react-query";
import { __ } from "@wordpress/i18n";
import { getLeastHelpfulData } from "../../function";
import Pagination from "rc-pagination";
import localInfo from "rc-pagination/es/locale/en_US";
import { ReactComponent as HappyIcon } from "../../images/happy.svg";
import { ReactComponent as NeutralIcon } from "../../images/neutral.svg";
import { ReactComponent as UnhappyIcon } from "../../images/unhappy.svg";
import GridLoader from "../utilities/GridLoader";
import { ReactComponent as EmptyDataIcon } from "../../images/empty-data.svg";

const LeastHelpful = () => {
  const perPage = 20;
  const [page, setPage] = useState(1);

  const { isLoading, isError, error, data } = useQuery(
    ["leastHelpfulReaction", perPage, page],
    getLeastHelpfulData
  );

  const handlePageChange = (page) => {
    setPage(page);
  };

  if (isLoading) {
    return (
      <div className="btd-reaction-list-wrapper">
        <div className="btd-info-card-list">
          {[...Array(perPage)].map(() => (
            <GridLoader key={Math.random()} />
          ))}
        </div>
      </div>
    );
  }

  if (isError) {
    return (
      <div className="btd-empty-data">
        <div className="btd-empty-data-icon">
          <EmptyDataIcon />
        </div>
        <div className="btd-empty-data-content">
          <h3 className="title">Error! {error?.message || error}</h3>
          <p className="text">
            Seems Error, We apologize and are fixing the problem. Please try
            again at a later stage.
          </p>
        </div>
      </div>
    );
  }

  return (
    <div className="btd-reaction-list-wrapper">
      {data && data.docs && data.docs.length ? (
        <>
          <div className="btd-info-card-list">
            {data?.docs?.map((data) => (
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
          {data?.pagination?.total_page > 1 ? (
            <div className="btd-pagination-wrapper">
              <Pagination
                defaultCurrent={1}
                pageSize={perPage}
                onChange={handlePageChange}
                total={data?.pagination?.total_page * perPage}
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
    </div>
  );
};

export default LeastHelpful;
