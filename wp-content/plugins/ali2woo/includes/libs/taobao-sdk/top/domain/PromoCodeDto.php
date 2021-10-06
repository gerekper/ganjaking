<?php

/**
 * code信息
 * @author auto create
 */
class PromoCodeDto
{
	
	/** 
	 * code使用有效期的结束时间
	 **/
	public $code_availabletime_end;
	
	/** 
	 * code使用有效期的开始时间
	 **/
	public $code_availabletime_start;
	
	/** 
	 * 优惠方式 1 满减，2 满折
	 **/
	public $code_campaigntype;
	
	/** 
	 * 最低使用门槛
	 **/
	public $code_mini_spend;
	
	/** 
	 * 品code合一url
	 **/
	public $code_promotionurl;
	
	/** 
	 * code剩余可使用的数量
	 **/
	public $code_quantity;
	
	/** 
	 * 面额
	 **/
	public $code_value;
	
	/** 
	 * 专属绑定PID的code码
	 **/
	public $promo_code;	
}
?>