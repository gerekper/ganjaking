<?php
/**
 * TOP API: aliexpress.affiliate.category.get request
 * 
 * @author auto create
 * @since 1.0, 2020.03.09
 */
class AliexpressAffiliateCategoryGetRequest
{
	/** 
	 * 请求安全签名
	 **/
	private $appSignature;
	
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

	public function getApiMethodName()
	{
		return "aliexpress.affiliate.category.get";
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
