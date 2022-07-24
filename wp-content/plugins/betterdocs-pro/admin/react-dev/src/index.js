import React from "react";
import ReactDOM from "react-dom";
import BetterDocsAnalytics from "./Analytics";

const BetterDocsApp = () => {
  return (
    <>
      <BetterDocsAnalytics />
    </>
  );
};

ReactDOM.render(
  <BetterDocsApp />,
  document.getElementById("betterdocsAnalytics")
);
