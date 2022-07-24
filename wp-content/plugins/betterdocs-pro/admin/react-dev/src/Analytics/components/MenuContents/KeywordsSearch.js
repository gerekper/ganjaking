import React from "react";
import PopularSearches from "../KeywordsSearch/PopularSearches";
import NullSearch from "../KeywordsSearch/NullSearch";
import ChartWrapper from "../KeywordsSearch/ChartWrapper";

const KeywordsSearch = () => {
  return (
    <div className="betterdocs-analytics-keyword-search">
      <ChartWrapper />
      <div className="betterdocs-analytics-keyword-search-table-wrapper">
        <PopularSearches />
        <NullSearch />
      </div>
    </div>
  );
};

export default KeywordsSearch;
