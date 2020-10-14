import { withSelect, withDispatch } from '@wordpress/data';
import { PluginPostStatusInfo } from '@wordpress/edit-post';
import { CheckboxControl } from '@wordpress/components';
import { compose } from '@wordpress/compose';
import { Component } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { registerPlugin } from '@wordpress/plugins';

export class SearchwpExclude extends Component {

	render() {
		// Nested object destructuring.
		const {
			meta: {
				_searchwp_excluded: SearchwpExcluded,
			} = {},
			updateMeta,
		} = this.props;

		return (
			<PluginPostStatusInfo>
				<CheckboxControl
					label={ __( 'Exclude from search' ) }
					checked={ SearchwpExcluded }
					onChange={ ( SearchwpExcluded ) => {
						updateMeta( { _searchwp_excluded: SearchwpExcluded || false } );
					} }
				/>
			</PluginPostStatusInfo>
		);
	}
}


export default compose( [
	withSelect( ( select ) => {
		const { getEditedPostAttribute } = select( 'core/editor' );

		return {
			meta: getEditedPostAttribute( 'meta' ),
		};
	} ),
	withDispatch( ( dispatch, { meta } ) => {
		const { editPost } = dispatch( 'core/editor' );

		return {
			updateMeta( newMeta ) {
				newMeta._searchwp_excluded = newMeta._searchwp_excluded ? '1' : '';
				editPost( { meta: { ...meta, ...newMeta } } ); // Important: Old and new meta need to be merged in a non-mutating way!
			},
		};
	} )
] )( SearchwpExclude );
