import apiFetch from "@wordpress/api-fetch";
import { ReactComponent as Inc } from "./images/inc.svg";
import { ReactComponent as Dec } from "./images/dec.svg";

// this function work for fetch plugin info
export const getPluginData = () => {
  return apiFetch({
    path:
        "betterdocs/plugin_info"
  })
      .then((res) => res)
      .catch((err) => err);
};

// this function work for fetch reaction chart data
export const getFeedbackChartData = (start_date, end_date) => {
  if (start_date && end_date) {
    return apiFetch({
      path:
        "betterdocs/feedbacks?start_date=" +
        start_date +
        "&end_date=" +
        end_date,
    })
      .then((res) => res)
      .catch((err) => err);
  }
  return apiFetch({
    path: "betterdocs/feedbacks",
  })
    .then((res) => res)
    .catch((err) => err);
};

// this function work for fetch reaction table data
export const getMostHelpfulData = (perPage = 10, page = 1) => {
  return apiFetch({
    path:
      "betterdocs/feedbacks/docs?orderby=most_helpful&per_page=" +
      perPage +
      "&page_now=" +
      page,
  })
    .then((res) => res)
    .catch((err) => err);
};

// this function work for fetch reaction table data
export const getLeastHelpfulData = (perPage = 10, page = 1) => {
  return apiFetch({
    path:
      "betterdocs/feedbacks/docs?orderby=least_helpful&per_page=" +
      perPage +
      "&page_now=" +
      page,
  })
    .then((res) => res)
    .catch((err) => err);
};

// this function work for fetch overview chart data
export const getOverviewChartData = (start_date, end_date) => {
  if (start_date && end_date) {
    return apiFetch({
      path:
        "betterdocs/feedbacks/overview?start_date=" +
        start_date +
        "&end_date=" +
        end_date,
    })
      .then((res) => res)
      .catch((err) => err);
  }
  return apiFetch({
    path: "betterdocs/feedbacks/overview",
  })
    .then((res) => res)
    .catch((err) => err);
};

// this function work for fetch leading doc data
export const getLeadingDocsData = (perPage = 5, page = 1) => {
  return apiFetch({
    path:
      "betterdocs/leading_docs?per_page=" +
      perPage +
      "&page_now=" +
      page,
    parse: false,
  })
    .then((res) => res)
    .catch((err) => err);
};

// this function work for fetch leading category data
export const getLeadingCategoryData = (perPage = 10, page = 1) => {
  return apiFetch({
    path:
      "betterdocs/leading_category?per_page=" + perPage + "&page_now=" + page,
  })
    .then((res) => res)
    .catch((err) => err);
};

// this function work for fetch leading knowledge base data
export const getLeadingKBData = (perPage = 10, page = 1) => {
  return apiFetch({
    path: "betterdocs/leading_kb?per_page=" + perPage + "&page_now=" + page,
  })
    .then((res) => res)
    .catch((err) => err);
};

// this function work for fetch popular search data
export const getPopularSearch = (perPage = 10, page = 1) => {
  return apiFetch({
    path: "betterdocs/search/all?per_page=" + perPage + "&page_now=" + page,
  })
    .then((res) => res)
    .catch((err) => err);
};

// this function work for fetch null search data
export const getNullSearch = (perPage = 10, page = 1) => {
  return apiFetch({
    path:
      "betterdocs/search/not_found?per_page=" + perPage + "&page_now=" + page,
  })
    .then((res) => res)
    .catch((err) => err);
};

// this function work for fetch doc category data
export const getDocCategory = (id) => {
  return apiFetch({
    path: "wp/v2/doc_category?post=" + id,
  })
    .then((res) => res)
    .catch((err) => err);
};

// this function work for fetch doc knowledge base data
export const getDocKnowledgeBase = (id) => {
  return apiFetch({
    path: "wp/v2/knowledge_base?post=" + id,
  })
    .then((res) => res)
    .catch((err) => err);
};

// this function work for fetch doc author data
export const getAuthorData = (id) => {
  return apiFetch({
    path: "wp/v2/users/" + id,
  })
    .then((res) => res)
    .catch((err) => err);
};

// this function work for fetch total feedback count
export const getTotalFeedback = () => {
  return apiFetch({ path: "betterdocs/feedbacks/totalCount" })
    .then((res) => res)
    .catch((err) => err);
};

// this function work for fetch search chart data
export const getSearchChartData = (start_date, end_date) => {
  if (start_date && end_date) {
    return apiFetch({
      path:
        "betterdocs/search/date?start_date=" +
        start_date +
        "&end_date=" +
        end_date,
    })
      .then((res) => res)
      .catch((err) => err);
  }
  return apiFetch({
    path: "betterdocs/search/date",
  })
    .then((res) => res)
    .catch((err) => err);
};

// this function work for fetch search pie chart data
export const getSearchPieChartData = () => {
  return apiFetch({
    path: "betterdocs/search/overview",
  })
    .then((res) => res)
    .catch((err) => err);
};

// this function work for fetch setting options value
export const getSettingOptions = () => {
  return apiFetch({
    path: "betterdocs/settings",
  })
    .then((res) => res)
    .catch((err) => err);
};

// this function is for convert number to string like 5.2K, 6.4M, 2.1T etc.
export const intToString = (num) => {
  if (num != undefined) {
    num = num.toString().replace(/[^0-9.]/g, "");
    if (num < 1000) {
      return num;
    }
    let si = [
      { v: 1e3, s: "K" },
      { v: 1e6, s: "M" },
      { v: 1e9, s: "B" },
      { v: 1e12, s: "T" },
      { v: 1e15, s: "P" },
      { v: 1e18, s: "E" },
    ];
    let index;
    for (index = si.length - 1; index > 0; index--) {
      if (num >= si[index].v) {
        break;
      }
    }
    return (
      (num / si[index].v).toFixed(2).replace(/\.0+$|(\.[0-9]*[1-9])0+$/, "$1") +
      si[index].s
    );
  }
};

export const isWhatPercentOf = (numA, numB) => {
  return isFinite(numA / numB) * 100 ? (numA / numB) * 100 : 0;
};

export const comparisonFactor = (current, previous, dateDifference) => {
  let type = 0,
    factor = 0;
  if (!current) {
    return "";
  }
  if (current == previous) {
    type = 0;
    return "";
  }

  if (current > previous) {
    type = 1;
  }
  if (current < previous) {
    type = -1;
  }
  if (previous == 0) {
    factor = current * 100;
  } else if (current == 0) {
    factor = previous * 100;
  } else {
    factor = ((current - previous) / previous) * 100;
  }

  return (
    <p className="btd-chart-comparison">
      <span className={`btd-comparison-highlight ${type == 1 ? "inc" : "dec"}`}>
        {type == 1 ? <Inc /> : <Dec />}
        {`${Math.abs(factor).toFixed(2)}%`}
      </span>
      <span>{`vs Previous ${
        dateDifference != 0 ? `${dateDifference} Days` : "Day"
      }`}</span>
    </p>
  );
};

export const Tooltip = (props) => {
  return (
    <span className={`btd-tooltip ${props.className ? props.className : ""}`}>
      <button
        className={`btd-tooltip-button ${
          props.buttonClassName ? props.buttonClassName : ""
        }`}
      >
        {props.buttonContent}
      </button>
      <span
        className={`btd-tooltip-content ${
          props.tooltipClassName ? props.tooltipClassName : ""
        }`}
      >
        <span className="btd-tooltip-content-inner">
          {props.tooltipContent}
        </span>
      </span>
    </span>
  );
};
