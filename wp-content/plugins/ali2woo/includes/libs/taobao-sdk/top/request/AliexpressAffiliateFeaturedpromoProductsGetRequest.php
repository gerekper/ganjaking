<?php
/**
 * TOP API: aliexpress.affiliate.featuredpromo.products.get request
 * 
 * @author auto create
 * @since 1.0, 2020.06.08
 */
class AliexpressAffiliateFeaturedpromoProductsGetRequest
{
	/** 
	 * 请求签名
	 **/
	private $appSignature;
	
	/** 
	 * 类目 ID 如何获取category_id，请参考，https://open.taobao.com/api.htm?docId=45801&docType=2&scopeId=17063
	 **/
	private $categoryId;
	
	/** 
	 * 目标国家
	 **/
	private $country;
	
	/** 
	 * 返回字段列表
	 **/
	private $fields;
	
	/** 
	 * 查询页码
	 **/
	private $pageNo;
	
	/** 
	 * 每页记录数，1-50
	 **/
	private $pageSize;
	
	/** 
	 * 活动结束时间，PST 时区
	 **/
	private $promotionEndTime;
	
	/** 
	 * 主题活动的名称，如何获取主题活动，请参考"get featuredpromo info" API. 固定主题：高佣品（Hot Product）、新品（New Arrival）、热销商品（Best Seller）、每周尖货（Weekly Deals）
	 **/
	private $promotionName;
	
	/** 
	 * 活动开始时间，PST 时区
	 **/
	private $promotionStartTime;
	
	/** 
	 * 排序方式：commissionAsc，commissionDesc, priceAsc，priceDesc，volumeAsc、volumeDesc, discountAsc, discountDesc, ratingAsc，ratingDesc, promotionTimeAsc, pr
	 **/
	private $sort;
	
	/** 
	 * 目标币种，可根据目标币种返回对应币种：USD, GBP, CAD, EUR, UAH, MXN, TRY, RUB, BRL, AUD, INR, JPY, IDR, SEK,KRW
	 **/
	private $targetCurrency;
	
	/** 
	 * 目标语言，可根据目标语言返回对应语言：EN,RU,PT,ES,FR,ID,IT,TH,JA,AR,VI,TR,DE,HE,KO,NL,PL,MX,CL,IN
	 **/
	private $targetLanguage;
	
	/** 
	 * trackingID
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

	public function setCategoryId($categoryId)
	{
		$this->categoryId = $categoryId;
		$this->apiParas["category_id"] = $categoryId;
	}

	public function getCategoryId()
	{
		return $this->categoryId;
	}

	public function setCountry($country)
	{
		$this->country = $country;
		$this->apiParas["country"] = $country;
	}

	public function getCountry()
	{
		return $this->country;
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

	public function setPageNo($pageNo)
	{
		$this->pageNo = $pageNo;
		$this->apiParas["page_no"] = $pageNo;
	}

	public function getPageNo()
	{
		return $this->pageNo;
	}

	public function setPageSize($pageSize)
	{
		$this->pageSize = $pageSize;
		$this->apiParas["page_size"] = $pageSize;
	}

	public function getPageSize()
	{
		return $this->pageSize;
	}

	public function setPromotionEndTime($promotionEndTime)
	{
		$this->promotionEndTime = $promotionEndTime;
		$this->apiParas["promotion_end_time"] = $promotionEndTime;
	}

	public function getPromotionEndTime()
	{
		return $this->promotionEndTime;
	}

	public function setPromotionName($promotionName)
	{
		$this->promotionName = $promotionName;
		$this->apiParas["promotion_name"] = $promotionName;
	}

	public function getPromotionName()
	{
		return $this->promotionName;
	}

	public function setPromotionStartTime($promotionStartTime)
	{
		$this->promotionStartTime = $promotionStartTime;
		$this->apiParas["promotion_start_time"] = $promotionStartTime;
	}

	public function getPromotionStartTime()
	{
		return $this->promotionStartTime;
	}

	public function setSort($sort)
	{
		$this->sort = $sort;
		$this->apiParas["sort"] = $sort;
	}

	public function getSort()
	{
		return $this->sort;
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
		return "aliexpress.affiliate.featuredpromo.products.get";
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
