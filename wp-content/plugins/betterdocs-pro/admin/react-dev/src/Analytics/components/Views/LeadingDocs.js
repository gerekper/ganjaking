import React, { useState, useEffect, forwardRef } from "react";
import { useQuery } from "@tanstack/react-query";
import { __ } from "@wordpress/i18n";
import { getSettingOptions, getLeadingDocsData, Tooltip } from "../../function";
import TableLoader from "../utilities/TableLoader";
import Pagination from "rc-pagination";
import localInfo from "rc-pagination/es/locale/en_US";
import { ReactComponent as EmptyDataIcon } from "../../images/empty-data.svg";
import { ReactComponent as DocumentIcon } from "../../images/document.svg";
import { ReactComponent as AnalyticsIcon } from "../../images/analytics.svg";

const LeadingDocs = forwardRef(({ setPostDetails }, mainComponentRef) => {
  const [page, setPage] = useState(1);
  const perPage = 10;
  const { isLoading, isError, error, data } = useQuery(
    ["leadingDoc", perPage, page],
    getLeadingDocsData
  );
  const setting = useQuery(["pluginSetting"], getSettingOptions);

  const handlePageChange = (page) => {
    setPage(page);
  };

  const handleDocAnalytics = (data) => {
    window.scrollTo({
      top: mainComponentRef.current.offsetTop - 20,
      left: 0,
      behavior: "smooth",
    });
    setPostDetails(data);
  };

  if (isLoading) {
    return <TableLoader />;
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
    <>
      <div>
        {data && data?.docs && data?.docs?.length ? (
          <>
            <div className="BtdDataTableWrapper">
              <table className="BtdDataTable">
                <thead>
                  <tr>
                    <th>Title</th>
                    <th className="text-center">Category</th>
                    {setting.data?.multiple_kb == 1 && (
                      <th className="text-center">Knowledge Base</th>
                    )}
                    <th className="text-center">Views</th>
                    <th className="text-center">Unique Views</th>
                    <th className="text-center">Author</th>
                  </tr>
                </thead>
                <tbody>
                  {data.docs.map((data) => (
                    <tr key={Math.random()}>
                      <td>
                        <span className="BtdTableItemTitleWrap">
                          <p
                            dangerouslySetInnerHTML={{
                              __html: data?.title?.rendered || data?.title,
                            }}
                          ></p>
                          <span className="BtdButtonGroup">
                            <Tooltip
                              className="btd-nested-tooltip"
                              buttonClassName="btd-nested-tooltip-button"
                              tooltipClassName="btd-nested-tooltip-content"
                              buttonContent={
                                <a href={data?.link} target="_blank">
                                  <DocumentIcon />
                                </a>
                              }
                              tooltipContent="Browse Doc"
                            />
                            <Tooltip
                              className="btd-nested-tooltip"
                              buttonClassName="btd-nested-tooltip-button"
                              tooltipClassName="btd-nested-tooltip-content"
                              buttonContent={
                                <button
                                  onClick={() => handleDocAnalytics(data)}
                                >
                                  <AnalyticsIcon />
                                </button>
                              }
                              tooltipContent="View Analytics"
                            />
                          </span>
                        </span>
                      </td>
                      <td className="text-center">
                        <p
                          dangerouslySetInnerHTML={{
                            __html: data?.doc_category_terms
                              ? data?.doc_category_terms
                                  ?.map((category) => category?.name)
                                  .join(", ")
                              : "...",
                          }}
                        ></p>
                      </td>
                      {setting.data?.multiple_kb == 1 && (
                        <td className="text-center">
                          <p
                            dangerouslySetInnerHTML={{
                              __html: data?.knowledge_base_terms
                                ? data?.knowledge_base_terms
                                    ?.map((kb) => kb?.name)
                                    .join(", ")
                                : "...",
                            }}
                          ></p>
                        </td>
                      )}
                      <td className="text-center">
                        <p>{data?.total_views}</p>
                      </td>
                      <td className="text-center">
                        <p>{data?.total_unique_visit}</p>
                      </td>
                      <td className="text-center">
                        {data?.author ? (
                          <span className="btd-author-info">
                            <span className="btd-author-avatar">
                              <img
                                src={data?.author?.avatar}
                                alt={
                                  data?.author?.name ||
                                  data?.author?.display_name
                                }
                              />
                            </span>
                            <span className="btd-author-name">
                              {data?.author?.name || data?.author?.display_name}
                            </span>
                          </span>
                        ) : (
                          "..."
                        )}
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
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
    </>
  );
});

export default LeadingDocs;
