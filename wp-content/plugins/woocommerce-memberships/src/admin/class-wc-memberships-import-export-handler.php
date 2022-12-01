<?php
/**
 * WooCommerce Memberships
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Memberships to newer
 * versions in the future. If you wish to customize WooCommerce Memberships for your
 * needs please refer to https://docs.woocommerce.com/document/woocommerce-memberships/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2014-2022, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

use SkyVerge\WooCommerce\PluginFramework\v5_10_13 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * CSV Import / Export User Memberships admin pages handler.
 *
 * The original concept of this class was to handle import and export individual handlers.
 * With the introduction of batch processing for those tasks, it now handles exclusively the import/export admin screens
 *
 * TODO when updating codebase to PHP 5.3+ and using namespaces, update the name of this class to reflect its new adjusted role {FN 2018-03-06}
 *
 * @since 1.6.0
 */
class WC_Memberships_Admin_Import_Export_Handler {


	/** @var string the location of this page */
	private $url;

	/** @var array sections of the Import / Export admin page */
	private $sections;

	/** @var string the current section name for the Import / Export admin page */
	private $current_section;


	/**
	 * Handler constructor.
	 *
	 * @since 1.6.0
	 */
	public function __construct() {

		$this->url = admin_url( 'admin.php?page=wc_memberships_import_export' );

		/**
		 * Filter the Memberships Import / Export admin sections.
		 *
		 * @since 1.6.0
		 *
		 * @param $sections array associative array with section ids and labels
		 */
		$this->sections = apply_filters( 'wc_memberships_admin_import_export_sections', array(
			'csv_export_user_memberships' => __( 'Export to CSV', 'woocommerce-memberships' ),
			'csv_import_user_memberships' => __( 'Import from CSV', 'woocommerce-memberships' ),
		) );

		// auto determine the section based on current page
		$this->current_section = $this->get_admin_page_current_section();

		// output page content
		add_action( 'wc_memberships_render_import_export_page', array( $this, 'render_admin_page' ) );

		// makes sure that the Memberships menu item is set to currently active
		add_filter( 'parent_file', array( $this, 'set_current_admin_menu_item' ) );

		// set the admin page title
		add_filter( 'admin_title', array( $this, 'set_admin_page_title' ), 10 );

		// add csv file input field handler
		add_action( 'woocommerce_admin_field_wc-memberships-import-file', array( $this, 'render_file_upload_field' ) );
		// add date range input field handler
		add_action( 'woocommerce_admin_field_wc-memberships-date-range',  array( $this, 'render_date_range_field' ) );

		// display a message on import tab
		if ( 'csv_import_user_memberships' === $this->current_section ) {

			$docs_button = '<p><a class="button" href="https://docs.woocommerce.com/document/woocommerce-memberships-import-and-export/">' . esc_html__( 'See Documentation', 'woocommerce-memberships' ) . '</a>';

			wc_memberships()->get_admin_notice_handler()->add_admin_notice(
				'<p>' . __( '<strong>Members CSV Import</strong> - Importing members will create or update automatically User Memberships in bulk. Importing members <strong>does not</strong> create any associated billing, subscription or order records.', 'woocommerce-memberships' ) . '</p>' . $docs_button,
				'wc-memberships-csv-import-user-memberships-docs'
			);
		}
	}


	/**
	 * Returns the import / export admin screen URL.
	 *
	 * @since 1.6.0
	 *
	 * @param string $section optional, defaults to current section
	 * @return string URL
	 */
	private function get_admin_page_url( $section = '' ) {

		$section = empty( $section ) ? $this->get_admin_page_current_section() : $section;

		return add_query_arg( array( 'section' => $section ), $this->url );
	}


	/**
	 * Returns the admin page current section.
	 *
	 * @since 1.6.0
	 *
	 * @return string
	 */
	private function get_admin_page_current_section() {

		$current_section = '';

		if ( ! empty( $this->sections ) ) {

			$sections        = array_keys( $this->sections );
			$current_section = current( $sections );

			if ( isset( $_GET['section'] ) && in_array( $_GET['section'], $sections, true ) ) {

				$current_section = $_GET['section'];
			}
		}

		return $current_section;
	}


