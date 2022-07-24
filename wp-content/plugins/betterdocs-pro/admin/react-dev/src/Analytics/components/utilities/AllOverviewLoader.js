import React from "react";
import ContentLoader from "react-content-loader";

const AllOverviewLoader = (props) => (
  <ContentLoader
    speed={2}
    viewBox="0 0 160 65"
    backgroundColor="#c5c8d6"
    foregroundColor="#e4e3e8"
    {...props}
  >
    <rect x="0" y="0" rx="0" ry="0" width="160" height="2" />
    <rect x="0" y="63" rx="0" ry="0" width="160" height="2" />
    <rect x="0" y="0" rx="0" ry="0" width="2" height="65" />
    <rect x="158" y="0" rx="0" ry="0" width="2" height="65" />
    <rect x="12" y="15" rx="0" ry="0" width="16" height="16" />
    <rect x="40" y="15" rx="0" ry="0" width="69" height="10" />
    <rect x="40" y="35" rx="0" ry="0" width="106" height="16" />
  </ContentLoader>
);

export default AllOverviewLoader;
