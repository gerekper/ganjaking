<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2019 ThemePunch
 */
 
if(!defined('ABSPATH')) exit();

class RevSliderWooCommerce extends RevSliderFunctions {
	
	const META_SKU	 = '_sku'; //can be 'instock' or 'outofstock'
	const META_STOCK = '_stock'; //can be 'instock' or 'outofstock'
	
	/**
	 * return true / false if the woo commerce exists
	 * @before RevSliderWooCommerce::isWooCommerceExists();
	 */
	public static function woo_exists(){
		return (class_exists('Woocommerce')) ? true : false;
	}
	
	
	/**
	 * compare wc current version to given version
	 */
	public static function version_check($version = '1.0') {
		if(self::woo_exists()){
			global $woocommerce;
			if(version_compare($woocommerce->version, $version, '>=')){
				return true;
			}
		}
		return false;
	}
	
	
	/**
	 * get wc post types
	 */
	public static function getCustomPostTypes(){
		$arr = array(
			'product'			=> __('Product', 'revslider'),
			'product_variation'	=> __('Product Variation', 'revslider')
		);
		
		return $arr;
	}
	
	
	/**
	 * get price query
	 * @before: RevSliderWooCommerce::getPriceQuery()
	 */
	private static function get_price_query($from, $to, $meta_tag){
		
		$from	= (empty($from)) ? 0 : $from;
		$to		= (empty($to)) ? 9999999999 : $to;
		$query	= array(
			'key'		=> $meta_tag,
			'value'		=> array($from, $to),
			'type'		=> 'numeric',
			'compare'	=> 'BETWEEN'
		);
		
		return $query;
	}
	
	
	/**
	 * get meta query for filtering woocommerce posts.
	 * before: RevSliderWooCommerce::getMetaQuery();
	 */
	public static function get_meta_query($args){
		$f = RevSliderGlobals::instance()->get('RevSliderFunctions');
		$reg_price_from		= $f->get_val($args, array('source', 'woo', 'regPriceFrom'));
		$reg_price_to		= $f->get_val($args, array('source', 'woo', 'regPriceTo'));
		$sale_price_from	= $f->get_val($args, array('source', 'woo', 'salePriceFrom'));
		$sale_price_to		= $f->get_val($args, array('source', 'woo', 'salePriceTo'));
		
		$query			= array();
		$meta_query		= array();
		$tax_query		= array();
		
		//get regular price array
		if(!empty($reg_price_from) || !empty($reg_price_to)){
			$meta_query[] = self::get_price_query($reg_price_from, $reg_price_to, '_regular_price');
		}
		
		//get sale price array
		if(!empty($sale_price_from) || !empty($sale_price_to)){
			$meta_query[] = self::get_price_query($sale_price_from, $sale_price_to, '_sale_price');
		}
		
		if($f->get_val($args, array('source', 'woo', 'inStockOnly')) == true){
			$meta_query[] = array(
				'key' => '_stock_status',
				'value' => 'instock',
				'compare' => '='
			);
		}
		
		if($f->get_val($args, array('source', 'woo', 'featuredOnly')) == true){
			$tax_query[] = array(
				'taxonomy' => 'product_visibility',
				'field'    => 'name',
				'terms'    => 'featured',
			);
		}
		
		if(!empty($meta_query)){
			$query['meta_query'] = $meta_query;
		}
		
		if(!empty($tax_query)){
			$query['tax_query'] = $tax_query;
		}
		
		return $query;
	}
	
	
	/**
	 * get sortby function including standart wp sortby array
	 */
	public static function getArrSortBy(){
		
		$sort_by = array(
			'meta_num__regular_price'	=> __('Regular Price', 'revslider'),
			'meta_num__sale_price'		=> __('Sale Price', 'revslider'),
			'meta_num_total_sales'		=> __('Number Of Sales', 'revslider'),
			//'meta__featured'			=> __('Featured Products', 'revslider'),
			'meta__sku'					=> __('SKU', 'revslider'),
			'meta_num_stock'			=> __('Stock Quantity', 'revslider')
		);
		
		return $sort_by;
	}
	
}	//end of the class