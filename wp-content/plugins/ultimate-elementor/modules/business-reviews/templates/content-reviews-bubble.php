<?php
/**
 * UAEL Reviews - Template.
 *
 * @package UAEL
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
$photolink  = ( null !== $review['profile_photo_url'] ) ? $review['profile_photo_url'] : ( UAEL_URL . 'assets/img/user.png' );
$google_svg = '<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 width="18px" height="18px" viewBox="0 0 100 100" enable-background="new 0 0 100 100" xml:space="preserve">
<g>
	<path id="XMLID_5_" fill="#FFFFFF" d="M34.963,3.686C23.018,7.777,12.846,16.712,7.206,28.002
		c-1.963,3.891-3.397,8.045-4.258,12.315C0.78,50.961,2.289,62.307,7.2,72.002c3.19,6.328,7.762,11.951,13.311,16.361
		c5.236,4.175,11.336,7.256,17.806,8.979c8.163,2.188,16.854,2.14,25.068,0.268c7.426-1.709,14.452-5.256,20.061-10.436
		c5.929-5.449,10.158-12.63,12.399-20.342c2.441-8.415,2.779-17.397,1.249-26.011c-15.373-0.009-30.744-0.004-46.113-0.002
		c0.003,6.375-0.007,12.749,0.006,19.122c8.9-0.003,17.802-0.006,26.703,0c-1.034,6.107-4.665,11.696-9.813,15.135
		c-3.236,2.176-6.954,3.587-10.787,4.26c-3.861,0.661-7.846,0.746-11.696-0.035c-3.914-0.781-7.649-2.412-10.909-4.711
		c-5.212-3.662-9.189-9.018-11.23-15.048c-2.088-6.132-2.103-12.954,0.009-19.08c1.466-4.316,3.907-8.305,7.112-11.551
		c3.955-4.048,9.095-6.941,14.633-8.128c4.742-1.013,9.745-0.819,14.389,0.586c3.947,1.198,7.584,3.359,10.563,6.206
		c3.012-2.996,6.011-6.008,9.014-9.008c1.579-1.615,3.236-3.161,4.763-4.819C79.172,9.52,73.819,6.123,67.97,3.976
		C57.438,0.1,45.564,0.018,34.963,3.686z"/>
	<g>
		<path id="XMLID_4_" fill="#EA4335" d="M34.963,3.686C45.564,0.018,57.438,0.1,67.97,3.976c5.85,2.147,11.202,5.544,15.769,9.771
			c-1.526,1.659-3.184,3.205-4.763,4.819c-3.003,3-6.002,6.012-9.014,9.008c-2.979-2.846-6.616-5.008-10.563-6.206
			c-4.645-1.405-9.647-1.599-14.389-0.586c-5.539,1.187-10.679,4.08-14.633,8.128c-3.206,3.246-5.646,7.235-7.112,11.551
			c-5.353-4.152-10.703-8.307-16.058-12.458C12.846,16.712,23.018,7.777,34.963,3.686z"/>
	</g>
	<g>
		<path id="XMLID_3_" fill="#FBBC05" d="M2.947,40.317c0.861-4.27,2.295-8.424,4.258-12.315c5.355,4.151,10.706,8.306,16.058,12.458
			c-2.112,6.126-2.097,12.948-0.009,19.08C17.903,63.695,12.557,67.856,7.2,72.002C2.289,62.307,0.78,50.961,2.947,40.317z"/>
	</g>
	<g>
		<path id="XMLID_2_" fill="#4285F4" d="M50.981,40.818c15.369-0.002,30.74-0.006,46.113,0.002
			c1.53,8.614,1.192,17.596-1.249,26.011c-2.241,7.712-6.471,14.893-12.399,20.342c-5.18-4.039-10.386-8.057-15.568-12.099
			c5.147-3.438,8.778-9.027,9.813-15.135c-8.9-0.006-17.803-0.003-26.703,0C50.974,53.567,50.984,47.194,50.981,40.818z"/>
	</g>
	<g>
		<path id="XMLID_1_" fill="#34A853" d="M7.2,72.002c5.356-4.146,10.703-8.307,16.055-12.461c2.041,6.03,6.018,11.386,11.23,15.048
			c3.26,2.299,6.995,3.93,10.909,4.711c3.851,0.781,7.835,0.696,11.696,0.035c3.833-0.673,7.551-2.084,10.787-4.26
			c5.183,4.042,10.389,8.06,15.568,12.099c-5.608,5.18-12.635,8.727-20.061,10.436c-8.215,1.872-16.906,1.921-25.068-0.268
			c-6.469-1.723-12.57-4.804-17.806-8.979C14.962,83.953,10.39,78.33,7.2,72.002z"/>
	</g>
</g>
</svg>';
?>
<div class="uael-review-wrap">
	<div class="uael-review uael-review-type-<?php echo esc_attr( $review['source'] ); ?>">
		<?php if ( 'yes' === $this->get_instance_value( 'reviewer_image' ) && 'all_left' === $this->get_instance_value( 'image_align' ) ) { ?>
			<div class="uael-review-image" style="background-image:url( <?php echo esc_url( $photolink ); ?> );"></div>
		<?php } ?>
		<div class="uael-review-inner-wrap">
			<?php if ( 'yes' === $this->get_instance_value( 'review_content' ) ) { ?>
				<?php
				$the_content = $review['text'];
				if ( '' !== $this->get_instance_value( 'review_content_length' ) ) {
					$the_content    = wp_strip_all_tags( $review['text'] ); // Strips tags.
					$content_length = $this->get_instance_value( 'review_content_length' ); // Sets content length by word count.
					$words          = explode( ' ', $the_content, $content_length + 1 );
					if ( count( $words ) > $content_length ) {
						array_pop( $words );
						$the_content  = implode( ' ', $words ); // put in content only the number of word that is set in $content_length.
						$the_content .= '...';
						if ( '' !== $this->get_instance_value( 'read_more' ) ) {
							$the_content .= '<a href="' . apply_filters( 'uael_business_reviews_read_more', $review['review_url'] ) . '"  target="_blank" rel="noopener noreferrer" class="uael-reviews-read-more">' . $this->get_instance_value( 'read_more' ) . '</a>';
						}
					}
				}
				?>
				<div class="uael-review-content-wrap">
					<div class="uael-review-content"><?php echo wp_kses_post( $the_content ); ?>
						<?php if ( 'yes' !== $this->get_instance_value( 'hide_arrow_content' ) ) { ?>
							<div class="uael-review-content-arrow-wrap">
								<div class="uael-review-arrow-border"></div>
								<div class="uael-review-arrow"></div>
							</div>
						<?php } ?>
						<?php if ( 'yes' === $this->get_instance_value( 'review_source_icon' ) ) { ?>
							<div class="uael-review-icon-wrap">
								<?php if ( 'yelp' === $review['source'] ) { ?>
									<i class="fa fa-yelp" aria-hidden="true"></i>
									<?php
								} else {
									echo $google_svg; // phpcs:ignore
								}
								?>
							</div>
						<?php } ?>
					</div>
				</div>
			<?php } ?>
			<?php $this->get_reviews_header( $review, $photolink, $settings ); ?>
		</div>
	</div>
</div>
<?php