	/**
	 * Sets the Memberships admin menu item as active while viewing the Import / Export tab page.
	 *
	 * @internal
	 * @see \SkyVerge\WooCommerce\Memberships\Admin\Profile_Fields::set_current_admin_menu_item()
	 *
	 * @since 1.6.2
	 *
	 * @param string $parent_file
	 * @return string
	 */
	public function set_current_admin_menu_item( $parent_file ) {
		global $menu, $submenu_file;

		if ( isset( $_GET['page'] ) && 'wc_memberships_import_export' === $_GET['page'] ) {

			$submenu_file = 'edit.php?post_type=wc_user_membership';

			if ( ! empty( $menu ) ) {

				foreach ( $menu as $key => $value ) {

					if ( isset( $value[2], $menu[ $key ][4] ) && 'woocommerce' === $value[2] ) {
						$menu[ $key ][4] .= ' wp-has-current-submenu wp-menu-open';
					}
				}
			}
		}

		return $parent_file;
	}


	/**
	 * Sets the admin page title.
	 *
	 * @internal
	 * @see Profile_Fields::set_admin_page_title()
	 *
	 * @since 1.6.2
	 *
	 * @param string $admin_title the page title, with extra context added
	 * @return string
	 */
	public function set_admin_page_title( $admin_title ) {

		if ( isset( $_GET['page'] ) && 'wc_memberships_import_export' === $_GET['page'] ) {

			switch ( $this->current_section ) {

				case 'csv_export_user_memberships' :
					$admin_title = __( 'Export Members', 'woocommerce-memberships' ) . ' ' . $admin_title;
				break;

				case 'csv_import_user_memberships':
					$admin_title = __( 'Import Members', 'woocommerce-memberships' ) . ' ' . $admin_title;
				break;
			}
		}

		return $admin_title;
	}


	/**
	 * Renders the page within Memberships admin page tabs.
	 *
	 * @internal
	 *
	 * @since 1.6.0
	 */
	public function render_admin_page() {

		$current_section = $this->current_section;
		$section_class   = ! empty( $current_section ) ? sanitize_html_class( 'woocommerce-memberships-' . $current_section ) : '';

		?>
		<div class="wrap woocommerce woocommerce-memberships woocommerce-memberships-import-export <?php echo $section_class; ?>">

			<?php $this->render_admin_page_sections_navigation_links( $current_section ); ?>

			<br class="clear" />

			<?php

			/**
			 * Renders the current section in the Import / Export admin page (legacy hook).
			 *
			 * @since 1.6.0
			 *
			 * @param string $current_section the section that should be displayed
			 */
			do_action( 'wc_memberships_render_import_export_page_section', $current_section );

			?>
			<div>
				<?php

				if ( 'csv_import_user_memberships' === $this->current_section ) {
					woocommerce_admin_fields( $this->get_import_fields() );
				} elseif ( 'csv_export_user_memberships' === $this->current_section ) {
					woocommerce_admin_fields( $this->get_export_fields() );
				}

				?>
				<p class="submit">
					<?php if ( 'csv_import_user_memberships' === $this->current_section ) : ?>
						<button
							id="wc-memberships-import-export-trigger"
							data-action-id="import"
							data-action-title="<?php esc_html_e( 'Import User Memberships', 'woocommerce-memberships' ); ?>"
							class="button button-primary"><?php
							esc_html_e( 'Upload File and Import', 'woocommerce-memberships' ); ?></button>
						<span class="spinner" style="float:none;margin-top:-1px;"></span>
					<?php elseif ( 'csv_export_user_memberships' === $this->current_section ) : ?>
						<button
							id="wc-memberships-import-export-trigger"
							data-action-id="export"
							data-action-title="<?php esc_html_e( 'Export User Memberships', 'woocommerce-memberships' ); ?>"
							class="button button-primary" ><?php
							esc_html_e( 'Export', 'woocommerce-memberships' ); ?></button>
					<?php endif; ?>
				</p>

			</div>
		</div>
		<?php
	}


