<?php

/*
Class Name: VI_WNOTIFICATION_Admin_Report
Author: Andy Ha (support@villatheme.com)
Author URI: http://villatheme.com
Copyright 2016-2019 villatheme.com. All rights reserved.
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VI_WNOTIFICATION_Admin_Report {
	protected $start_date;
	protected $end_date;

	public function __construct() {
		$current_time  = current_time( 'timestamp' );
		$this->start_date = date( 'M d, Y', ( $current_time - 30 * 86400 ) );
		$this->end_date   = date( 'M d, Y', $current_time );
		add_action( 'admin_menu', array( $this, 'menu_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		/*WordPress lower 4.5*/
		if ( woocommerce_notification_wpversion() ) {
			add_action( 'admin_print_scripts', array( $this, 'custom_script' ) );
		}
	}

	/**
	 * Custom script in WordPress lower 4.5
	 */
	public function custom_script() {
		$id      = isset( $_GET['id'] ) ? intval( $_GET['id'] ) : '';
		$subpage = isset( $_GET['subpage'] ) ? $_GET['subpage'] : '';
		if ( $id && $subpage ) {
			$data = $this->get_data( $id );
		} else {
			$data = $this->get_data();
		}
		if ( $data ) {

			/*Labels*/
			$labels = array();
			if ( count( $data->label ) ) {
				foreach ( $data->label as $label ) {
					$labels[] = date( "M d", $label );
				}
			}
			$labels = '"' . implode( '","', $labels ) . '"';

			/*Data*/
			$counts = array();

			if ( count( $data->data ) ) {
				if ( $id && $subpage ) {
					$counts = $data->data;
				} else {
					foreach ( $data->data as $count ) {
						$counts[] = count( $count );
					}
				}
			}
			$counts = '"' . implode( '","', $counts ) . '"';


			/*Javascript*/
			$script = '
					var woo_notification_labels = [' . $labels . '];
					var woo_notification_label = ["' . esc_js( __( 'Click', 'woocommerce-notification' ) ) . '"];
					var woo_notification_data = [' . $counts . '];';

		} else {

			$script = '
					var woo_notification_labels = [];
					var woo_notification_label = ["' . esc_js( __( 'Click', 'woocommerce-notification' ) ) . '"];
					var woo_notification_data = [];';
		}
		?>
        <script type="text/javascript">
			<?php echo $script; ?>
        </script>
	<?php }

	/**
	 * Add script
	 */
	public function admin_enqueue_scripts() {
		$page    = isset( $_REQUEST['page'] ) ? $_REQUEST['page'] : '';
		$id      = isset( $_GET['id'] ) ? intval( $_GET['id'] ) : '';
		$subpage = isset( $_GET['subpage'] ) ? $_GET['subpage'] : '';
		if ( $page == 'woocommerce-notification-report' ) {
			wp_enqueue_style( 'jquery-ui-datepicker', VI_WNOTIFICATION_CSS . 'jquery-ui-1.10.1.css' );
			wp_enqueue_style( 'jquery-ui-datepicker-latoja', VI_WNOTIFICATION_CSS . 'latoja.datepicker.css' );
			wp_enqueue_style( 'woocommerce-notification-menu', VI_WNOTIFICATION_CSS . 'menu.min.css' );
			wp_enqueue_style( 'woocommerce-notification-form', VI_WNOTIFICATION_CSS . 'form.min.css' );


			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_enqueue_script( 'woocommerce-notification-chart', VI_WNOTIFICATION_JS . 'Chart.min.js', array( 'jquery' ) );
			wp_enqueue_script( 'woocommerce-notification-report', VI_WNOTIFICATION_JS . 'woocommerce-notification-admin-report.js', array( 'jquery' ) );


			/*Custom*/
			if ( woocommerce_notification_wpversion() ) {
				return;
			}
			if ( $id && $subpage ) {
				$data = $this->get_data( $id );
			} else {
				$data = $this->get_data();
			}
			if ( $data ) {

				/*Labels*/
				$labels = array();
				if ( count( $data->label ) ) {
					foreach ( $data->label as $label ) {
						$labels[] = date( "M d", $label );
					}
				}
				$labels = '"' . implode( '","', $labels ) . '"';

				/*Data*/
				$counts = array();

				if ( count( $data->data ) && is_array( $data->data ) ) {
					if ( $id && $subpage ) {
						$counts = $data->data;
					} else {
						foreach ( $data->data as $count ) {
							$counts[] = count( $count );
						}
					}
				}
				$counts = '"' . implode( '","', $counts ) . '"';


				/*Javascript*/
				$script = '
					var woo_notification_labels = [' . $labels . '];
					var woo_notification_label = ["' . esc_js( __( 'Click', 'woocommerce-notification' ) ) . '"];
					var woo_notification_data = [' . $counts . '];';
				wp_add_inline_script( 'woocommerce-notification-report', $script );

			} else {

				$script = '
					var woo_notification_labels = [];
					var woo_notification_label = ["' . esc_js( __( 'Click', 'woocommerce-notification' ) ) . '"];
					var woo_notification_data = [];';
				wp_add_inline_script( 'woocommerce-notification-report', $script );
			}
		}
	}

	/**
	 * @param bool $id
	 *
	 * @return bool|stdClass
	 */
	private function get_data( $id = false ) {
		$start_date    = '';
		$end_date      = '';
		if ( isset( $_GET['_wpnonce'] ) ) {
			if ( wp_verify_nonce( $_GET['_wpnonce'], 'woocommerce_notification_filter_date' ) ) {
				$start_date       = isset( $_GET['start_date'] ) ? urldecode( $_GET['start_date'] ) : $this->start_date;
				$end_date         = isset( $_GET['end_date'] ) ? urldecode( $_GET['end_date'] ) : $this->end_date;
				/*Convert to int*/
				$start_date = strtotime( $start_date );
				$end_date   = strtotime( $end_date );
			}
		}


		$files = $this->scan_dir( VI_WNOTIFICATION_CACHE );
		if ( ! is_array( $files ) ) {
			return false;
		}
		$data  = new stdClass();
		$files = array_map( 'intval', $files );
		asort( $files );
		$files      = array_values( $files );
		$settings   = new VI_WNOTIFICATION_Data();
		$time_clear = intval( $settings->get_history_time() );
		if ( $time_clear > 0 && count( $files ) ) {
			$clear_end = strtotime( current_time( "Y-m-d" ) . " - {$time_clear} days" );

			foreach ( $files as $k => $file ) {
				if ( $file <= $clear_end ) {
					wp_delete_file( VI_WNOTIFICATION_CACHE . $file . '.txt' );
					unset( $files[ $k ] );
				}
			}
			$files = array_values( $files );
		}

		/*Filter files*/
		if ( $start_date || $end_date ) {
			$new_arg = array();
			if ( $start_date && $end_date ) {
				foreach ( $files as $file ) {
					if ( $file >= $start_date && $file <= $end_date ) {
						$new_arg[] = $file;
					}
				}
			} elseif ( $start_date ) {
				foreach ( $files as $file ) {
					if ( $file >= $start_date ) {
						$new_arg[] = $file;
					}
				}
			} else {
				foreach ( $files as $file ) {
					if ( $file <= $end_date ) {
						$new_arg[] = $file;
					}
				}
			}

			$files = $new_arg;

			if ( count( $files ) < 1 ) {
				return false;
			}
		}

		$data->label = $files;
		$temp        = array();
		if ( count( $files ) ) {
			foreach ( $files as $file ) {
				@$content = file_get_contents( VI_WNOTIFICATION_CACHE . $file . '.txt' );
				if ( $content ) {
					$array = explode( ',', $content );
					if ( $id ) {
						$counts = array_count_values( $array );
						$temp[] = isset( $counts[ $id ] ) ? $counts[ $id ] : 0;
					} else {
						$temp[] = $array;
					}
				}
			}
		}
		if ( count( $temp ) ) {
			$data->data = $temp;
		} else {
			$data->data = false;
		}

		return $data;
	}

	/**
	 * Get files in directory
	 *
	 * @param $dir
	 *
	 * @return array|bool
	 */
	private function scan_dir( $dir ) {
		$ignored = array( '.', '..', '.svn', '.htaccess', 'test-log.log' );

		$files = array();
		if ( is_dir( $dir ) ) {
			$scan_dir = scandir( $dir );
			if ( is_array( $scan_dir ) && count( $scan_dir ) ) {
				foreach ( $scan_dir as $file ) {
					if ( in_array( $file, $ignored ) ) {
						continue;
					}
					$files[ $file ] = filemtime( $dir . '/' . $file );
				}
			}

		}
		arsort( $files );
		$files = array_keys( $files );

		return ( $files ) ? $files : false;
	}

	/**
	 * HTML Reporting
	 */
	public function page_callback() {
		$start_date       = isset( $_GET['start_date'] ) ? urldecode( $_GET['start_date'] ) : $this->start_date;
		$end_date         = isset( $_GET['end_date'] ) ? urldecode( $_GET['end_date'] ) : $this->end_date;
		$active     = isset( $_GET['subpage'] ) ? 1 : 0;
		?>
        <h2><?php esc_html_e( 'WooCommerce Notification Reporting', 'woocommerce-notification' ) ?></h2>
        <div class="vi-ui secondary pointing menu">
            <a class="item <?php echo ! $active ? 'active' : ''; ?>"
               href="<?php echo admin_url( 'admin.php?page=woocommerce-notification-report' ) ?>"><?php esc_html_e( 'Clicks by date', 'woocommerce-notification' ) ?></a>
            <a class="item <?php echo $active ? 'active' : ''; ?>"
               href="<?php echo admin_url( 'admin.php?page=woocommerce-notification-report&subpage=byproduct' ) ?>"><?php esc_html_e( 'Clicks by product', 'woocommerce-notification' ) ?></a>
        </div>
		<?php if ( ! $active ) { ?>
            <form class="vi-ui form" action="<?php esc_attr_e( admin_url( 'admin.php' ) ) ?>" method="get">
				<?php wp_nonce_field( 'woocommerce_notification_filter_date', '_wpnonce' ) ?>
                <input type="hidden" name="page" value="woocommerce-notification-report">
                <div class="inline fields">
                    <div class="two field">
                        <label><?php esc_html_e( 'From', 'woocommerce-notification' ) ?></label>
                        <input type="text" name="start_date" class="datepicker"
                               value="<?php echo esc_attr( $start_date ) ?>"/>
                    </div>
                    <div class="two field">
                        <label><?php esc_html_e( 'To', 'woocommerce-notification' ) ?></label>
                        <input type="text" name="end_date" class="datepicker"
                               value="<?php echo esc_attr( $end_date ) ?>"/>
                    </div>
                    <div class="two field">
                        <input type="submit" value=" <?php esc_html_e( 'SUBMIT', 'woocommerce-notification' ) ?> "
                               class="button button-primary"/>
                    </div>
                </div>
            </form>
		<?php } ?>
        <div class="vi-ui form">
            <div class="fields">
				<?php if ( $active ) { ?>
                    <div class="five wide field">
                        <h3><?php echo esc_html__( 'Top Click', 'woocommerce-notification' ) ?></h3>
                        <table class="table" width="100%" cellspacing="2" cellpadding="2">
                            <tr>
                                <th align="left"
                                    width="80%"><?php esc_html_e( 'Products', 'woocommerce-notification' ) ?></th>
                                <th align="left"
                                    width="20%"><?php esc_html_e( 'Clicked', 'woocommerce-notification' ) ?></th>
                            </tr>
							<?php
							$data = new stdClass();
							$data = $this->get_data();
							if ( isset( $data->data ) ) {
								$result   = array_reduce( $data->data, 'array_merge', array() );
								$products = array_count_values( $result );
								arsort( $products );
								foreach ( $products as $k => $count ) { ?>
                                    <tr>
                                        <td>
                                            <a href="<?php echo admin_url( 'admin.php?page=woocommerce-notification-report&subpage=byproduct&id=' . $k ) ?>">
												<?php echo esc_html( get_post_field( 'post_title', $k ) ) ?>
                                            </a>
                                        </td>
                                        <td>
                                            <span class="count"><?php echo esc_html( $count ) ?></span>
                                        </td>
                                    </tr>
								<?php }
							}
							?>
                        </table>
                    </div>
				<?php } ?>
                <div class="eleven wide field">
                    <canvas id="myChart"></canvas>
                </div>

            </div>
        </div>
	<?php }

	/**
	 * Register a custom menu page.
	 */
	public function menu_page() {
		add_submenu_page(
			'woocommerce-notification',
			esc_html__( 'Report', 'woocommerce-notification' ),
			esc_html__( 'Report', 'woocommerce-notification' ),
			'manage_options',
			'woocommerce-notification-report',
			array( $this, 'page_callback' )
		);

	}
}

?>