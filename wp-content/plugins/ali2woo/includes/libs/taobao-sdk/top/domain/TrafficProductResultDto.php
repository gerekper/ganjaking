<?php

/**
 * 返回结果状态结果
 * @author auto create
 */
class TrafficProductResultDto
{
	
	/** 
	 * 当前页码
	 **/
	public $current_page_no;
	
	/** 
	 * 当前返回数量
	 **/
	public $current_record_count;
	
	/** 
	 * 返回商品明细
	 **/
	public $products;
	
	/** 
	 * 总计页数
	 **/
	public $total_page_no;
	
	/** 
	 * 总计数量
	 **/
	public $total_record_count;	
}
?>