	/**
	 * Generates sections navigation items.
	 *
	 * @since 1.6.0
	 *
	 * @param string $current_section optional, if empty will determine the current section
	 */
	private function render_admin_page_sections_navigation_links( $current_section = '' ) {

		$sections = $this->sections;

		if ( ! empty ( $sections ) ) {

			if ( '' === $current_section ) {
				$current_section = $this->get_admin_page_current_section();
			}

			$links = array();

			foreach ( $sections as $id => $label ) {

				$url   = add_query_arg( 'section', $id, $this->get_admin_page_url() );
				$class = $id === $current_section ? 'class="current"' : '';

				$links[] = '<li><a href="' . esc_url( $url ) . '" ' . $class . '>' . esc_html( $label ) . '</a></li>';
			}

			echo '<ul class="subsubsub">' . implode( ' | ', $links ) . '</ul>';
		}
	}


	/**
	 * Returns import options input fields.
	 *
	 * @since 1.10.0
	 *
	 * @return array associative array of fields data
	 */
	private function get_import_fields() {

		$documentation_url = 'https://docs.woocommerce.com/document/woocommerce-memberships-import-and-export/';
		$max_upload_size   = size_format( wc_let_to_num( ini_get( 'post_max_size' ) ) );

		if ( ! $site_timezone = wc_timezone_string() ) {
			$site_timezone = 'UTC';
		}

		$options = array(

			// section start
			array(
				'title' => __( 'Import Members', 'woocommerce-memberships' ),
				/* translators: Placeholders: %1$s - opening <a> link HTML tag, $2$s - closing </a> link HTML tag */
				'desc'  => sprintf( __( 'Your CSV file must be formatted with the correct column names and cell data. Please %1$ssee the documentation%2$s for more information and a sample CSV file.', 'woocommerce-memberships' ),
					'<a href="' . esc_url( $documentation_url ) . '">',
					'</a>'
				),
				'type'  => 'title',
			),

			// csv file to upload
			array(
				'id'       => 'wc_memberships_members_csv_import_file',
				'title'    => __( 'Choose a file from your computer', 'woocommerce-memberships' ),
				/* translators: Placeholder: %s - maximum uploadable file size (e.g. 8M, 20M, 100M...)  */
				'desc_tip' => sprintf( __( 'Acceptable file types: CSV or tab-delimited text files. Maximum file size: %s', 'woocommerce-memberships' ),
					empty( $max_upload_size ) ? '<em>' . __( 'Undetermined', 'woocommerce-memberships' ) . '</em>' : $max_upload_size
				),
				'type'     => 'wc-memberships-import-file',
			),

			// update existing user memberships?
			array(
				'id'            => 'wc_memberships_members_csv_import_merge_existing_user_memberships',
				'title'         => __( 'Import Options', 'woocommerce-memberships' ),
				'desc'          => __( 'Update existing records if a matching user membership is found', 'woocommerce-memberships' ),
				'desc_tip'      => __( 'User memberships can be found either by user membership ID, or by a combination of user ID, login name or email address and a membership plan ID or slug.', 'woocommerce-memberships' ),
				'default'       => 'yes',
				'type'          => 'checkbox',
				'checkboxgroup' => 'start',
			),

			// allow transferring memberships in case of user conflict?
			array(
				'id'            => 'wc_memberships_members_csv_import_allow_memberships_transfer',
				'desc'          => __( 'Allow membership transfer between users if the imported user differs from the existing user for the membership (skips conflicting rows when disabled)', 'woocommerce-memberships' ),
				'desc_tip'      => __( 'Will transfer a matched user membership if the user indicated in the same row differs from the one currently associated with the existing user membership.', 'woocommerce-memberships' ),
				'default'       => 'no',
				'type'          => 'checkbox',
				'checkboxgroup' => '',
			),

			// create new memberships?
			array(
				'id'            => 'wc_memberships_members_csv_import_create_new_user_memberships',
				'desc'          => __( 'Create new user memberships if a matching user membership is not found (skips rows when disabled)', 'woocommerce-memberships' ),
				'desc_tip'      => __( 'Requires matching a valid plan, by ID or slug, and a user, by ID, email address or login name.', 'woocommerce-memberships' ),
				'default'       => 'yes',
				'type'          => 'checkbox',
				'checkboxgroup' => '',
			),

			// create new users?
			array(
				'id'            => 'wc_memberships_members_csv_import_create_new_users',
				'desc'          => __( 'Create a new user if no matching user is found (skips rows when disabled)', 'woocommerce-memberships' ),
				'desc_tip'      => __( 'Users can be found either by ID, email address or login name provided.', 'woocommerce-memberships' ),
				'default'       => 'no',
				'type'          => 'checkbox',
				'checkboxgroup' => '',
			),

			// notify new users?
			array(
				'id'            => 'wc_memberships_members_csv_import_new_user_email_notification',
				'desc'          => __( 'Send new account notification emails when creating new users during an import', 'woocommerce-memberships' ),
				'default'       => 'no',
				'type'          => 'checkbox',
				'checkboxgroup' => 'end',
			),

			// default start date when unspecified
			array(
				'id'          => 'wc_memberships_members_csv_import_default_start_date',
				'title'       => __( 'Default Start Date', 'woocommerce-memberships' ),
				'desc'        => __( "When creating new memberships, you can specify a default date to set a membership start date if not defined in the import data. Leave this blank to use today's date otherwise.", 'woocommerce-memberships' ),
				'default'     => '',
				'css'         => 'max-width: 120px;',
				'placeholder' => date( 'Y-m-d' ),
				'type'        => 'text',
				'class'       => 'js-user-membership-date',
			),

			// timezone
			array(
				'id'       => 'wc_memberships_members_csv_import_timezone',
				'title'    => __( 'Dates timezone', 'woocommerce-memberships' ),
				'type'     => 'select',
				'class'    => 'wc-enhanced-select',
				'desc_tip' => __( 'Choose the timezone the dates in the import are from.', 'woocommerce-memberships' ),
				'options'  => array(
					$site_timezone => __( 'Site timezone', 'woocommerce-memberships' ),
					'UTC'           => __( 'UTC', 'woocommerce-memberships' ),
				),
			),

			// entries are separated by comma or tab? (filterable)
			array(
				'id'       => 'wc_memberships_members_csv_import_fields_delimiter',
				'title'    => __( 'Fields are separated by', 'woocommerce-memberships' ),
				'type'     => 'select',
				'class'    => 'wc-enhanced-select',
				'desc_tip' => __( 'Change the delimiter based on your input file format.', 'woocommerce-memberships' ),
				'options'  => $this->get_csv_delimiter_options( 'import' ),
			),

			// end of section
			array( 'type' => 'sectionend' ),

		);

		/**
		 * Filters the CSV Import User Memberships options.
		 *
		 * @since 1.6.0
		 *
		 * @param array $options associative array
		 */
		return (array) apply_filters( 'wc_memberships_csv_import_user_memberships_options', $options );
	}


