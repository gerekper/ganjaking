import React from "react";
import ContentLoader from "react-content-loader";

const GridLoader = (props) => (
  <ContentLoader
    speed={2}
    viewBox="0 0 130 50"
    backgroundColor="#c5c8d6"
    foregroundColor="#e4e3e8"
    {...props}
  >
    <rect x="0" y="0" rx="0" ry="0" width="130" height="1" />
    <rect x="0" y="49" rx="0" ry="0" width="130" height="1" />
    <rect x="0" y="0" rx="0" ry="0" width="1" height="50" />
    <rect x="129" y="0" rx="0" ry="0" width="1" height="50" />
    <rect x="8" y="7" rx="2" ry="2" width="114" height="4" />
    <rect x="8" y="16" rx="2" ry="2" width="110" height="4" />
    <rect x="8" y="25" rx="2" ry="2" width="60" height="4" />
    <rect x="8" y="36" rx="3" ry="3" width="35" height="6" />
    <rect x="88" y="36" rx="3" ry="3" width="35" height="6" />
    <rect x="48" y="36" rx="3" ry="3" width="35" height="6" />
  </ContentLoader>
);

export default GridLoader;
