import React from "react";
import ContentLoader from "react-content-loader";

const ChartLoader = (props) => (
  <ContentLoader
    speed={2}
    style={{ margin: "auto", width: "70%", maxWidth: 250, padding: "5%" }}
    viewBox="0 0 500 380"
    backgroundColor="#c5c8d6"
    foregroundColor="#e4e3e8"
    {...props}
  >
    <rect x="190" y="90" rx="5" ry="5" width="50" height="250" />
    <rect x="260" y="140" rx="5" ry="5" width="50" height="200" />
    <rect x="330" y="40" rx="5" ry="5" width="50" height="300" />
    <rect x="400" y="120" rx="5" ry="5" width="50" height="220" />
    <rect x="120" y="190" rx="5" ry="5" width="50" height="150" />
    <rect x="50" y="240" rx="5" ry="5" width="50" height="100" />
    <rect x="0" y="0" rx="2" ry="2" width="4" height="380" />
    <rect x="496" y="0" rx="2" ry="2" width="4" height="380" />
    <rect x="0" y="0" rx="2" ry="2" width="500" height="4" />
    <rect x="0" y="376" rx="2" ry="2" width="500" height="4" />
  </ContentLoader>
);

export default ChartLoader;
