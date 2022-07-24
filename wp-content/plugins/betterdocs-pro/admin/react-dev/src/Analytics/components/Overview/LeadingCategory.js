import React, { useState, useEffect } from "react";
import { __ } from "@wordpress/i18n";
import { getLeadingCategoryData } from "../../function";
import TableLoader from "../utilities/TableLoader";
import Pagination from "rc-pagination";
import localInfo from "rc-pagination/es/locale/en_US";
import { ReactComponent as EmptyDataIcon } from "../../images/empty-data.svg";

const LeadingCategory = () => {
  const [leadingCategory, setLeadingCategory] = useState([]);
  const [page, setPage] = useState(1);
  const perPage = 10;
  const [isLoading, setIsLoading] = useState(true);

  useEffect(() => {
    setIsLoading(true);
    getLeadingCategoryData(perPage, page)
      .then((data) => setLeadingCategory(data))
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
          {leadingCategory &&
          leadingCategory?.doc_category &&
          leadingCategory?.doc_category?.length ? (
            <>
              <div className="BtdDataTableWrapper">
                <table className="BtdDataTable">
                  <thead>
                    <tr>
                      <th>Category</th>
                      <th className="text-center">Slug</th>
                      <th className="text-center">Views</th>
                      <th className="text-center">Reactions</th>
                    </tr>
                  </thead>
                  <tbody>
                    {leadingCategory.doc_category.map((data) => (
                      <tr key={Math.random()}>
                        <td>
                          <p
                            dangerouslySetInnerHTML={{
                              __html: data?.name,
                            }}
                          ></p>
                        </td>
                        <td className="text-center">
                          <p>{data?.slug}</p>
                        </td>
                        <td className="text-center">
                          <p>{data?.total_view}</p>
                        </td>
                        <td className="text-center">
                          <p>{data?.total_reactions}</p>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
              {leadingCategory?.pagination?.total_page > 1 ? (
                <div className="btd-pagination-wrapper">
                  <Pagination
                    defaultCurrent={1}
                    pageSize={perPage}
                    onChange={handlePageChange}
                    total={leadingCategory?.pagination?.total_page * perPage}
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
        <TableLoader />
      )}
    </>
  );
};

export default LeadingCategory;
