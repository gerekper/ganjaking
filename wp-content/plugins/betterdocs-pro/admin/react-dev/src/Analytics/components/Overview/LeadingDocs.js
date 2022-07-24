import React, { useState, useEffect } from "react";
import { __ } from "@wordpress/i18n";
import {
  getSettingOptions,
  getLeadingDocsData,
  // getDocCategory,
  // getDocKnowledgeBase,
  // getAuthorData,
} from "../../function";
import TableLoader from "../utilities/TableLoader";
import Pagination from "rc-pagination";
import localInfo from "rc-pagination/es/locale/en_US";
import { ReactComponent as EmptyDataIcon } from "../../images/empty-data.svg";

// this component return the category data
// const Category = ({ id }) => {
//   const [categoryName, setCategoryName] = useState("");

//   useEffect(() => {
//     getDocCategory(id).then((res) => {
//       setCategoryName(res.map((data) => data?.name).join(", "));
//     });
//   }, []);

//   if (!categoryName?.length) {
//     return "...";
//   }

//   return categoryName;
// };

// this component return the knowledgebase data
// const KnowledgeBase = ({ id }) => {
//   const [knowledgeBaseName, setKnowledgeBaseName] = useState("");

//   useEffect(() => {
//     getDocKnowledgeBase(id).then((res) => {
//       setKnowledgeBaseName(res.map((data) => data?.name).join(", "));
//     });
//   }, []);

//   if (!knowledgeBaseName?.length) {
//     return "...";
//   }

//   return knowledgeBaseName;
// };

// this component return the knowledgebase data
// const Author = ({ id }) => {
//   const [authorInfo, setAuthorInfo] = useState({});

//   useEffect(() => {
//     getAuthorData(id).then((res) => {
//       setAuthorInfo({
//         name: res?.name,
//         avatar:
//           res?.avatar_urls[24] || res?.avatar_urls[48] || res?.avatar_urls[96],
//       });
//     });
//   }, []);

//   if (!Object.keys(authorInfo).length) {
//     return "...";
//   }

//   return (
//     <span className="btd-author-info">
//       <span className="btd-author-avatar">
//         <img src={authorInfo?.avatar} alt="" />
//       </span>
//       <span className="btd-author-name">{authorInfo?.name}</span>
//     </span>
//   );
// };

const LeadingDocs = () => {
  const [leadingDoc, setLeadingDoc] = useState([]);
  const [page, setPage] = useState(1);
  const perPage = 10;
  const [isLoading, setIsLoading] = useState(true);
  const [multipleKbEnable, setMultipleKbEnable] = useState(false);

  useEffect(() => {
    getSettingOptions()
      .then((data) => data?.multiple_kb != "off" && setMultipleKbEnable(true))
      .catch((err) => console.log(err));
  }, []);

  useEffect(() => {
    setIsLoading(true);
    getLeadingDocsData(perPage, page)
      .then(async (data) => {
        setLeadingDoc(await data.json());
      })
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
          {leadingDoc &&
          leadingDoc?.docs &&
          leadingDoc?.docs?.length ? (
            <>
              <div className="BtdDataTableWrapper">
                <table className="BtdDataTable">
                  <thead>
                    <tr>
                      <th>Title</th>
                      <th className="text-center">Category</th>
                      {multipleKbEnable && (
                        <th className="text-center">Knowledge Base</th>
                      )}
                      <th className="text-center">Views</th>
                      <th className="text-center">Reactions</th>
                      <th className="text-center">Author</th>
                    </tr>
                  </thead>
                  <tbody>
                    {leadingDoc.docs.map((data) => (
                      <tr key={Math.random()}>
                        <td>
                          <a
                            href={data?.link}
                            target="_blank"
                            dangerouslySetInnerHTML={{
                              __html: data?.title?.rendered || data?.title,
                            }}
                          ></a>
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
                          >
                            {/* <Category id={data?.id} /> */}
                          </p>
                        </td>
                        {multipleKbEnable && (
                          <td className="text-center">
                            <p
                              dangerouslySetInnerHTML={{
                                __html: data?.knowledge_base_terms
                                  ? data?.knowledge_base_terms
                                      ?.map((kb) => kb?.name)
                                      .join(", ")
                                  : "...",
                              }}
                            >
                              {/* <KnowledgeBase id={data?.id} /> */}
                            </p>
                          </td>
                        )}
                        <td className="text-center">
                          <p>{data?.total_views}</p>
                        </td>
                        <td className="text-center">
                          <p>{data?.total_reactions}</p>
                        </td>
                        <td className="text-center">
                          {/* <Author id={data?.author} /> */}
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
                                {data?.author?.name ||
                                  data?.author?.display_name}
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
              {leadingDoc?.pagination?.total_page > 1 ? (
                <div className="btd-pagination-wrapper">
                  <Pagination
                    defaultCurrent={1}
                    pageSize={perPage}
                    onChange={handlePageChange}
                    total={leadingDoc?.pagination?.total_page * perPage}
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

export default LeadingDocs;
