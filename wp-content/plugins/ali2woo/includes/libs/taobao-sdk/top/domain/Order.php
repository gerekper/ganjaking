<?php

/**
 * 订单内容明细
 * @author auto create
 */
class Order
{
	
	/** 
	 * 所属类目ID
	 **/
	public $category_id;
	
	/** 
	 * 佣金率
	 **/
	public $commission_rate;
	
	/** 
	 * 订单创建时间
	 **/
	public $created_time;
	
	/** 
	 * 自定义参数(JSON格式）
	 **/
	public $customer_parameters;
	
	/** 
	 * 订单完成时的预计佣金
	 **/
	public $estimated_finished_commission;
	
	/** 
	 * 订单支付时的预计佣金
	 **/
	public $estimated_paid_commission;
	
	/** 
	 * 订单完成金额
	 **/
	public $finished_amount;
	
	/** 
	 * 订单完成时间
	 **/
	public $finished_time;
	
	/** 
	 * 是否爆品订单:Y 或者N
	 **/
	public $is_hot_product;
	
	/** 
	 * 是否新买家
	 **/
	public $is_new_buyer;
	
	/** 
	 * 新买家奖励佣金
	 **/
	public $new_buyer_bonus_commission;
	
	/** 
	 * 订单ID
	 **/
	public $order_id;
	
	/** 
	 * 子订单ID:已废弃，请使用sub_order_id
	 **/
	public $order_number;
	
	/** 
	 * 订单状态:Payment Completed,Buyer Confirmed Receipt
	 **/
	public $order_status;
	
	/** 
	 * 订单支付完成金额
	 **/
	public $paid_amount;
	
	/** 
	 * 订单支付完成时间
	 **/
	public $paid_time;
	
	/** 
	 * 父订单ID:已废弃，请使用order_id
	 **/
	public $parent_order_number;
	
	/** 
	 * 下单商品数量
	 **/
	public $product_count;
	
	/** 
	 * 商品DetailUrl
	 **/
	public $product_detail_url;
	
	/** 
	 * 商品ID
	 **/
	public $product_id;
	
	/** 
	 * 商品主图Url
	 **/
	public $product_main_image_url;
	
	/** 
	 * 商品标题
	 **/
	public $product_title;
	
	/** 
	 * 推广者结算币种
	 **/
	public $settled_currency;
	
	/** 
	 * 订单物流国家
	 **/
	public $ship_to_country;
	
	/** 
	 * 子订单ID
	 **/
	public $sub_order_id;
	
	/** 
	 * trackId
	 **/
	public $tracking_id;	
}
?>