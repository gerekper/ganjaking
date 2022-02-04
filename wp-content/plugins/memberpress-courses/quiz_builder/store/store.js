const { registerStore } = wp.data;

import { StoreKey as QuestionKey, StoreConfig as Question } from "./question";
registerStore(QuestionKey, Question);
