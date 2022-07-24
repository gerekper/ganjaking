import React, { useState, useEffect } from "react";
import { __ } from "@wordpress/i18n";
import { getPopularSearch, Tooltip } from "../../function";
import TableLoaderSmall from "../utilities/TableLoaderSmall";
import Pagination from "rc-pagination";
import localInfo from "rc-pagination/es/locale/en_US";
import { ReactComponent as EmptyDataIcon } from "../../images/empty-data.svg";
import { ReactComponent as Info } from "../../images/info.svg";

const PopularSearches = () => {
  const [popularSearch, setPopularSearch] = useState([]);
  const [page, setPage] = useState(1);
  const perPage = 10;
  const [isLoading, setIsLoading] = useState(true);

  useEffect(() => {
    setIsLoading(true);
    getPopularSearch(perPage, page)
      .then((data) => setPopularSearch(data))
      .catch((err) => console.log(err))
      .finally(() => {
        setIsLoading(false);
      });
  }, [page]);

  const handlePageChange = (page) => {
    setPage(page);
  };

  return (
    <>
      {!isLoading ? (
        <div>
          {popularSearch &&
          popularSearch?.search &&
          popularSearch?.search?.length ? (
            <>
              <div className="BtdDataTableWrapper BtdTableWithTooltip">
                <table className="BtdDataTable BtdTableColNoBorder">
                  <thead>
                    <tr>
                      <th>
                        <Tooltip
                          className="btd-nested-tooltip"
                          buttonClassName="btd-nested-tooltip-button"
                          tooltipClassName="btd-nested-tooltip-content"
                          buttonContent={<Info />}
                          tooltipContent="This list shows the most popular searches."
                        />
                        Popular Keywords
                      </th>
                      <th>Count</th>
                    </tr>
                  </thead>
                  <tbody>
                    {popularSearch.search.map((data) => (
                      <tr key={Math.random()}>
                        <td>
                          <p
                            dangerouslySetInnerHTML={{
                              __html: data?.keyword,
                            }}
                          ></p>
                        </td>
                        <td>
                          <p>{data?.count}</p>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
              {popularSearch?.pagination?.total_page > 1 ? (
                <div className="btd-pagination-wrapper">
                  <Pagination
                    defaultCurrent={1}
                    pageSize={perPage}
                    onChange={handlePageChange}
                    total={popularSearch?.pagination?.total_page * perPage}
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
      ) : (
        <TableLoaderSmall />
      )}
    </>
  );
};

export default PopularSearches;
