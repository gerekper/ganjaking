import classNames from "classnames";
import icons from "../lib/icons";
import QuestionBlockIcon from "./QuestionBlockIcon";
import QuestionDraggableChip from "./QuestionDraggableChip";

const { applyFilters } = wp.hooks;
const { createBlock } = wp.blocks;
const { Animate, Draggable, Icon } = wp.components;
const { __ } = wp.i18n;

class SidebarItem extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      isHovering: false,
      isExpanded: false,
    }
  }

  handleMouseEnter = () => {
    this.setState({ isHovering: true });
  }

  handleMouseLeave = () => {
     this.setState({ isHovering: false });
  }

  excerpt = () => {
    let length = applyFilters('mpcs_question_excerpt_length', 23);
    let ex = this.props.question.text.substring(0, length);

    if (ex.length >= length) {
      ex += '...';
    }

    return ex;
  }

  expand = () => {
    this.setState({ isExpanded: true });
  }

  collapse = () => {
    this.setState({ isExpanded: false });
  }

  render() {
    const { question } = this.props;
    const { isHovering, isExpanded } = this.state;
    const expandable = question.text.length > this.excerpt().length;
    const inQuiz = question.quizId > 0;

    return (
      <div className="mpcs-question mpcs-card-wrapper" key={ this.props.index }>
        <div
          className={
            isHovering && inQuiz ? "mepr-question mpcs-card" : "mepr-question"
          }
          onMouseEnter={() => this.handleMouseEnter(this.props.index)}
          onMouseLeave={() => this.handleMouseLeave(this.props.index)}
        >
          <Draggable
            transferData={{
              type: 'inserter',
              blocks: [createBlock(`memberpress-courses/${question.type}-question`, { questionId: question.id, duplicateOnMount: true })],
            }}
            __experimentalTransferDataType="wp-blocks"
            __experimentalDragComponent={
              <QuestionDraggableChip type={question.type} />
            }
          >
            {({ onDraggableStart, onDraggableEnd }) => (
              <div
                className="mpcs-question-drag-handle"
                draggable
                onDragStart={onDraggableStart}
                onDragEnd={onDraggableEnd}
              >
                {isHovering && (<Icon icon={icons.draggable} size={14} />
                )}
              </div>
            )}
          </Draggable>

          <QuestionBlockIcon type={question.type} size={14} className="mpcs-question-icon" />

          <div className="mepr-question-details">
            <div className="mepr-question-text">
              {isExpanded ? question.text : this.excerpt() }
            </div>

            {(isExpanded || isHovering) && inQuiz && (
              <div className="mepr-question-meta">
                {__("Quiz", "memberpress-courses")}: {question.quizTitle}
              </div>
            )}
          </div>

          <div className="mepr-actions">
            {expandable && isExpanded && (
              <Animate options={{ origin: 'middle center' }} type="appear">
                {({ className }) => (
                  <span className={classNames(className, 'button-link')} onClick={this.collapse}>
                    <Icon icon={icons.collapse} size="14" />
                  </span>
              )}
              </Animate>
            )}

            {expandable && isHovering && !isExpanded && (
              <Animate options={{ origin: 'middle center' }} type="appear">
                {({ className }) => (
                  <span className={classNames(className, 'button-link')} onClick={this.expand}>
                    <Icon icon={icons.expand} size="14" />
                  </span>
                )}
              </Animate>
            )}

            {(isHovering || isExpanded) && (
              <Animate options={{ origin: 'middle center' }} type="appear">
                {({ className }) => (
                  <span className={classNames(className, 'button-link')} onClick={() => this.props.copy(question.id)}>
                    <Icon icon={icons.plusCircle} size="14" />
                  </span>
                )}
              </Animate>
            )}

            {(isHovering || isExpanded) && !inQuiz && !question.hasAnswers && (
              <Animate options={{ origin: 'middle center' }} type="appear">
                {({ className }) => (
                  <span className={classNames(className, 'button-link')} onClick={() => this.props.delete(question.id)}>
                    <Icon icon={icons.delete} size="14" />
                  </span>
                )}
              </Animate>
            )}
          </div>
        </div>
      </div>
    );
  }
}

export default SidebarItem;
