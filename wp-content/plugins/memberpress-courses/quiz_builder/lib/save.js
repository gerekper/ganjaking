import { StoreKey } from '../store/question';

const { __ } = wp.i18n;
const { dispatch, select, subscribe } = wp.data;
const { isSavingPost, isAutosavingPost, getCurrentPostId } = select('core/editor');
const { getBlocks } = select('core/block-editor');
const { createNotice } = dispatch('core/notices');
const { updateBlockAttributes } = dispatch('core/block-editor');
const { getQuestions } = select(StoreKey);
const { saveQuestions } = dispatch(StoreKey);

let wasSavingPost = isSavingPost();
let wasAutosavingPost = isAutosavingPost();

const unsubscribe = subscribe(() => {
  const shouldTriggerSave = wasSavingPost && !wasAutosavingPost && !isSavingPost();

  wasSavingPost = isSavingPost();
  wasAutosavingPost = isAutosavingPost();

  if (shouldTriggerSave) {
    const order = getBlocks()
      .filter(block => block.name.indexOf('memberpress-courses') === 0)
      .map(block => block.attributes.questionId);

    saveQuestions(getCurrentPostId(), getQuestions(), order).then(action => {
      if (action.ids && action.ids.length) {
        getBlocks()
          .filter(block => block.name.indexOf('memberpress-courses') === 0)
          .forEach(block => {
            const { clientId, attributes: { questionId } } = block;

            if (questionId) {
              action.ids.forEach(({ oldId, newId }) => {
                if (questionId === oldId) {
                  updateBlockAttributes(clientId, { questionId: newId });
                }
              });
            }
          });
      }
    }).catch(error => {
        createNotice(
          'error',
          __('Error saving quiz questions: %s', 'memberpress-courses').replace('%s', error && error.message || 'Request error'),
          {
            isDismissible: true
          }
        );
    });
  }
})

export default unsubscribe;
