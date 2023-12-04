import React from "react";
import AnalyticsMenu from "./components/AnalyticsMenu";
import Header from "./components/Header";
import "./scss/analytics.scss";

const BetterDocsAnalytics = () => {
    return (
        <div className="betterdocs-admin-analytics-wrapper">
            <Header />
            <AnalyticsMenu />
        </div>
    );
};

export default BetterDocsAnalytics;
