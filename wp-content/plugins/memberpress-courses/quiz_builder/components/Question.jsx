import icons from "../lib/icons";
import Option from "./Option.jsx";
import getDefaultQuestion from '../lib/getDefaultQuestion';
import Tooltip from "../../builder/components/Tooltip";
import classNames from 'classnames';

const { Component, Fragment } = wp.element;
const { PanelBody, TextareaControl, TextControl, ToggleControl } = wp.components;
const { InspectorControls, PlainText } = wp.blockEditor;
const { compose } = wp.compose;
const { withSelect, withDispatch } = wp.data;
const { __ } = wp.i18n;

class Question extends Component {
  constructor(props) {
    super(props);

    this.state = {
      focused: false,
      touched: false,
      autoFocusOptions: false,
    };
  }

  componentDidMount() {
    const duplicateOnMount = this.props.attributes.duplicateOnMount;

    if (duplicateOnMount) {
      this.props.setAttributes({ duplicateOnMount: false });
    }

    if (!this.props.question) {
      // Add a placeholder question to the store so that rendering the block does not need to wait
      this.props.addPlaceholder(this.props.clientId, getDefaultQuestion(this.props.type));

      if (duplicateOnMount) {
        this.props.duplicateQuestion(this.props.attributes.questionId, this.props.currentPostId).then(result => {
          this.props.setAttributes({ questionId: result.question.questionId });
        });
      } else {
        // Because the questions are saved after the post is saved, we need to create a placeholder in the database
        // to get an ID so that the question ID is within the post content, otherwise the question data will not load
        // when the editor is reloaded.
        this.props.getNextQuestionId(this.props.currentPostId, this.props.clientId).then(result => {
          this.props.setAttributes({ questionId: result.id });
        });
      }
    } else {
      // Detect if this block has been duplicated by seeing if there is another block with the same question ID. The
      // original block will be at index 0, so if the index of this block is greater than that, it is a duplicate.
      const isDuplicate = this.props.getBlocks()
        .filter(block => block.name.indexOf('memberpress-courses') === 0 && block.attributes.questionId === this.props.attributes.questionId)
        .map(block => block.clientId)
        .indexOf(this.props.clientId) > 0;

      if (isDuplicate) {
        this.props.duplicateQuestion(this.props.attributes.questionId, this.props.currentPostId).then(result => {
          this.props.setAttributes({ questionId: result.question.questionId });
        });
      }
    }
  }

  /**
   * Within the store, the question key is the question ID for stored questions, but for placeholder questions it is
   * the block client ID.
   *
   * @return {number|string}
   */
  getKey() {
    return this.props.attributes.questionId || this.props.clientId;
  }

  getNumberValue = value => {
    let number = parseInt(value, 10);

    if (isNaN(number)) {
      number = 0;
    }

    return number;
  }

  addOption() {
    this.setState({ autoFocusOptions: true });
    this.props.addOption(this.getKey(), { value: '', isCorrect: false });
  }

  optionOnKeyDown(e, index, value) {
    if (!!value && e.keyCode === 13 && index === this.props.question.options.length - 1) {
      this.addOption();
    }
  }

