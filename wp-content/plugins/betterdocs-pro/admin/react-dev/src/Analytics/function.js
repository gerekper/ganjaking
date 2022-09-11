import axios from "axios";
import { ReactComponent as Inc } from "./images/inc.svg";
import { ReactComponent as Dec } from "./images/dec.svg";

// this function work for fetch overview chart data
export const getOverviewChartData = async ({ queryKey }) => {
  const [_, dateRange, postID] = queryKey;
  let res;
  if (dateRange == -1) {
    if (postID) {
      res = await axios.get(
        `${betterdocs.rest_url}betterdocs/feedbacks/overview?post_id=${postID}`
      );
    } else {
      res = await axios.get(
        `${betterdocs.rest_url}betterdocs/feedbacks/overview`
      );
    }
  } else if (dateRange && Object.entries(dateRange).length) {
    let start_date = moment(dateRange?.start).format("YYYY-MM-DD"),
      end_date = moment(dateRange?.end).format("YYYY-MM-DD");

    if (postID) {
      res = await axios.get(
        `${betterdocs.rest_url}betterdocs/feedbacks/overview?start_date=${start_date}&end_date=${end_date}&post_id=${postID}`
      );
    } else {
      res = await axios.get(
        `${betterdocs.rest_url}betterdocs/feedbacks/overview?start_date=${start_date}&end_date=${end_date}`
      );
    }
  }
  return res.data;
};

// this function work for fetch reaction chart data
export const getFeedbackChartData = async ({ queryKey }) => {
  const [_, dateRange] = queryKey;
  let res;
  if (dateRange == -1) {
    res = await axios.get(`${betterdocs.rest_url}betterdocs/feedbacks`);
  } else if (dateRange && Object.entries(dateRange).length) {
    let start_date = moment(dateRange?.start).format("YYYY-MM-DD"),
      end_date = moment(dateRange?.end).format("YYYY-MM-DD");
    res = await axios.get(
      `${betterdocs.rest_url}betterdocs/feedbacks?start_date=${start_date}&end_date=${end_date}`
    );
  }
  return res.data;
};

// this function work for fetch search chart data
export const getSearchChartData = async ({ queryKey }) => {
  const [_, dateRange] = queryKey;
  let res;
  if (dateRange == -1) {
    res = await axios.get(`${betterdocs.rest_url}betterdocs/search/date`);
  } else if (dateRange && Object.entries(dateRange).length) {
    let start_date = moment(dateRange?.start).format("YYYY-MM-DD"),
      end_date = moment(dateRange?.end).format("YYYY-MM-DD");
    res = await axios.get(
      `${betterdocs.rest_url}betterdocs/search/date?start_date=${start_date}&end_date=${end_date}`
    );
  }
  return res.data;
};

// this function work for fetch Total Data Count
export const getTotalDataCount = async ({ queryKey }) => {
  const [_, dateRange, postID] = queryKey;
  let res;
  if (dateRange == -1) {
    if (postID) {
      res = await axios.get(
        `${betterdocs.rest_url}betterdocs/feedbacks/totalCount?post_id=${postID}`
      );
    } else {
      res = await axios.get(
        `${betterdocs.rest_url}betterdocs/feedbacks/totalCount`
      );
    }
  } else if (dateRange && Object.entries(dateRange).length) {
    let start_date = moment(dateRange?.start).format("YYYY-MM-DD"),
      end_date = moment(dateRange?.end).format("YYYY-MM-DD");

    if (postID) {
      res = await axios.get(
        `${betterdocs.rest_url}betterdocs/feedbacks/totalCount?start_date=${start_date}&end_date=${end_date}&post_id=${postID}`
      );
    } else {
      res = await axios.get(
        `${betterdocs.rest_url}betterdocs/feedbacks/totalCount?start_date=${start_date}&end_date=${end_date}`
      );
    }
  }
  return res.data;
};

// this function work for fetch leading doc data
export const getLeadingDocsData = async ({ queryKey }) => {
  const [_, perPage, page] = queryKey;
  const { data } = await axios.get(
    `${betterdocs.rest_url}betterdocs/leading_docs?per_page=${perPage}&page_now=${page}`
  );
  return data;
};

// this function work for fetch leading category data
export const getLeadingCategoryData = async ({ queryKey }) => {
  const [_, perPage, page] = queryKey;
  const { data } = await axios.get(
    `${betterdocs.rest_url}betterdocs/leading_category?per_page=${perPage}&page_now=${page}`
  );
  return data;
};

// this function work for fetch leading knowledge base data
export const getLeadingKBData = async ({ queryKey }) => {
  const [_, perPage, page] = queryKey;
  const { data } = await axios.get(
    `${betterdocs.rest_url}betterdocs/leading_kb?per_page=${perPage}&page_now=${page}`
  );
  return data;
};

// this function work for fetch most helpful feedback
export const getMostHelpfulData = async ({ queryKey }) => {
  const [_, perPage, page] = queryKey;
  const { data } = await axios.get(
    `${betterdocs.rest_url}betterdocs/feedbacks/docs?orderby=most_helpful&per_page=${perPage}&page_now=${page}`
  );
  return data;
};

// this function work for fetch least helpful feedback
export const getLeastHelpfulData = async ({ queryKey }) => {
  const [_, perPage, page] = queryKey;
  const { data } = await axios.get(
    `${betterdocs.rest_url}betterdocs/feedbacks/docs?orderby=least_helpful&per_page=${perPage}&page_now=${page}`
  );
  return data;
};

// this function work for fetch popular search data
export const getPopularSearch = async ({ queryKey }) => {
  const [_, perPage, page] = queryKey;
  const { data } = await axios.get(
    `${betterdocs.rest_url}betterdocs/search/all?per_page=${perPage}&page_now=${page}`
  );
  return data;
};

// this function work for fetch null search data
export const getNullSearch = async ({ queryKey }) => {
  const [_, perPage, page] = queryKey;
  const { data } = await axios.get(
    `${betterdocs.rest_url}betterdocs/search/not_found?per_page=${perPage}&page_now=${page}`
  );
  return data;
};

// this function work for fetch setting options value
export const getSettingOptions = async () => {
  const { data } = await axios.get(`${betterdocs.rest_url}betterdocs/settings`);
  return data;
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

// this function is for compare two value and find the percentage of them.
export const isWhatPercentOf = (numA, numB) => {
  return isFinite(numA / numB) * 100 ? (numA / numB) * 100 : 0;
};

// this function is for compare two value and returne the percentage of them based on date difference.
export const comparisonFactor = (cur, prev, dateDifference) => {
  let type = 0,
    factor = 0,
    current = cur == null ? "0" : cur,
    previous = prev == null ? "0" : prev;

  if (!current || current == previous) {
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

// this function is for create a tooltip.
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

// this function is for formating the fetched data to the format that the chart required.
export const formatDataForChart = (data, filterState) => {
  const dataForChart = {};
  if (filterState && data && data?.length) {
    dataForChart.labels = data
      .map((obj) =>
        moment(obj.created_at || obj.search_date || obj.date).format(
          "MMM D YYYY"
        )
      )
      .reverse();
    dataForChart.colors = filterState
      .filter((item) => item.checked)
      .map((item) => item.color);
    dataForChart.count = filterState
      .filter((item) => item.checked && item.enabled)
      .map((item) => {
        return {
          name: item.label,
          data: data
            .map(
              (data) =>
                data.hasOwnProperty(item.id) &&
                (data[item.id] == null ? "0" : data[item.id])
            )
            .reverse(),
        };
      });
  }
  return dataForChart;
};
