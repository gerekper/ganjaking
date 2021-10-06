<?php
/**
 * TOP API: aliexpress.affiliate.order.get request
 * 
 * @author auto create
 * @since 1.0, 2020.04.13
 */
class AliexpressAffiliateOrderGetRequest
{
	/** 
	 * 安全签名
	 **/
	private $appSignature;
	
	/** 
	 * 返回的字段列表
	 **/
	private $fields;
	
	/** 
	 * 订单ID列表，以逗号分隔，当前只支持子订单ID查询
	 **/
	private $orderIds;
	
	private $apiParas = array();
	
	public function setAppSignature($appSignature)
	{
		$this->appSignature = $appSignature;
		$this->apiParas["app_signature"] = $appSignature;
	}

	public function getAppSignature()
	{
		return $this->appSignature;
	}

	public function setFields($fields)
	{
		$this->fields = $fields;
		$this->apiParas["fields"] = $fields;
	}

	public function getFields()
	{
		return $this->fields;
	}

	public function setOrderIds($orderIds)
	{
		$this->orderIds = $orderIds;
		$this->apiParas["order_ids"] = $orderIds;
	}

	public function getOrderIds()
	{
		return $this->orderIds;
	}

	public function getApiMethodName()
	{
		return "aliexpress.affiliate.order.get";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
