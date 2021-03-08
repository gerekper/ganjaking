<?php


namespace YoutubeFeed\Api\Channel;


use YoutubeFeed\Api\Video\PageInfoResponse;

/**
 * @property string $kind
 * @property string $etag
 * @property PageInfoResponse $pageInfo
 * @property YoutubeChannelItem[] $items
 */
class YoutubeChannelResponse
{

    public function __construct($json)
    {
        $data = json_decode($json);

        $this->kind = isset($data->kind) ? $data->kind : '';
        $this->etag = isset($data->etag) ? $data->etag : '';
        $this->pageInfo = isset($data->pageInfo) ? $data->pageInfo : null;
        $this->items = isset($data->items) ? $data->items : null;

        return $this;
    }

}