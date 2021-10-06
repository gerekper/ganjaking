<?php
/**
 * TOP API: aliexpress.affiliate.productdetail.get request
 * 
 * @author auto create
 * @since 1.0, 2020.05.19
 */
class AliexpressAffiliateProductdetailGetRequest
{
	/** 
	 * 安全签名
	 **/
	private $appSignature;
	
	/** 
	 * commission_rate,sale_price
	 **/
	private $fields;
	
	/** 
	 * 商品ID列表
	 **/
	private $productIds;
	
	/** 
	 * 目标币种:USD, GBP, CAD, EUR, UAH, MXN, TRY, RUB, BRL, AUD, INR, JPY, IDR, SEK,KRW
	 **/
	private $targetCurrency;
	
	/** 
	 * 目标语言:EN,RU,PT,ES,FR,ID,IT,TH,JA,AR,VI,TR,DE,HE,KO,NL,PL,MX,CL,IW,IN
	 **/
	private $targetLanguage;
	
	/** 
	 * trackingId
	 **/
	private $trackingId;
	
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

	public function setProductIds($productIds)
	{
		$this->productIds = $productIds;
		$this->apiParas["product_ids"] = $productIds;
	}

	public function getProductIds()
	{
		return $this->productIds;
	}

	public function setTargetCurrency($targetCurrency)
	{
		$this->targetCurrency = $targetCurrency;
		$this->apiParas["target_currency"] = $targetCurrency;
	}

	public function getTargetCurrency()
	{
		return $this->targetCurrency;
	}

	public function setTargetLanguage($targetLanguage)
	{
		$this->targetLanguage = $targetLanguage;
		$this->apiParas["target_language"] = $targetLanguage;
	}

	public function getTargetLanguage()
	{
		return $this->targetLanguage;
	}

	public function setTrackingId($trackingId)
	{
		$this->trackingId = $trackingId;
		$this->apiParas["tracking_id"] = $trackingId;
	}

	public function getTrackingId()
	{
		return $this->trackingId;
	}

	public function getApiMethodName()
	{
		return "aliexpress.affiliate.productdetail.get";
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
