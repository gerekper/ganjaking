import React, { useState } from "react";
import { __ } from "@wordpress/i18n";
import { useQuery } from "@tanstack/react-query";
import { getLeadingCategoryData } from "../../function";
import TableLoader from "../utilities/TableLoader";
import Pagination from "rc-pagination";
import localInfo from "rc-pagination/es/locale/en_US";
import { ReactComponent as EmptyDataIcon } from "../../images/empty-data.svg";

const data = ({ dateRange }) => {
    const [page, setPage] = useState(1);
    const perPage = 10;
    const { isLoading, isError, error, data } = useQuery(
        ["leadingCategory", perPage, page, dateRange],
        getLeadingCategoryData
    );

    const handlePageChange = (page) => {
        setPage(page);
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
                    <h3 className="title">
                        {__("Error! ", "betterdocs-pro") + error?.message || error}
                    </h3>
                    <p className="text">
                        {__(
                            "Seems Error, We apologize and are fixing the problem. Please try again at a later stage.",
                            "betterdocs-pro"
                        )}
                    </p>
                </div>
            </div>
        );
    }

    return (
        <>
            {data && data?.doc_category && data?.doc_category?.length ? (
                <>
                    <div className="BtdDataTableWrapper">
                        <table className="BtdDataTable">
                            <thead>
                                <tr>
                                    <th>{__("Category", "betterdocs-pro")}</th>
                                    <th className="text-center">
                                        {__("Slug", "betterdocs-pro")}
                                    </th>
                                    <th className="text-center">
                                        {__("Views", "betterdocs-pro")}
                                    </th>
                                    <th className="text-center">
                                        {__("Reactions", "betterdocs-pro")}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                {data.doc_category.map((data) => (
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
                    {data?.pagination?.total_page > 1 ? (
                        <div className="btd-pagination-wrapper">
                            <Pagination
                                defaultCurrent={1}
                                pageSize={perPage}
                                onChange={handlePageChange}
                                total={data?.pagination?.total_page * perPage}
                                prevIcon={__("Prev", "betterdocs-pro")}
                                nextIcon={__("Next", "betterdocs-pro")}
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
                        <h3 className="title">
                            {__("Sorry, No Data Found.", "betterdocs-pro")}
                        </h3>
                        <p className="text">
                            {__(
                                "Seems like you haven't got any reactions yet. You will see the list once there's some data available.",
                                "betterdocs-pro"
                            )}
                        </p>
                    </div>
                </div>
            )}
        </>
    );
};

export default data;