  render() {
    const { question } = this.props;
    const showFeedback = ['multiple-choice', 'multiple-answer', 'true-false'].includes(this.props.type);
    const hasOptions = ['multiple-choice', 'multiple-answer'].includes(this.props.type);

    if(!question) {
      return null;
    }

    return (
      <Fragment>
        <InspectorControls key="setting">
          <PanelBody>
            <ToggleControl
              label={__('Required', 'memberpress-courses')}
              className="mpcs-required-input"
              checked={question.required}
              onChange={value => this.props.updateQuestion(this.getKey(), { required: value })}
            />
            <TextControl
              label={__('Points', 'memberpress-courses')}
              className="mpcs-point-input"
              value={question.points}
              onChange={value => this.props.updateQuestion(this.getKey(), { points: this.getNumberValue(value) })}
              help={
                <Fragment>
                  {this.props.type === 'short-answer' && (
                    <Tooltip
                      heading={__('Points', 'memberpress-courses')}
                      message={__('Short Answer questions will be marked as correct and awarded the points entered here as long as the student enters any value.', 'memberpress-courses')}
                      edge="right"
                    />
                  )}
                  {this.props.type === 'essay' && (
                    <Tooltip
                      heading={__('Points', 'memberpress-courses')}
                      message={__('Essay questions will be marked as correct and awarded the points value as long as the student enters an answer that is more than the min characters and less than the max characters.', 'memberpress-courses')}
                      edge="right"
                    />
                  )}
                </Fragment>
              }
            />
            {showFeedback && (
              <TextareaControl
                label={
                  <Fragment>
                    {__('Feedback for Wrong Answer: ', 'memberpress-courses')}
                    <Tooltip
                      heading={__('Feedback for Wrong Answer', 'memberpress-courses')}
                      message={__("Explain the correct answer here. The information in this box only displays if 'Show Answer' is enabled in the parent Course settings, and the user answers the question incorrectly. If 'Show Answers' is not enabled then this information will not be displayed.", 'memberpress-courses')}
                      edge="right"
                    />
                  </Fragment>
                }
                className="mpcs-feedback-input"
                value={question.feedback}
                onChange={value => this.props.updateQuestion(this.getKey(), { feedback: value })}
              />
            )}
            {this.props.type === 'essay' && (
              <TextControl
                label={__('Min Characters', 'memberpress-courses')}
                className="mpcs-min-input"
                value={question.min}
                onChange={value => {
                  let number = this.getNumberValue(value);

                  // Min must be at least one.
                  if (number < 1) {
                    number = 1;
                  }

                  this.props.updateQuestion(this.getKey(), { min: number });
                }}
                help={
                  <Tooltip
                    heading={__('Min Length', 'memberpress-courses')}
                    message={__('Minimum length of the answer in characters.', 'memberpress-courses')}
                    edge="right"
                  />
                }
              />
            )}
            {this.props.type === 'essay' && (
              <TextControl
                label={__('Max Characters', 'memberpress-courses')}
                className="mpcs-max-input"
                value={question.max}
                onChange={value => {
                  let number = this.getNumberValue(value);

                  // Probably won't ever be an issue, but this is the max
                  // number of characters for a longtext column in MySql Server
                  if (number > 4294967295) {
                    number = 4294967295;
                  }

                  this.props.updateQuestion(this.getKey(), { max: number });
                }}
                help={
                  <Tooltip
                    heading={__('Max Length', 'memberpress-courses')}
                    message={__('Maximum length of the answer in characters.', 'memberpress-courses')}
                    edge="right"
                  />
                }
              />
            )}
          </PanelBody>
        </InspectorControls>
        <div className="mpcs-question-block">
          <div className="mpcs-question-block-inner">
            <div className="mpcs-question-text">
              <div className={classNames('mpcs-question-field', {'mpcs-focused': this.state.focused})}>
                <PlainText
                  className="mpcs-question-input"
                  placeholder={this.props.heading}
                  value={question.question}
                  onChange={value => {
                    this.props.updateQuestion(this.getKey(), {
                      question: value,
                      isError: false,
                      errorMessage: '',
                    });
                  }}
                  onFocus={() => {
                    this.setState({ focused: true });
                  }}
                  onBlur={() => {
                    this.setState({
                      touched: true,
                      focused: false,
                    });
                  }}
                />
              </div>
              {this.state.touched && !question.question && (
                <div className="mpcs-error">{__('Please enter a question.', 'memberpress-courses')}</div>
              )}
              {question.isError && (
                <div className="mpcs-question-save-error mpcs-error">
                  <span>{question.errorMessage}</span>
                </div>
              )}
            </div>
            {question.options && (
              <div className="mpcs-edit-options">
                {question.options.map((option, index) => (
                  <Option
                    key={index}
                    index={index}
                    value={option.value}
                    correct={option.isCorrect}
                    multiple={this.props.type === 'multiple-answer'}
                    update={value => this.props.updateOptionValue(this.getKey(), index, value)}
                    mark={() => this.props.toggleOptionCorrect(this.getKey(), index)}
                    showDelete={question.options.length > 1}
                    delete={() => question.options.length > 1 && this.props.deleteOption(this.getKey(), index)}
                    onKeyDown={this.optionOnKeyDown.bind(this)}
                    autoFocus={this.state.autoFocusOptions}
                  />
                ))}
                {question.options.filter(option => option.isCorrect).length === 0 && (
                  <div className="mpcs-error">
                    {this.props.type === 'multiple-choice' ? __('You must choose a correct answer.', 'memberpress-courses') : __('You must choose at least one correct answer.', 'memberpress-courses')}
                  </div>
                )}
              </div>
            )}
            {hasOptions && (
              <div className="mpcs-add-option">
                <span onClick={() => this.addOption()}>
                  {__('Add answer option', 'memberpress-courses')}
                </span>
              </div>
            )}
            {this.props.type === 'true-false' && (
              <div className="mpcs-true-false-answer">
                <div
                  className={classNames('mpcs-true-false-true', { 'mpcs-option-correct': question.answer === '1' })}
                  onClick={() => this.props.updateQuestion(this.getKey(), { answer: '1' })}
                >
                  {question.answer === '1' ? icons.radioChecked : icons.radioUnchecked}
                  {__('True', 'memberpress-courses')}
                </div>
                <div
                  className={classNames('mpcs-true-false-false', { 'mpcs-option-correct': question.answer === '0' })}
                  onClick={() => this.props.updateQuestion(this.getKey(), { answer: '0' })}
                >
                  {question.answer === '0' ? icons.radioChecked : icons.radioUnchecked}
                  {__('False', 'memberpress-courses')}
                </div>
              </div>
            )}
          </div>
        </div>
      </Fragment>
    );
  }
}

export default compose([
  withDispatch((dispatch) => {
    const {
      getNextQuestionId,
      addPlaceholder,
      refreshSidebarQuestions,
      duplicateQuestion,
      updateQuestion,
      addOption,
      updateOptionValue,
      toggleOptionCorrect,
      deleteOption,
    } = dispatch("memberpress/course/question");

    return {
      getNextQuestionId,
      addPlaceholder,
      refreshSidebarQuestions,
      duplicateQuestion,
      updateQuestion,
      addOption,
      updateOptionValue,
      toggleOptionCorrect,
      deleteOption,
    };
  }),

  withSelect((select, props) => {
    return {
      question: select('memberpress/course/question').getQuestion(props.attributes.questionId || props.clientId),
      isSavingPost: select("core/editor").isSavingPost(),
      isAutosavingPost: select("core/editor").isAutosavingPost(),
      currentPostId: select("core/editor").getCurrentPost().id,
      getBlocks: select("core/block-editor").getBlocks,
    };
  }),
])(Question);
