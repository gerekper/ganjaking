import icons from "../lib/icons";
import classNames from 'classnames';

const { TextControl, Icon, Animate } = wp.components;
const { useState } = wp.element;
const { __ } = wp.i18n;

const Option = props => {
  const [hovering, setHovering] = useState(false);
  const [touched, setTouched] = useState(false);

  return (
    <div
      className={classNames('mpcs-option-container', {'mpcs-option-correct': props.correct})}
      onMouseEnter={() => setHovering(true)}
      onMouseLeave={() => setHovering(false)}
    >
      <div className="mpcs-option-field">
        <div
          className="mpcs-option-answer"
          title={props.correct ? __('Correct answer', 'memberpress-courses') : null}
          onClick={props.mark}
        >
          {props.correct ? (
            props.multiple ? icons.checkboxChecked : icons.radioChecked
          ) : (
            props.multiple ? icons.checkboxUnchecked : icons.radioUnchecked
          )}
        </div>
        <TextControl
          className={classNames('mpcs-edit-option', {'mpcs-text-error': touched && !props.value})}
          placeholder={__('Write an answer', 'memberpress-courses')}
          value={props.value}
          onChange={value => props.update(value)}
          onBlur={() => setTouched(true)}
          onKeyDown={(e) => props.onKeyDown(e, props.index, props.value)}
          autoFocus={props.autoFocus}
        />
        {touched && !props.value && (
          <div className="mpcs-error">{__('Please enter a value.', 'memberpress-courses')}</div>
        )}
      </div>
      {hovering && props.showDelete && (
        <Animate options={{ origin: 'bottom center' }} type="appear">
          {() => (
            <Icon
              className="mpcs-option-delete"
              icon={icons.delete}
              size="19"
              onClick={props.delete}
            />
          )}
        </Animate>
      )}
    </div>
  );
};

export default Option;