	/**
	 * Returns export options input fields.
	 *
	 * @since 1.10.0
	 *
	 * @return array associative array of fields data
	 */
	private function get_export_fields() {

		$options = [

			// section start
			[
				'title' => __( 'Export Members', 'woocommerce-memberships' ),
				'type'  => 'title',
			],

			// select plans to export from
			[
				'id'                => 'wc_memberships_members_csv_export_plan',
				'title'             => __( 'Plan', 'woocommerce-memberships' ),
				'desc_tip'          => __( 'Choose which plan(s) to export members from. Leave blank to export members from every plan.', 'woocommerce-memberships' ),
				'type'              => 'multiselect',
				'options'           => $this->get_plans(),
				'default'           => '',
				'class'             => 'wc-enhanced-select',
				'css'               => 'min-width: 250px;',
				'custom_attributes' => [
					'data-placeholder' => __( 'Leave blank to export members of any plan.', 'woocommerce-memberships' ),
				],
			],

			// select membership statuses to export
			[
				'id'                => 'wc_memberships_members_csv_export_status',
				'title'             => __( 'Status', 'woocommerce-memberships' ),
				'desc_tip'          => __( 'Choose to export user memberships with specific status(es) only. Leave blank to export user memberships of any status.', 'woocommerce-memberships' ),
				'type'              => 'multiselect',
				'options'           => $this->get_statuses(),
				'default'           => '',
				'class'             => 'wc-enhanced-select',
				'css'               => 'min-width: 250px;',
				'custom_attributes' => [
					'data-placeholder' => __( 'Leave blank to export members with any status.', 'woocommerce-memberships' ),
				],
			],

			// set memberships minimum start date
			[
				'id'    => 'wc_memberships_members_csv_export_start_date',
				'title' => __( 'Start Date', 'woocommerce-memberships' ),
				/* translators: Placeholder: %s - date format */
				'desc'  => sprintf(
					__( 'Start date of memberships to include in the exported file, in the format %s.', 'woocommerce-memberships' ) . '<br>' .
					__( 'You can optionally specify a date range, or leave one of the fields blank for open-ended ranges.', 'woocommerce-memberships' ),
					'<code>YYYY-MM-DD</code>'
				),
				'css'   => 'max-width: 120px;',
				'type'  => 'wc-memberships-date-range',
				'class' => 'js-user-membership-date',
			],

			// set memberships maximum end date
			[
				'id'    => 'wc_memberships_members_csv_export_end_date',
				'title' => __( 'End Date', 'woocommerce-memberships' ),
				/* translators: Placeholder: %s - date format */
				'desc'  => sprintf(
					__( 'Expiration date of memberships to include in the exported file, in the format %s.', 'woocommerce-memberships' ) . '<br>' .
					__( 'You can optionally specify a date range, or leave one of the fields blank for open-ended ranges.', 'woocommerce-memberships' ),
					'<code>YYYY-MM-DD</code>'
				),
				'css'   => 'max-width: 120px;',
				'type'  => 'wc-memberships-date-range',
				'class' => 'js-user-membership-date',
			],

			// export profile fields
			[
				'id'       => 'wc_memberships_members_csv_export_profile_fields',
				'name'     => __( 'Profile fields', 'woocommerce-memberships' ),
				'type'     => 'checkbox',
				'desc'     => __( 'Include profile fields', 'woocommerce-memberships' ),
				'desc_tip' => __( 'Include member profile field data in member export.', 'woocommerce-memberships' ),
				'default'  => 'no',
			],

			// export all post meta
			[
				'id'       => 'wc_memberships_members_csv_export_meta_data',
				'name'     => __( 'Meta data', 'woocommerce-memberships' ),
				'type'     => 'checkbox',
				'desc'     => __( 'Include additional meta data', 'woocommerce-memberships' ),
				'desc_tip' => __( 'Add an extra column to the CSV file with all post meta of each membership in JSON format.', 'woocommerce-memberships' ),
				'default'  => 'no'
			],

			// entries are going to be separated by comma or tab? (filterable)
			[
				'id'       => 'wc_memberships_members_csv_export_fields_delimiter',
				'name'     => __( 'Separate fields by', 'woocommerce-memberships' ),
				'type'     => 'select',
				'class'    => 'wc-enhanced-select',
				'desc_tip' => __( 'Change the delimiter based on your desired output format.', 'woocommerce-memberships' ),
				'options'  => $this->get_csv_delimiter_options( 'export' ),
			],

			// section end
			[
				'type' => 'sectionend'
			],

		];

		/**
		 * Filters CSV Export User Memberships options.
		 *
		 * @since 1.6.0
		 *
		 * @para array $options associative array
		 */
		return (array) apply_filters( 'wc_memberships_csv_export_user_memberships_options', $options );
	}


