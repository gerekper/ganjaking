<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class MPCA_Membership_Controller {
  public function __construct() {
    add_action( 'mepr-product-advanced-metabox', array( $this, 'display_fields' ) );
    add_action( 'mepr-membership-save-meta', array( $this, 'save_meta' ) );
    add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
  }

  public function enqueue_scripts() {
    wp_enqueue_style('mpca-edit-membership', MPCA_URL . '/public/css/mpca-edit-membership.css');
  }

  public function display_fields($product) {
    // Instantiate helper for use in view template
    $helper = new MPCA_Admin_Helper();

    $ca_enabled = get_post_meta( $product->ID, 'mpca_is_corporate_product', true);
    $num_sub_accounts = get_post_meta( $product->ID, 'mpca_num_sub_accounts', true);

    require(MeprView::file('/mpca-edit-membership-template'));
  }

  public function save_meta($product) {
    if(isset($_POST['mpca_is_corporate_product'])) {
      update_post_meta( $product->ID, 'mpca_is_corporate_product', $_POST['mpca_is_corporate_product'] );
    } else{
      delete_post_meta($product->ID, 'mpca_is_corporate_product');
    }

    if(isset($_POST['mpca_num_sub_accounts'])) {
      update_post_meta( $product->ID, 'mpca_num_sub_accounts', $_POST['mpca_num_sub_accounts'] );
    }
  }
}
