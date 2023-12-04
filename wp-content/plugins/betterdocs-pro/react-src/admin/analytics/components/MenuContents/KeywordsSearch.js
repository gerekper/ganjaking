import React, { useState } from "react";
import PopularSearches from "../KeywordsSearch/PopularSearches";
import NullSearch from "../KeywordsSearch/NullSearch";
import ChartWrapper from "../KeywordsSearch/ChartWrapper";

const KeywordsSearch = () => {
    const [dateRange, setDateRange] = useState({});

    return (
        <div className="betterdocs-analytics-keyword-search">
            <ChartWrapper dateRange={dateRange} setDateRange={setDateRange} />
            <div className="betterdocs-analytics-keyword-search-table-wrapper">
                <PopularSearches dateRange={dateRange} />
                <NullSearch dateRange={dateRange} />
            </div>
        </div>
    );
};

export default KeywordsSearch;
