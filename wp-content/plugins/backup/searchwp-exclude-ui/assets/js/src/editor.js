import { registerPlugin } from '@wordpress/plugins';
import { default as SearchwpExclude } from './components/searchwp-exclude';

registerPlugin(
	'searchwp-exclude',
	{
		render: SearchwpExclude,
	}
);
