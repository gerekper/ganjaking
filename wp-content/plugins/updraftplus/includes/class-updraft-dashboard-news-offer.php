<?php
if (!defined('ABSPATH')) die('No direct access allowed');

if (class_exists('Updraft_Dashboard_News_Offer')) return;

if (!class_exists('Updraft_Dashboard_News')) updraft_try_include_file('includes/class-updraft-dashboard-news.php', 'include_once');

/**
 * Overide Updraft_Dashboard_News class to offer the user regarding our news feed
 */
class Updraft_Dashboard_News_Offer extends Updraft_Dashboard_News {

	/**
	 * slug to use, where needed
	 *
	 * @var String
	 */
	protected $slug;

	/**
	 * Whether to require the user to first opt-in
	 *
	 * @var Boolean
	 */
	private $require_user_confirmation;

	/**
	 * Constructor
	 *
	 * @param String $feed_url	   - dashboard news feed URL
	 * @param String $link		   - web page URL
	 * @param Array  $translations - an array of translations, with keys: product_title, item_prefix, item_description, dismiss_confirm
	 */
	public function __construct($feed_url, $link, $translations) {
		global $updraftplus;

		$this->slug = sanitize_title($translations['product_title']);

		// Users of the wordpress.org release are required to explicitly opt in
		$this->require_user_confirmation = ('2' !== $updraftplus->version[0] || !defined('UDADDONS2_DIR'));

		if ($this->require_user_confirmation && 'no' === get_user_meta(get_current_user_id(), $this->slug.'_confirmed_news_offer', true)) return;

		parent::__construct($feed_url, $link, $translations);

		add_action('admin_enqueue_scripts', array($this, 'enqueue_inline_style'));

		add_action('wp_ajax_'.$this->slug.'_ajax_confirm_news_offer', array($this, 'confirm_news_offer'));
	}

	/**
	 * Enqueue news offer cascading style sheet
	 *
	 * @return void
	 */
	public function enqueue_inline_style() {
		global $pagenow;
		if ('index.php' != $pagenow) return;
		wp_register_style('updraftplus-news-offer-style', false); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- not specifying the URL as we just need an alias name/handle 
		wp_enqueue_style('updraftplus-news-offer-style');
		wp_add_inline_style('updraftplus-news-offer-style', $this->news_offer_css_style());
	}

	/**
	 * Get dashboard news HTML based on the installed version while respecting user pre-existing preference
	 *
	 * @return String - the output of the offering message in HTML format
	 */
	protected function get_dashboard_news_html() {
		if ($this->require_user_confirmation) {
			$confirmed_news_offer = (string) get_user_meta(get_current_user_id(), $this->slug.'_confirmed_news_offer', true);
			if ('no' === $confirmed_news_offer) return;
			if (!in_array(strtolower($confirmed_news_offer), array('yes', 'no'))) return '<div class="ud-news-container">'.parent::get_dashboard_news_html().$this->get_news_offer_html().'</div>';
		}
		return parent::get_dashboard_news_html();
	}

	/**
	 * Save current user confirmation as its own preferrence/meta to the database as to whether or not they prefer our news to be shown in their own admin dashboard
	 */
	public function confirm_news_offer() {
		$request = wp_unslash($_REQUEST);
		if (!wp_verify_nonce($request['nonce'], $this->slug.'-confirm-news-offer')) die('Security check.');
		if (!empty($request['confirmation']) && in_array(strtolower($request['confirmation']), array('yes', 'no'))) {
			update_user_meta(get_current_user_id(), $this->slug.'_confirmed_news_offer', $request['confirmation']);
		}
		die();
	}

	/**
	 * Get the HTML that offers our news feed
	 *
	 * @return String - the resulting message
	 */
	protected function get_news_offer_html() {
		ob_start();
		?>
		<div class="rss-widget ud-news-offer-box">
			<ul>
				<li class="rsswidget <?php echo esc_attr($this->slug.'_dashboard_news_offer_item'); ?>">
					<div>
						<p style="color: #fff"><?php esc_html_e(sprintf("This website uses the %s plugin.", 'UpdraftPlus'), 'updraftplus'); ?> <?php esc_html_e("Do you want to see official news from this plugin in this Events and News section?", 'updraftplus'); ?></p>
						<a class="ud-news-confirm-link" data-val="yes" href="<?php echo esc_url(UpdraftPlus::get_current_clean_url()); ?>"><?php esc_html_e('Yes, show me the news.', 'updraftplus'); ?></a><a class="ud-news-confirm-link" href="<?php echo esc_url(UpdraftPlus::get_current_clean_url()); ?>" data-val="no"><?php esc_html_e("No, please don't.", 'updraftplus'); ?></a>
					</div>
				</li>
			</ul>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Prints javascripts in admin footer
	 */
	public function admin_print_footer_scripts() {
		parent::admin_print_footer_scripts();
		$confirmed_news_offer = (string) get_user_meta(get_current_user_id(), $this->slug.'_confirmed_news_offer', true);
		if ($this->require_user_confirmation && !in_array(strtolower($confirmed_news_offer), array('yes', 'no'))) {
		?>
		<script>
			(function($) {
				$('.ud-news-offer-box').on('click', 'a.ud-news-confirm-link', function(e) {
					e.preventDefault();
					$this = $(this);
					jQuery.ajax({
						url: '<?php echo admin_url('admin-ajax.php');?>',
						data : {
							action: '<?php echo $this->slug; ?>_ajax_confirm_news_offer',
							confirmation: $($this).data('val'),
							nonce : '<?php echo wp_create_nonce($this->slug.'-confirm-news-offer');?>'
						},
						success: function(response) {
							if ('yes' == $($this).data('val')) {
								$('.ud-news-offer-box').fadeOut(500, function(e) {
									$('.ud-news-container > div').css('position', 'unset');
								});
							} else {
								$('.ud-news-container').fadeOut(500);
							}
						},
						error: function(response, status, error_code) {
							console.log("<?php echo $this->slug; ?>_confirmed_news_offer: error: "+status+" ("+error_code+")");
							console.log(response);
						}
					});
				});
			})(jQuery);
		</script>
		<?php
		}
	}

	/**
	 * News offer CSS style
	 *
	 * @return void
	 */
	protected function news_offer_css_style() {
		ob_start();
		?>
			div.ud-news-container {
				position: relative;
				height: 135px;
				padding-bottom: 0px !important;
				margin-bottom: 6px;
			}
			div.ud-news-container > div {
				position: absolute;
			}
			div.ud-news-offer-box {
				background-color: #db6939; 
				padding-top: 5px; 
				padding-bottom: 5px;
			}
			li.updraftplus_dashboard_news_offer_item a {
				float: right;
				margin-right: 13%;
			}
			div.ud-news-offer-box li a {
				color: #fff;
			}
			li.updraftplus_dashboard_news_offer_item a:hover {
				color: #cfc8c5;
			}
		<?php
		return ob_get_clean();
	}
}
