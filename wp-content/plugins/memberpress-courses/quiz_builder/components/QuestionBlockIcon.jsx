import icons from '../lib/icons';
import { camelCase } from 'lodash';

const { Icon } = wp.components;

function QuestionBlockIcon(props) {
  const { type, ...other } = props;
  const icon = icons[camelCase(type)] || null;

  return (
    icon ? <Icon icon={icon} { ...other } /> : null
  );
}

export default QuestionBlockIcon;
