import React from "react";
import ReactDOM from "react-dom";
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import BetterDocsAnalytics from "./Analytics";

const BetterDocsApp = () => {
  return (
    <>
      <BetterDocsAnalytics />
    </>
  );
};

const queryClient = new QueryClient();

ReactDOM.render(
  <QueryClientProvider client={queryClient}>
    <BetterDocsApp />
  </QueryClientProvider>,
  document.getElementById("betterdocsAnalytics")
);
