import React from "react";
import { ReactComponent as Check } from "../../images/Check.svg";

const Checkbox = (props) => {
  return (
    <label className="btd-checkbox">
      <input type="checkbox" {...props} />
      <span className="btd-checkbox-inner">
        <span className="btd-checkbox-icon">
          <Check />
        </span>
        {props?.text && (
          <span className="btd-checkbox-text">{props?.text}</span>
        )}
      </span>
    </label>
  );
};

export default Checkbox;
