<?php


namespace YoutubeFeed\Api\Video;

/**
 * Class YoutubeVideosResponse
 *
 * @property string $kind
 * @property string $etag
 * @property PageInfoResponse $pageInfo
 * @property YoutubeVideo[] $items
 *
 * @package YoutubeFeed\includes\Api
 */
class YoutubeVideosResponse
{

    /**
     * YoutubeVideosResponse constructor.
     *
     * @param $json
     */
    public function __construct($json)
    {
        $data = json_decode($json);

        $this->kind = $data->kind;
        $this->etag = $data->etag;
        $this->pageInfo = $data->pageInfo;
        $this->items = $data->items;

        return $this;
    }

}
