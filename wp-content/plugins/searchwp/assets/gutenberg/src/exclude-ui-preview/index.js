import { PluginPostStatusInfo } from '@wordpress/edit-post';
import { CheckboxControl, ExternalLink } from '@wordpress/components';
import { Component } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';
import { registerPlugin } from '@wordpress/plugins';

class SearchwpExcludeUiPreview extends Component {

	render() {
		const postTypeLabel = wp.data.select('core/editor').getPostTypeLabel()
		const extensionsUrl = '/wp-admin/admin.php?page=searchwp-extensions';


		return (
			<PluginPostStatusInfo>
				<div
					className="searchwp-exclude-preview"
					style={{
						position: "relative",
						padding: "10px",
						"border-radius": "2px",
						backgroundColor: "#f0f0f0",
						color: "#7f7f7f",
					}}>
					<span
						style={{
							display: "block",
							position: "absolute",
							top: "0",
							right: "0"
						}}
						onClick={this.dismissPreview}>
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false"><path d="M12 13.06l3.712 3.713 1.061-1.06L13.061 12l3.712-3.712-1.06-1.06L12 10.938 8.288 7.227l-1.061 1.06L10.939 12l-3.712 3.712 1.06 1.061L12 13.061z"></path></svg>
					</span>
					<CheckboxControl
						label={ __( 'Exclude from SearchWP' ) }
						checked= {false}
						disabled= {true}
						onChange={ () => {
							return;
						} }
						style={{border: "1px solid rgba(0,0,0,.5)"}}
					/>
					<span style={{
						"display":"block",
						"margin-top":"10px"
					}}>
						<span>{ sprintf(
							__( 'Activate the SearchWP Exclude UI extension and exclude any %s from your search results.' ),
							postTypeLabel
						) } </span>
						<br />
						<ExternalLink href="https://searchwp.com/extensions/exclude-ui/">
							{ __( 'View Docs' ) }
						</ExternalLink>
						<ExternalLink
							href={extensionsUrl}
							style={{
								"display":"inline-block",
								"margin-left":"10px"
						}}>
							{ __( 'Activate' ) }
						</ExternalLink>
					</span>
				</div>
			</PluginPostStatusInfo>
		);
	}

	dismissPreview ( e ) {
		e.preventDefault();

		jQuery.ajax({
			url: ajaxurl,
			type: 'POST',
			data: {
				action: 'searchwp_exclude_ui_preview_dismissed'
			},
			success: function (response) {
				if (response.success) {
					// get the element ancestor with class components-panel__row
					e.target.closest('.components-panel__row').style.display = 'none';
				}
			}
		});
	};
}

registerPlugin(
	'searchwp-exclude-ui-preview',
	{
		render: SearchwpExcludeUiPreview,
	}
);