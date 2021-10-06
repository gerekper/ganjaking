<?php
/**
 * TOP API: aliexpress.affiliate.hotproduct.download request
 * 
 * @author auto create
 * @since 1.0, 2020.05.19
 */
class AliexpressAffiliateHotproductDownloadRequest
{
	/** 
	 * 请求签名
	 **/
	private $appSignature;
	
	/** 
	 * 类目ID
	 **/
	private $categoryId;
	
	/** 
	 * 返回字段列表
	 **/
	private $fields;
	
	/** 
	 * 站点商品标：global,it_site,es_site,ru_site
	 **/
	private $localeSite;
	
	/** 
	 * 请求页数
	 **/
	private $pageNo;
	
	/** 
	 * 每次请求数量
	 **/
	private $pageSize;
	
	/** 
	 * 目标币种:USD, GBP, CAD, EUR, UAH, MXN, TRY, RUB, BRL, AUD, INR, JPY, IDR, SEK,KRW
	 **/
	private $targetCurrency;
	
	/** 
	 * 目标语言:EN,RU,PT,ES,FR,ID,IT,TH,JA,AR,VI,TR,DE,HE,KO,NL,PL,MX,CL,IW,IN
	 **/
	private $targetLanguage;
	
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

	public function setFields($fields)
	{
		$this->fields = $fields;
		$this->apiParas["fields"] = $fields;
	}

	public function getFields()
	{
		return $this->fields;
	}

	public function setLocaleSite($localeSite)
	{
		$this->localeSite = $localeSite;
		$this->apiParas["locale_site"] = $localeSite;
	}

	public function getLocaleSite()
	{
		return $this->localeSite;
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

	public function getApiMethodName()
	{
		return "aliexpress.affiliate.hotproduct.download";
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
