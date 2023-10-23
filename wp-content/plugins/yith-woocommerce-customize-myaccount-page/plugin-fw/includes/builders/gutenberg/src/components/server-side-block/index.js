import React            from 'react';
import { Spinner }      from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';

import './style.scss';

function ServerSideBlockLoading() {
	return <div className="yith-plugin-fw-blocks__ssr-loading"><Spinner/></div>
}

function ServerSideBlock( { block, attributes } ) {
	return <ServerSideRender block={block} attributes={attributes} LoadingResponsePlaceholder={ServerSideBlockLoading}/>
}

export default ServerSideBlock;