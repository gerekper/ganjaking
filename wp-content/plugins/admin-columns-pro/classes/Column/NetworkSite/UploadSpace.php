<?php

namespace ACP\Column\NetworkSite;

use AC;

class UploadSpace extends AC\Column {

	public function __construct() {
		$this->set_type( 'column-msite_uploadspace' );
		$this->set_label( __( 'Storage Space', 'codepress-admin-columns' ) );
	}

	public function get_value( $id ) {
		switch_to_blog( $id );

		$used = get_space_used();
		$quota = get_space_allowed();

		restore_current_blog();

		$display_used = '&dash;';
		$display_quota = '';

		if ( $used ) {
			$display_used = sprintf( __( '%s MB', 'codepress-admin-columns' ), $this->trim_zeros( number_format_i18n( $used, 2 ) ) );
		}

		if ( $this->upload_restrictions() ) {
			$display_quota = sprintf( __( '%s MB', 'codepress-admin-columns' ), number_format_i18n( $quota ) );
		} else {
			$display_used .= ' / &#x221e;'; // infinitive symbol
		}

		$percentused = 0;

		if ( $this->upload_restrictions() ) {
			if ( $used > $quota ) {
				$percentused = 100;
			} else if ( $quota ) {
				$percentused = round( ( $used / $quota ) * 100 );
			}
		}

		$class = '';
		if ( $percentused >= 70 ) {
			$class = ' warning';
		}
		if ( $percentused >= 100 ) {
			$class = ' full';
		}

		if ( $percentused ) {
			$display_used .= ' (' . $percentused . '%)';
		}

		ob_start();
		?>
		<div class="ac-upload-space<?php echo $class; ?>">
			<div class="ac-upload-space-labels">
				<div class="inner">
					<span class="ac-upload-space-icon"></span>
					<span class="ac-upload-space-left"><?php echo $display_used; ?></span>
					<?php if ( $this->upload_restrictions() ) : ?>
						<span class="ac-upload-space-right"><?php echo $display_quota; ?></span>
					<?php endif; ?>
				</div>
			</div>

			<?php if ( $this->upload_restrictions() ) : ?>
				<div class="ac-upload-space-progress">
					<span class="ac-upload-space-progress-bar" style="width:<?php echo esc_attr( $percentused ); ?>%"></span>
				</div>
			<?php endif; ?>
		</div>
		<?php

		return ob_get_clean();
	}

	private function upload_restrictions() {
		return '1' !== get_site_option( 'upload_space_check_disabled' );
	}

	private function trim_zeros( $number ) {
		global $wp_locale;

		$decimal_separator = '.';

		if ( $wp_locale ) {
			$decimal_separator = $wp_locale->number_format['decimal_point'];
		}

		return preg_replace( '/' . preg_quote( $decimal_separator, '/' ) . '0++$/', '', $number );
	}

}