<?php
/**
 * TOP API: aliexpress.affiliate.featuredpromo.get request
 * 
 * @author auto create
 * @since 1.0, 2020.06.02
 */
class AliexpressAffiliateFeaturedpromoGetRequest
{
	/** 
	 * 请求签名
	 **/
	private $appSignature;
	
	/** 
	 * 返回字段列表
	 **/
	private $fields;
	
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

	public function getApiMethodName()
	{
		return "aliexpress.affiliate.featuredpromo.get";
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
