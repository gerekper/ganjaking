<?php

/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */
namespace WPMailSMTP\Vendor\Google\Service\Gmail;

class Message extends \WPMailSMTP\Vendor\Google\Collection
{
    protected $collection_key = 'labelIds';
    public $historyId;
    public $id;
    public $internalDate;
    public $labelIds;
    protected $payloadType = \WPMailSMTP\Vendor\Google\Service\Gmail\MessagePart::class;
    protected $payloadDataType = '';
    public $raw;
    public $sizeEstimate;
    public $snippet;
    public $threadId;
    public function setHistoryId($historyId)
    {
        $this->historyId = $historyId;
    }
    public function getHistoryId()
    {
        return $this->historyId;
    }
    public function setId($id)
    {
        $this->id = $id;
    }
    public function getId()
    {
        return $this->id;
    }
    public function setInternalDate($internalDate)
    {
        $this->internalDate = $internalDate;
    }
    public function getInternalDate()
    {
        return $this->internalDate;
    }
    public function setLabelIds($labelIds)
    {
        $this->labelIds = $labelIds;
    }
    public function getLabelIds()
    {
        return $this->labelIds;
    }
    /**
     * @param MessagePart
     */
    public function setPayload(\WPMailSMTP\Vendor\Google\Service\Gmail\MessagePart $payload)
    {
        $this->payload = $payload;
    }
    /**
     * @return MessagePart
     */
    public function getPayload()
    {
        return $this->payload;
    }
    public function setRaw($raw)
    {
        $this->raw = $raw;
    }
    public function getRaw()
    {
        return $this->raw;
    }
    public function setSizeEstimate($sizeEstimate)
    {
        $this->sizeEstimate = $sizeEstimate;
    }
    public function getSizeEstimate()
    {
        return $this->sizeEstimate;
    }
    public function setSnippet($snippet)
    {
        $this->snippet = $snippet;
    }
    public function getSnippet()
    {
        return $this->snippet;
    }
    public function setThreadId($threadId)
    {
        $this->threadId = $threadId;
    }
    public function getThreadId()
    {
        return $this->threadId;
    }
}
// Adding a class alias for backwards compatibility with the previous class name.
\class_alias(\WPMailSMTP\Vendor\Google\Service\Gmail\Message::class, 'WPMailSMTP\\Vendor\\Google_Service_Gmail_Message');