	/**
	 * Gets the options for the CSV delimiter field.
	 *
	 * @since 1.13.1
	 *
	 * @param string $action import or export
	 * @return array associative array of identifiers and labels
	 */
	private function get_csv_delimiter_options( $action ) {

		/**
		 * Filters admin options for the CSV delimiter used in import and export jobs.
		 *
		 * @since 1.13.1
		 *
		 * @param array $options associative array of identifiers and labels
		 */
		return (array) apply_filters( "wc_memberships_csv_{$action}_delimiter_options", array(
			'comma' => __( 'Comma', 'woocommerce-memberships' ),
			'tab'   => __( 'Tab space', 'woocommerce-memberships' ),
		) );
	}


	/**
	 * Returns Membership Plans for exporting.
	 *
	 * @since 1.10.0
	 *
	 * @return array associative array of plan IDs and names
	 */
	private function get_plans() {

		$plan_objects = wc_memberships_get_membership_plans();
		$plans        = array();

		if ( ! empty( $plan_objects ) ) {

			foreach ( $plan_objects as $plan ) {

				$plans[ $plan->get_id() ] = $plan->get_name();
			}
		}

		return $plans;
	}


	/**
	 * Returns User Membership statuses for exporting.
	 *
	 * @since 1.10.0
	 *
	 * @return array associative array of user membership statuses and their labels
	 */
	private function get_statuses() {

		$statuses_array = wc_memberships_get_user_membership_statuses();
		$statuses       = array();

		if ( ! empty( $statuses_array ) ) {

			foreach ( $statuses_array as $id => $status ) {

				if ( isset( $status['label'] ) ) {

					$statuses[ $id ] = $status['label'];
				}
			}
		}

		return $statuses;
	}


