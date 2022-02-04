import QuestionBlockIcon from './QuestionBlockIcon';

const { Flex, FlexItem } = wp.components;

function QuestionDraggableChip({ type }) {
  return (
    <div className="block-editor-block-draggable-chip-wrapper">
      <div className="block-editor-block-draggable-chip">
        <Flex
          justify="center"
          className="block-editor-block-draggable-chip__content"
        >
          <FlexItem>
            <QuestionBlockIcon type={type} size={20} />
          </FlexItem>
          <FlexItem>
            <svg
              width="18"
              height="18"
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 18 18"
            >
              <path d="M5 4h2V2H5v2zm6-2v2h2V2h-2zm-6 8h2V8H5v2zm6 0h2V8h-2v2zm-6 6h2v-2H5v2zm6 0h2v-2h-2v2z" />
            </svg>
          </FlexItem>
        </Flex>
      </div>
    </div>
  );
}

export default QuestionDraggableChip;
