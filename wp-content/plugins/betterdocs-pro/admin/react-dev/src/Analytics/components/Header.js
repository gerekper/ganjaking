import React, {useEffect, useState} from "react";
import { __ } from "@wordpress/i18n";
import { ReactComponent as BTDLogo } from "../images/BetterDocs Icons.svg";
import { getPluginData } from "../function";

const Header = () => {
  const [pluginData, setPluginData] = useState(undefined);
  useEffect(() => {
    getPluginData()
    .then(async (data) => {
      setPluginData(await data);
    })
    .catch((err) => console.log(err));
  }, []);
  return (
    <div className="betterdocs-analytics-header">
      <div className="betterdocs-header-left">
        <div className="betterdocs-admin-logo-inline">
          <BTDLogo />
        </div>
        <h2 className="title">
          {__("BetterDocs Analytics", "betterdocs-pro")}
        </h2>
      </div>
      <div className="betterdocs-header-right">
        <span>
          {__("Version: ", "betterdocs-pro")}
          <strong>{pluginData?.betterdocs_version}</strong>
        </span>
        <span>
          {__("Pro Version: ", "betterdocs-pro")}
          <strong>{pluginData?.betterdocs_pro_version}</strong>
        </span>
      </div>
    </div>
  );
};

export default Header;