	/**
	 * Outputs a file input field.
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 *
	 * @param array $field field settings
	 */
	public function render_file_upload_field( $field ) {

		$field = wp_parse_args( $field, array(
			'id'       => '',
			'title'    => __( 'Choose a file from your computer', 'woocommerce-memberships' ),
			'desc'     => '',
			'desc_tip' => '',
			'type'     => 'wc-memberships-import-file',
			'class'    => '',
			'css'      => '',
			'value'    => '',
		) );

		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html( $field['title'] ); ?></label>
			</th>
			<td class="forminp forminp-<?php echo sanitize_html_class( $field['type'] ) ?>">
				<input
					type="hidden"
					name="MAX_FILE_SIZE"
					value="<?php echo wp_max_upload_size(); ?>"
				/>
				<input
					type="file"
					accept=".csv, .txt, text/csv, text/plain, text/html, text/anytext, text/comma-separated-values, application/csv"
					name="<?php echo esc_attr( $field['id'] ); ?>"
					id="<?php echo esc_attr( $field['id'] ); ?>"
					class="<?php echo esc_attr( $field['class'] ); ?>"
					style="<?php echo esc_attr( $field['css'] ); ?>"
					value="<?php echo esc_attr( $field['value'] ); ?>"
				/><br /><span class="description"><?php echo $field['desc_tip']; ?></span>
			</td>
		</tr>
		<?php
	}


	/**
	 * Outputs a date range input field.
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 *
	 * @param array $field field settings
	 */
	public function render_date_range_field( $field ) {

		$field = wp_parse_args( $field, array(
			'id'         => '',
			'title'      => __( 'Date Range', 'woocommerce-memberships' ),
			'desc'       => '',
			'desc_tip'   => '',
			'type'       => 'wc-memberships-date-range',
			'class'      => '',
			'css'        => '',
		) );

		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for=""><?php echo esc_html( $field['title'] ); ?></label>
			</th>
			<td class="forminp forminp-<?php echo sanitize_html_class( $field['type'] ) ?>">
				<span class="label">
					<?php esc_html_e( 'From:', 'woocommerce-memberships' ); ?>
					<input
						name="<?php echo esc_attr( $field['id'] ) . '_from'; ?>"
						id="<?php echo esc_attr( $field['id'] ) . '_from'; ?>"
						type="text"
						style="<?php echo esc_attr( $field['css'] ); ?>"
						value=""
						class="<?php echo esc_attr( $field['class'] ); ?>"
					/>
				</span>
				&nbsp;&nbsp;
				<span class="label">
					<?php esc_html_e( 'To:', 'woocommerce-memberships' ); ?>
					<input
						name="<?php echo esc_attr( $field['id'] . '_to' ); ?>"
						id="<?php echo esc_attr( $field['id'] . '_to' ); ?>"
						type="text"
						style="<?php echo esc_attr( $field['css'] ); ?>"
						value=""
						class="<?php echo esc_attr( $field['class'] ); ?>"
					/>
				</span>
				<br /><span class="description"><?php echo $field['desc']; ?></span>
			</td>
		</tr>
		<?php
	}


}
