<?php
/**
 * YITH Composite Products for WooCommerce Class.
 *
 * @class   WC_Product_Yith_Composite
 * @version 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_Product_Yith_Composite extends WC_Product {

	/**
	 * @var
	 */
	private $_virtual;

	/**
	 * @var
	 */
	private $_per_item_shipping;

	/**
	 * @var
	 */
	private $_per_item_pricing;

	/**
	 * @var mixed
	 */
	private $_layout_options;

	/**
	 * @var
	 */
	private $_layout_options_product_list_position;

	/**
	 * @var mixed
	 */
	private $_components_data;

	/**
	 * @var mixed
	 */
	private $_dependencies_data;

	/**
	 * @var mixed
	 */
	private $_dependencies_data_compontents_detail;

	public function __construct( $product_id ) {

		if ( version_compare( WC()->version, '2.7', '<' ) ) {
			$this->product_type = $this->get_type();
		}

		parent::__construct( $product_id );

		//Shipping and Pricing Option

        $product_object_id = $this->get_id();

		if ( get_post_meta( $product_object_id, '_ywcp_virtual', 'no' ) == 'yes' ) {
			$this->set_prop( 'virtual', true );
			$this->_virtual = true;
		}
		if ( get_post_meta( $product_object_id, '_ywcp_downloadable', 'no' ) == 'yes' ) {
			$this->set_prop( 'downloadable', true );
		}

		$this->_per_item_shipping = get_post_meta( $product_object_id, '_ywcp_options_product_per_item_shipping', 'no' ) == 'yes';
		$this->_per_item_pricing = get_post_meta( $product_object_id, '_ywcp_options_product_per_item_pricing', 'no' ) == 'yes';

		// style options

		$this->_layout_options = get_post_meta( $product_object_id , '_ywcp_layout_options', 'list' );

		if( isset( $_REQUEST['ywcp_layout_options'] ) && ( in_array( $_REQUEST['ywcp_layout_options'] , array( 'list' , 'accordion' , 'step' ) ) ) ) {
			$this->_layout_options = $_REQUEST['ywcp_layout_options'];
		}

		$this->_layout_options_product_list_position = get_post_meta( $product_object_id , '_ywcp_layout_options_product_list_position', 'cascading' );

		// components data

		$this->_components_data = get_post_meta( $product_object_id, '_ywcp_component_data_list', array() );

		// components data

		$this->_dependencies_data = get_post_meta( $product_object_id , '_ywcp_dependencies_data_list' );
		$this->_dependencies_data_compontents_detail = get_post_meta( $product_object_id , '_ywcp_dependencies_component_data_options' );

	}

    /**
     * Get internal type.
     *
     * @return string
     */
    public function get_type() {
        return 'yith-composite';
    }

	/**
	 * @return bool
	 */
	public function is_virtual() {
		return $this->_virtual;
	}

	/**
	 * @return mixed
	 */
	public function isPerItemShipping() {
		return $this->_per_item_shipping;
	}

	/**
	 * @return mixed
	 */
	public function isPerItemPricing() {
		return $this->_per_item_pricing;
	}


	/**
	 * @return mixed|string
	 */
	public function getLayoutOptions() {
		if( $this->_layout_options ) {
			return $this->_layout_options;
		} else {
			return '';
		}
	}

	/**
	 * @return mixed|string
	 */
	public function getLayoutOptionsProductListPosition() {
		if( $this->_layout_options_product_list_position ) {
			return $this->_layout_options_product_list_position;
		} else {
			return '';
		}
	}

	/**
	 * @return array
	 */
	public function getComponentsData() {
	   
		if( isset( $this->_components_data[0] ) ) {
			return $this->_components_data[0];
	   } else {
			return array();	
		}
		
	}

	/**
	 * @param $key
	 *
	 * @return array
	 */
	public function getComponentItemByKey( $key ) {

		if( isset( $this->_components_data[0] ) && isset( $this->_components_data[0][$key] ) ) {
			return $this->_components_data[0][$key];
		} else {
			return array();
		}

	}

	/**
	 * @return array
	 */
	public function getDependenciesData() {

		if( isset( $this->_dependencies_data[0] ) ) {
			return $this->_dependencies_data[0];
		} else {
			return array();
		}

	}

	/**
	 * @return array
	 */
	public function getDependenciesDataComponentsDetails() {

		if( isset( $this->_dependencies_data_compontents_detail[0] ) ) {
			return $this->_dependencies_data_compontents_detail[0];
		} else {
			return array();
		}

	}

}
