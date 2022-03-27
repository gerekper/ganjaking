<?php

class GP_Preview_Submission extends GWPerk {

	public $version                   = GP_PREVIEW_SUBMISSION_VERSION;
	public $min_gravity_perks_version = '2.0.11';
	public $min_gravity_forms_version = '2.4';
	public $min_wp_version            = '3.7';

	private static $instance = null;

	public static function get_instance( $perk_file ) {
		if ( null == self::$instance ) {
			self::$instance = new self( $perk_file );
		}
		return self::$instance;
	}

	public function init() {

		$this->include_snippet();

		add_action( 'gform_editor_js', array( $this, 'add_merge_tag_support' ) );

	}

	function include_snippet() {

		if ( class_exists( 'GWPreviewConfirmation' ) ) {
			if ( is_user_logged_in() ) {
				add_action(
					'admin_notices',
					array(
						new GP_Late_Static_Binding(
							array(
								'message' => '<p>You are including the <a href="http://gravitywiz.com/better-pre-submission-confirmation/">GWPreviewConfirmation snippet</a>. The <b>GP Preview Submission</b> perk loads the latest version of this snippet. Please remove your copy of this snippet.</p>',
								'class'   => 'error',
							)
						),
						'GravityPerks_display_admin_message',
					)
				);
			}
		} else {
			require_once( 'includes/gw-gravity-forms-preview-confirmation.php' );
		}

	}


	/**
	 * Adds field merge tags to the merge tag drop downs.
	 */
	function add_merge_tag_support() {
		?>

		<script type="text/javascript">

			window.gppsDoingMergeTags = false;

			gform.addFilter( 'gform_merge_tags', 'gppsMergeTags' );

			function gppsMergeTags( mergeTags, elementId, hideAllFields, excludeFieldTypes, isPrepop, option ) {

				if ( gppsDoingMergeTags || ( elementId != 'field_content' && elementId != 'field_default_value' ) ) {
					return mergeTags;
				}

				if( elementId == 'field_content' ) {
					hideAllFields = false;
				}

				gppsDoingMergeTags = true;
				var allTags = gfMergeTags.getMergeTags( Copy( form.fields ), elementId, hideAllFields, excludeFieldTypes, false, option );
				gppsDoingMergeTags = false;

				for( var key in allTags ) {

					if( ! allTags.hasOwnProperty( key ) || jQuery.inArray( key, [ 'required', 'optional', 'pricing' ] ) == -1 || allTags[key].tags.length <= 0 )
						continue;

					var tags = allTags[key].tags,
						newTags = [];

					for( var i = 0; i < tags.length; i++ ) {

						var fieldId = gppsGetFieldIdByTag( tags[i].tag );
						if( ! fieldId )
							continue;

						var field = GetFieldById( fieldId ),
							selField = GetSelectedField(),
							fieldPageNumber = field.pageNumber,
							selFieldPageNumber = selField.pageNumber;

						// leave the field if it is on a previous page
						if( selFieldPageNumber > fieldPageNumber )
							newTags.push( tags[i] );

					}

					allTags[key].tags = newTags;

				}

				return allTags;
			}

			function gppsGetFieldIdByTag( tag ) {

				var tag = jQuery.trim( Copy( tag ) );
				tag = tag.substring( 1, tag.length - 1 );

				var bits = tag.split( ':' );
				var fieldId = parseInt( bits[bits.length - 1] );

				return isNaN( fieldId ) || fieldId === 0 ? false : fieldId;
			}

		</script>

		<?php
	}

	function has_any_merge_tag( $string ) {
		// Negative lookbehind to prevent replacement of GP Populate Anything Live Merge Tags
		return preg_match_all( '/(?<!@){.+}/', $string, $matches, PREG_SET_ORDER );
	}

	function has_gppa_parent_merge_tag( $text ) {
		return is_callable( 'gpnf_parent_merge_tag' ) && preg_match( '/\{Parent:(.*?)\}/i', $text );
	}

}

function gp_preview_submission() {
	return GP_Preview_Submission::get_instance( null );
}
