<?php


namespace YoutubeFeed\Api\Video;

/**
 * Class YoubuteComment
 *
 * @property string $videoId
 * @property string $textDisplay
 * @property string $textOriginal
 * @property string $authorDisplayName
 * @property string $authorProfileImageUrl
 * @property string $authorChannelUrl
 * @property bool $canRate
 * @property string $viewerRating
 * @property int $likeCount
 * @property string $publishedAt
 * @property string $updatedAt
 *
 * @package YoutubeFeed\Api\Video
 */
class YoutubeComment
{
    public function __construct($item)
    {
        $this->videoId               = $item->snippet->topLevelComment->snippet->videoId;
        $this->textDisplay           = $item->snippet->topLevelComment->snippet->textDisplay;
        $this->textOriginal          = $item->snippet->topLevelComment->snippet->textOriginal;
        $this->authorDisplayName     = $item->snippet->topLevelComment->snippet->authorDisplayName;
        $this->authorProfileImageUrl = $item->snippet->topLevelComment->snippet->authorProfileImageUrl;
        $this->authorChannelUrl      = $item->snippet->topLevelComment->snippet->authorChannelUrl;
        $this->canRate               = $item->snippet->topLevelComment->snippet->canRate;
        $this->viewerRating          = $item->snippet->topLevelComment->snippet->viewerRating;
        $this->likeCount             = $item->snippet->topLevelComment->snippet->likeCount;
        $this->publishedAt           = $item->snippet->topLevelComment->snippet->publishedAt;
        $this->updatedAt             = $item->snippet->topLevelComment->snippet->updatedAt;

        return $this;
    }

}
