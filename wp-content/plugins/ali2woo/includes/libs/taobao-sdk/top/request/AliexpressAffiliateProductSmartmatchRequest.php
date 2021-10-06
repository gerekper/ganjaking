<?php
/**
 * TOP API: aliexpress.affiliate.product.smartmatch request
 * 
 * @author auto create
 * @since 1.0, 2020.05.20
 */
class AliexpressAffiliateProductSmartmatchRequest
{
	/** 
	 * 接入APP信息
	 **/
	private $app;
	
	/** 
	 * 请求签名
	 **/
	private $appSignature;
	
	/** 
	 * 设备信息
	 **/
	private $device;
	
	/** 
	 * adid或者idfa
	 **/
	private $deviceId;
	
	/** 
	 * 返回字段列表
	 **/
	private $fields;
	
	/** 
	 * 关键词
	 **/
	private $keywords;
	
	/** 
	 * 请求页数
	 **/
	private $pageNo;
	
	/** 
	 * 商品ID
	 **/
	private $productId;
	
	/** 
	 * 站点信息
	 **/
	private $site;
	
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
	
	/** 
	 * 用户信息
	 **/
	private $user;
	
	private $apiParas = array();
	
	public function setApp($app)
	{
		$this->app = $app;
		$this->apiParas["app"] = $app;
	}

	public function getApp()
	{
		return $this->app;
	}

	public function setAppSignature($appSignature)
	{
		$this->appSignature = $appSignature;
		$this->apiParas["app_signature"] = $appSignature;
	}

	public function getAppSignature()
	{
		return $this->appSignature;
	}

	public function setDevice($device)
	{
		$this->device = $device;
		$this->apiParas["device"] = $device;
	}

	public function getDevice()
	{
		return $this->device;
	}

	public function setDeviceId($deviceId)
	{
		$this->deviceId = $deviceId;
		$this->apiParas["device_id"] = $deviceId;
	}

	public function getDeviceId()
	{
		return $this->deviceId;
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

	public function setKeywords($keywords)
	{
		$this->keywords = $keywords;
		$this->apiParas["keywords"] = $keywords;
	}

	public function getKeywords()
	{
		return $this->keywords;
	}

	public function setPageNo($pageNo)
	{
		$this->pageNo = $pageNo;
		$this->apiParas["page_no"] = $pageNo;
	}

	public function getPageNo()
	{
		return $this->pageNo;
	}

	public function setProductId($productId)
	{
		$this->productId = $productId;
		$this->apiParas["product_id"] = $productId;
	}

	public function getProductId()
	{
		return $this->productId;
	}

	public function setSite($site)
	{
		$this->site = $site;
		$this->apiParas["site"] = $site;
	}

	public function getSite()
	{
		return $this->site;
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

	public function setUser($user)
	{
		$this->user = $user;
		$this->apiParas["user"] = $user;
	}

	public function getUser()
	{
		return $this->user;
	}

	public function getApiMethodName()
	{
		return "aliexpress.affiliate.product.smartmatch";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->deviceId,"deviceId");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
