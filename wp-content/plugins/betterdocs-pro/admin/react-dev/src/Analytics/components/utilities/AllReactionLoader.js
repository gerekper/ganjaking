import React from "react";
import ContentLoader from "react-content-loader";

const AllReactionLoader = (props) => (
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
    <rect x="50" y="40" rx="0" ry="0" width="68" height="10" />
    <rect x="50" y="15" rx="0" ry="0" width="95" height="16" />
    <circle cx="26" cy="28" r="14" />
  </ContentLoader>
);

export default AllReactionLoader;
