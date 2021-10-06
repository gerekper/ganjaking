<?php
/**
 * TOP API: aliexpress.affiliate.link.generate request
 * 
 * @author auto create
 * @since 1.0, 2020.03.09
 */
class AliexpressAffiliateLinkGenerateRequest
{
	/** 
	 * API请求签名
	 **/
	private $appSignature;
	
	/** 
	 * 转换的链接类型：0代表普通Link，1代表Search Link，2代表 hot link
	 **/
	private $promotionLinkType;
	
	/** 
	 * 原始链接或者值
	 **/
	private $sourceValues;
	
	/** 
	 * 推广者原始trackingID
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

	public function setPromotionLinkType($promotionLinkType)
	{
		$this->promotionLinkType = $promotionLinkType;
		$this->apiParas["promotion_link_type"] = $promotionLinkType;
	}

	public function getPromotionLinkType()
	{
		return $this->promotionLinkType;
	}

	public function setSourceValues($sourceValues)
	{
		$this->sourceValues = $sourceValues;
		$this->apiParas["source_values"] = $sourceValues;
	}

	public function getSourceValues()
	{
		return $this->sourceValues;
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
		return "aliexpress.affiliate.link.generate";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->promotionLinkType,"promotionLinkType");
		RequestCheckUtil::checkNotNull($this->sourceValues,"sourceValues");
		RequestCheckUtil::checkNotNull($this->trackingId,"trackingId");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
