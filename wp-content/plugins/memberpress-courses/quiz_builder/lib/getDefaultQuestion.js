export default type => {
  const question = {
    type,
    question: '',
    required: true,
    points: 1,
  };

  switch (type) {
    case 'multiple-choice':
    case 'multiple-answer':
      question.options = [{ value: '', isCorrect: true }];
      question.feedback = '';
      break;
    case 'true-false':
      question.answer = '0';
      question.feedback = '';
      break;
    case 'essay':
      question.min = 100;
      question.max = 1200;
      break;
  }

  return question;
};
