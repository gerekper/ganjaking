import React from "react";
import ContentLoader from "react-content-loader";

const PieChartLoader = (props) => (
  <ContentLoader
    speed={2}
    viewBox="0 0 320 350"
    backgroundColor="#c5c8d6"
    foregroundColor="#e4e3e8"
    {...props}
  >
    <rect x="0" y="0" rx="2" ry="2" width="320" height="350" />
  </ContentLoader>
);

export default PieChartLoader;
