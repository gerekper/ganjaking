import "../style.scss";
import Pagination from "../../builder/components/Pagination";
import Spinner from "../../builder/components/Spinner";
import SidebarItem from "./SidebarItem";
import { debounce } from "lodash";

const { PanelRow } = wp.components;
const { PluginDocumentSettingPanel } = wp.editPost;
const { compose } = wp.compose;
const { withSelect, withDispatch, subscribe } = wp.data;
const { createBlock } = wp.blocks;
const { __ } = wp.i18n;

class QuestionsSidebar extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      search: "",
      isLoading: false,
      page: 1,
      isAdding: false,
    };

    this.wasSavingPost = false;
    this.wasAutosavingPost = false;
    this.unsubscribe = () => {};
  }

  componentDidMount() {
    this.unsubscribe = subscribe(() => {
      const { isSavingPost, isAutosavingPost } = this.props;
      const shouldTriggerRefresh = this.wasSavingPost && !isSavingPost && !this.wasAutosavingPost;

      this.wasSavingPost = isSavingPost;
      this.wasAutosavingPost = isAutosavingPost;

      if (shouldTriggerRefresh) {
        this.refreshSidebarQuestions(this.state.page, this.state.search);
      }
    });

    // Open the panel by default unless closed by preference
    const panel = 'mpcs-questions-sidebar/mpcs-questions-sidebar';
    const preferences = this.props.getPreference('panels');
    const hasPreference = preferences && preferences[panel];

    if (!hasPreference && !this.props.isEditorPanelOpened(panel)) {
      this.props.toggleEditorPanelOpened(panel);
    }
  }

  componentWillUnmount() {
    this.unsubscribe();
  }

  addQuestionBlock = (id) => {
    this.props.duplicateQuestion(id, this.props.currentPostId).then(result => {
      let newBlock = createBlock(`memberpress-courses/${result.question.type}-question`, {
        questionId: result.question.questionId,
      });

      this.props.insertBlock(newBlock).then(() => {
        this.refreshSidebarQuestions(this.state.page, this.state.search);
      });
    });
  }

  refreshSidebarQuestions = debounce((page, search) => {
    this.props.refreshSidebarQuestions(this.props.currentPostId, page, search).then((resolve) => {
      this.setState({ isLoading: false, sidebar: resolve.data });
    });
  }, 300);

  handleSearch = (search) => {
    this.setState({ search: search, isLoading: true });
    this.refreshSidebarQuestions(1, search);
  };

  handlePaginate = (e, page) => {
    e.preventDefault();

    this.setState({ page: page, isLoading: true  });
    this.refreshSidebarQuestions(page, this.state.search);
  };

  deleteOrphanedQuestion = (id) => {
    this.props.maybeRemoveOrOrphanQuestion(id).then(() => {
      this.refreshSidebarQuestions(this.state.page, this.state.search);
    });
  }

  render() {
    const { questions, searchMeta } = this.props.sidebar;

    return (
      <PluginDocumentSettingPanel
        name="mpcs-questions-sidebar"
        title={__("Questions", "memberpress-courses")}
        className="mpcs-questions-sidebar"
      >
        {questions && (
          <PanelRow className="mpcs-search-questions">
            <input
              type="text"
              className="mpcs-search-questions-term"
              placeholder={__("Search Questions", "memberpress-courses")}
              value={this.state.search}
              onChange={(e) => this.handleSearch(e.target.value)}
            />
            {this.state.isLoading && <Spinner />}
          </PanelRow>
        )}
        {questions && (
          <PanelRow className="mpcs-questions-sidebar-list">
            { questions.map((question, index) => (
              <SidebarItem
                key={ index }
                question={ question }
                index={ index }
                delete={ this.deleteOrphanedQuestion }
                copy={ this.addQuestionBlock }
              />
            )) }
            <Pagination
              className="mpcs-questions-pagination"
              paged={ this.state.page }
              maxPage={ searchMeta.pages }
              handlePaginate={ this.handlePaginate }
            />
          </PanelRow>
        )}
      </PluginDocumentSettingPanel>
    );
  }
}

export default compose([
  withDispatch((dispatch) => {
    const {
      refreshSidebarQuestions,
      maybeRemoveOrOrphanQuestion,
      duplicateQuestion,
    } = dispatch('memberpress/course/question');

    const { toggleEditorPanelOpened } = dispatch('core/edit-post');
    const { insertBlock } = dispatch('core/block-editor');

    return {
      refreshSidebarQuestions,
      maybeRemoveOrOrphanQuestion,
      duplicateQuestion,
      toggleEditorPanelOpened,
      insertBlock,
    };
  }),
  withSelect((select) => {
    return {
      sidebar: select('memberpress/course/question').getAll(),
      currentPostId: select('core/editor').getCurrentPost().id,
      isSavingPost: select('core/editor').isSavingPost(),
      isAutosavingPost: select('core/editor').isAutosavingPost(),
      isEditorPanelOpened: select('core/edit-post').isEditorPanelOpened,
      getPreference: select('core/edit-post').getPreference,
    };
  }),
])(QuestionsSidebar);
