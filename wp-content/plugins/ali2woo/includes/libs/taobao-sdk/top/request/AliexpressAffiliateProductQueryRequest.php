<?php
/**
 * TOP API: aliexpress.affiliate.product.query request
 * 
 * @author auto create
 * @since 1.0, 2020.05.20
 */
class AliexpressAffiliateProductQueryRequest
{
	/** 
	 * 安全签名
	 **/
	private $appSignature;
	
	/** 
	 * 类目ID列表
	 **/
	private $categoryIds;
	
	/** 
	 * 返回字段列表
	 **/
	private $fields;
	
	/** 
	 * 关键词
	 **/
	private $keywords;
	
	/** 
	 * 最大售价
	 **/
	private $maxSalePrice;
	
	/** 
	 * 最小售价
	 **/
	private $minSalePrice;
	
	/** 
	 * 查询页码
	 **/
	private $pageNo;
	
	/** 
	 * 每页记录数
	 **/
	private $pageSize;
	
	/** 
	 * 平台商品类型：ALL,PLAZA,TMALL
	 **/
	private $platformProductType;
	
	/** 
	 * 排序方式:SALE_PRICE_ASC, SALE_PRICE_DESC, LAST_VOLUME_ASC, LAST_VOLUME_DESC
	 **/
	private $sort;
	
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

	public function setCategoryIds($categoryIds)
	{
		$this->categoryIds = $categoryIds;
		$this->apiParas["category_ids"] = $categoryIds;
	}

	public function getCategoryIds()
	{
		return $this->categoryIds;
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

	public function setMaxSalePrice($maxSalePrice)
	{
		$this->maxSalePrice = $maxSalePrice;
		$this->apiParas["max_sale_price"] = $maxSalePrice;
	}

	public function getMaxSalePrice()
	{
		return $this->maxSalePrice;
	}

	public function setMinSalePrice($minSalePrice)
	{
		$this->minSalePrice = $minSalePrice;
		$this->apiParas["min_sale_price"] = $minSalePrice;
	}

	public function getMinSalePrice()
	{
		return $this->minSalePrice;
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

	public function setPlatformProductType($platformProductType)
	{
		$this->platformProductType = $platformProductType;
		$this->apiParas["platform_product_type"] = $platformProductType;
	}

	public function getPlatformProductType()
	{
		return $this->platformProductType;
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
		return "aliexpress.affiliate.product.query";
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
