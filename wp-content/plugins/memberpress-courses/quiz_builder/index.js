import "./style.scss";
import "../builder/style.scss";
import "./store/store";
import "./lib/save";
import Header from "../builder/views/Header";
import QuestionsSidebar from "./components/QuestionsSidebar";
import Question from "./components/Question.jsx";
import icons from "./lib/icons";

const { __ } = wp.i18n;
const { registerBlockType, getCategories, setCategories } = wp.blocks;
const { registerPlugin } = wp.plugins;
const { Icon } = wp.components;

//This attribute is common to all blocks
var attributes = {
  questionId: { type: 'integer', default: 0 },
  duplicateOnMount: { type: 'boolean', default: false },
}

//these supports variables are common to all blocks
var supports = {
  defaultStylePicker: false,
  html: false,
  reusable: false
}

setCategories([
  {
    slug: 'mpcs-questions',
    title: __('Courses Questions', 'memberpress-courses'),
    icon: <Icon icon={icons.MemberPress} />
  },
  ...getCategories().filter(({ slug }) => slug !== "mpcs-questions")
]);

registerBlockType('memberpress-courses/multiple-choice-question', {
  title: __('Multiple Choice', 'memberpress-courses'),
  icon: <Icon icon={icons.multipleChoiceBlock}/>,
  description: __('Add a multiple choice question to the MemberPress-Courses Quiz.', 'memberpress-courses'),
  category: 'mpcs-questions',
  attributes: attributes,
  supports: supports,
  edit: function (props) {
    return <Question type="multiple-choice"
                     heading={__('Multiple Choice Question', 'memberpress-courses')}
                     { ...props } />;
  },
});

registerBlockType('memberpress-courses/multiple-answer-question', {
  title: __('Multiple Answer', 'memberpress-courses'),
  icon: <Icon icon={icons.multipleAnswerBlock}/>,
  description: __('Add a multiple answer question to the MemberPress-Courses Quiz.', 'memberpress-courses'),
  category: 'mpcs-questions',
  attributes: attributes,
  supports: supports,
  edit: function (props) {
    return <Question type="multiple-answer"
                     heading={__('Multiple Answer Question', 'memberpress-courses')}
                     { ...props } />;
  },
});

registerBlockType('memberpress-courses/true-false-question', {
  title: __('True/False', 'memberpress-courses'),
  icon: <Icon icon={icons.trueFalseBlock}/>,
  description: __('Add a true/false question to the MemberPress-Courses Quiz.', 'memberpress-courses'),
  category: 'mpcs-questions',
  attributes: attributes,
  supports: supports,
  edit: function (props) {
    return <Question type="true-false"
                     heading={__('True/False Question', 'memberpress-courses')}
                     { ...props } />;
  },
});

registerBlockType('memberpress-courses/short-answer-question', {
  title: __('Short Answer', 'memberpress-courses'),
  icon: <Icon icon={icons.shortAnswerBlock}/>,
  description: __('Add a short answer question to the MemberPress-Courses Quiz.', 'memberpress-courses'),
  category: 'mpcs-questions',
  attributes: attributes,
  supports: supports,
  edit: function (props) {
    return <Question type="short-answer"
                     heading={__('Short Answer Question', 'memberpress-courses')}
                     { ...props } />;
  },
});

registerBlockType('memberpress-courses/essay-question', {
  title: __('Essay', 'memberpress-courses'),
  icon: <Icon icon={icons.essayBlock}/>,
  description: __('Add an essay question to the MemberPress-Courses Quiz.', 'memberpress-courses'),
  category: 'mpcs-questions',
  attributes: attributes,
  supports: supports,
  edit: function (props) {
    return <Question type="essay"
                     heading={__('Essay Question', 'memberpress-courses')}
                     { ...props } />;
  },
});

if (document.getElementById("mpcs-admin-header-wrapper")) {
  wp.element.render(
    <Header />,
    document.getElementById("mpcs-admin-header-wrapper")
  );
}

registerPlugin("mpcs-questions-sidebar", {
  render() {
    const postType = wp.data.select("core/editor").getCurrentPostType();
    if ("mpcs-quiz" != postType) {
      return null;
    }
    return <QuestionsSidebar/>;
  },
  icon: null
});
