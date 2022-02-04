const { apiFetch } = wp;

const initialState = MPCS_Course_Data.state;

const reducer = (state = initialState, action) => {
  switch (action.type) {
    case "FETCH_ALL":
      return Object.assign({}, state, { sidebar: { ...action.data } });
    case "DUPLICATE_QUESTION":
      return {
        ...state,
        questions: {
          ...state.questions,
          [action.question.questionId]: {
            ...action.question
          }
        }
      };
    case "RESERVE_ID":
      const { [action.clientId]: question, ...others } = state.questions;

      question.questionId = action.id;

      return {
        ...state,
        questions: {
          ...others,
          [action.id]: question,
        }
      }
    case "ADD_PLACEHOLDER":
      return {
        ...state,
        questions: {
          ...state.questions,
          [action.clientId]: {
            ...action.values,
          }
        }
      };
    case "UPDATE_QUESTION":
      return {
        ...state,
        questions: {
          ...state.questions,
          [action.id]: {
            ...state.questions[action.id],
            ...action.values,
          }
        }
      };
    case "SAVE_QUESTIONS":
      const questions = {
        ...state.questions,
      };

      for (const { id, message } of action.errors) {
        questions[id] = {
          ...state.questions[id],
          isError: true,
          errorMessage: message,
        };
      }

      for (const { oldId, newId } of action.ids) {
        questions[newId] = {
          ...state.questions[oldId],
          questionId: newId,
        };

        delete questions[oldId];
      }

      return {
        ...state,
        questions: {
          ...questions
        }
      };
    case "ADD_OPTION":
      return {
        ...state,
        questions: {
          ...state.questions,
          [action.id]: {
            ...state.questions[action.id],
            options: state.questions[action.id].options.concat(action.option),
          }
        }
      };
    case "UPDATE_OPTION_VALUE":
      return {
        ...state,
        questions: {
          ...state.questions,
          [action.id]: {
            ...state.questions[action.id],
            options: state.questions[action.id].options.map((option, index) => {
              if (index !== action.index) {
                return option;
              }

              return {
                ...option,
                value: action.value,
              };
            }),
          }
        }
      };
    case "TOGGLE_OPTION_CORRECT":
      return {
        ...state,
        questions: {
          ...state.questions,
          [action.id]: {
            ...state.questions[action.id],
            options: state.questions[action.id].options.map((option, index) => {
              if (index !== action.index) {
                if (state.questions[action.id].type === 'multiple-choice') {
                  return {
                    ...option,
                    isCorrect: false,
                  };
                }

                return option;
              }

              return {
                ...option,
                isCorrect: !option.isCorrect,
              };
            }),
          }
        }
      };
    case "DELETE_OPTION":
      return {
        ...state,
        questions: {
          ...state.questions,
          [action.id]: {
            ...state.questions[action.id],
            options: state.questions[action.id].options.filter((option, index) => index !== action.index),
          }
        }
      };
    default:
      return state;
  }
}

const actions = {
  addPlaceholder(clientId, values) {
    return {
      type: "ADD_PLACEHOLDER",
      clientId,
      values,
    };
  },

  *getNextQuestionId(quizId, clientId) {
    const path = MPCS_Course_Data.api.reserveId + quizId;
    const id = yield actions.fetchFromAPI({ path });

    return {
      type: "RESERVE_ID",
      id,
      clientId,
    };
  },

  *maybeRemoveOrOrphanQuestion(id) {
    if (!id || id <= 0) return;

    const path = MPCS_Course_Data.api.releaseQuestion + id;
    yield actions.pushToApi(path, { id, });

    return {
      type: "RELEASE_QUESTION",
      id
    }
  },

  *refreshSidebarQuestions(quizId = 0, page = 1, search = "") {
    const args = {
      id: quizId,
      page: page,
      search: search
    }

    const path = getPathString(MPCS_Course_Data.api.question + "all", args);
    const data = yield actions.fetchFromAPI({ path }); //Returns questions, page, search, and pages

    return {
      type: "FETCH_ALL",
      data,
    };
  },

  *duplicateQuestion(questionId, quizId) {
    const question = yield actions.pushToApi(`mpcs/courses/question/${questionId}/duplicate/${quizId}`);

    return {
      type: "DUPLICATE_QUESTION",
      question,
    };
  },

  updateQuestion(id, values) {
    return {
      type: "UPDATE_QUESTION",
      id,
      values,
    };
  },

  addOption(id, option) {
    return {
      type: "ADD_OPTION",
      id,
      option,
    }
  },

  updateOptionValue(id, index, value) {
    return {
      type: "UPDATE_OPTION_VALUE",
      id,
      index,
      value,
    }
  },

  toggleOptionCorrect(id, index) {
    return {
      type: "TOGGLE_OPTION_CORRECT",
      id,
      index,
    }
  },

  deleteOption(id, index) {
    return {
      type: "DELETE_OPTION",
      id,
      index,
    }
  },

  *saveQuestions(quizId, questions, order) {
    const response = yield actions.pushToApi(
      `mpcs/courses/quiz/${quizId}/questions`,
      {
        questions,
        order,
      }
    );

    return {
      type: "SAVE_QUESTIONS",
      errors: response.errors,
      ids: response.ids,
    };
  },

  // API Utilities Control Actions
  fetchFromAPI(args) {
    return {
      type: "FETCH_FROM_API",
      args,
    };
  },

  pushToApi(path, data) {
    return {
      type: "PUSH_TO_API",
      path,
      data,
    };
  },

  deleteFromApi(path, data) {
    return {
      type: "DELETE_FROM_API",
      path,
      data,
    };
  },

}

const controls = {
  FETCH_FROM_API(action) {
    return apiFetch(action.args);
  },
  PUSH_TO_API(action) {
    return apiFetch({ path: action.path, data: action.data, method: "POST" });
  },
  DELETE_FROM_API(action) {
    return apiFetch({ path: action.path, data: action.data, method: "DELETE" });
  },
  FETCH_CURRENT_POST() {
    return wp.data.select("core/editor").getCurrentPostId();
  },
};

const selectors = {
  getQuestions(state) {
    return state.questions;
  },

  getQuestion(state, key) {
    return state.questions[key];
  },

  getAll(state) {
    return state.sidebar;
  }
}

const resolvers = {};

const getPathString = (path, args) => {
  return path + '?' + Object.keys(args).map(key => encodeURIComponent(key) + '=' + encodeURIComponent(args[key])).join('&');
};

export const StoreKey = "memberpress/course/question";
export const StoreConfig = {
  selectors,
  actions,
  reducer,
  resolvers,
  controls: { ...wp.data.controls, ...controls },
};
