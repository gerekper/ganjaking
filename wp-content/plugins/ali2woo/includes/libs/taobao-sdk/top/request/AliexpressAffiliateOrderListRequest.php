<?php
/**
 * TOP API: aliexpress.affiliate.order.list request
 * 
 * @author auto create
 * @since 1.0, 2020.04.13
 */
class AliexpressAffiliateOrderListRequest
{
	/** 
	 * 安全签名
	 **/
	private $appSignature;
	
	/** 
	 * 结束时间
	 **/
	private $endTime;
	
	/** 
	 * 返回的字段信息
	 **/
	private $fields;
	
	/** 
	 * 站点信息：global、ru_site、es_site、it_site
	 **/
	private $localeSite;
	
	/** 
	 * 页数
	 **/
	private $pageNo;
	
	/** 
	 * 每页记录数
	 **/
	private $pageSize;
	
	/** 
	 * 开始时间
	 **/
	private $startTime;
	
	/** 
	 * 订单状态:Payment Completed,Buyer Confirmed Receipt
	 **/
	private $status;
	
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

	public function setEndTime($endTime)
	{
		$this->endTime = $endTime;
		$this->apiParas["end_time"] = $endTime;
	}

	public function getEndTime()
	{
		return $this->endTime;
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

	public function setStartTime($startTime)
	{
		$this->startTime = $startTime;
		$this->apiParas["start_time"] = $startTime;
	}

	public function getStartTime()
	{
		return $this->startTime;
	}

	public function setStatus($status)
	{
		$this->status = $status;
		$this->apiParas["status"] = $status;
	}

	public function getStatus()
	{
		return $this->status;
	}

	public function getApiMethodName()
	{
		return "aliexpress.affiliate.order.list";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->endTime,"endTime");
		RequestCheckUtil::checkNotNull($this->startTime,"startTime");
		RequestCheckUtil::checkNotNull($this->status,"status");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
