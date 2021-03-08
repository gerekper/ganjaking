<?php

namespace YoutubeFeed\Api;


use Instagram\Includes\WIS_Plugin;
use YoutubeFeed\Api\Channel\YoutubeChannelResponse;
use YoutubeFeed\Api\Exceptions\YoutubeException;
use YoutubeFeed\Api\Video\YoutubeComment;
use YoutubeFeed\Api\Video\YoutubeVideosResponse;

class YoutubeApi
{
    public $apiKey;

    public function __construct()
    {
        $this->apiKey = WIS_Plugin::app()->getOption( WYT_API_KEY_OPTION_NAME, '');
    }

    const basePath = "https://www.googleapis.com/youtube/v3";
    const searchPath = self::basePath . "/search?";
    const videosPath = self::basePath . "/videos?";
    const searchChannelPath = self::basePath . "/channels?";
    const commentsPath = self::basePath . "/commentThreads?";

    const orderByUnspec = 'searchSortUnspecified';
    const orderByDate = 'date';
    const orderByRating = 'rating';
    const orderByViewCount = 'viewCount';
    const orderByRelevance = 'relevance';
    const orderByTitle = 'title';
    const orderByVideoCount = 'videoCount';


    /**
     * @param string $channelId [identifier of channel]
     * @param int $maxItems [items in result]
     * @param string $orderBy [how will be ordered]
     *
     * @return false|YoutubeVideosResponse
     */
    public function getVideos($channelId, $maxItems, $orderBy = self::orderByRelevance)
    {
        $path = self::searchPath . http_build_query([
                'key'        => $this->apiKey,
                'channelId'  => $channelId,
                'part'       => 'id',
                'type'       => 'video',
                'order'      => $orderBy,
                'maxResults' => $maxItems,
            ]);

        if(!($json = $this->makeRequest($path))){
            return false;
        }

        return new YoutubeVideosResponse($json);
    }

    /**
     * @param array $videosIds
     *
     * @return false|\YoutubeFeed\Api\Video\YoutubeVideosResponse
     */
    public function getVideosData($videosIds){
        $path = self::videosPath . http_build_query([
                'key'        => $this->apiKey,
                'id'         => implode(',', $videosIds),
                'part'       => 'snippet,statistics',
                'type'       => 'video',
            ]);

        if(!($json = $this->makeRequest($path))){
            return false;
        }

        return new YoutubeVideosResponse($json);
    }


    /**
     * @param string $name
     * @param int $maxResults
     * @param string $orderBy
     * @return false|YoutubeChannelResponse
     */
    public function findUserByName($name, $maxResults = 20 ,$orderBy = self::orderByRelevance)
    {
        $path = self::searchPath . http_build_query([
                'key'   => $this->apiKey,
                'q'     => $name,
                'part'  => 'id,snippet',
                'type'  => 'channel',
                'maxResults' => $maxResults,
                'order' => $orderBy,
            ]);
        if(!($json = $this->makeRequest($path))){
            return false;
        }
        return new YoutubeChannelResponse($json);
    }

    /**
     * @param $id
     * @return false|YoutubeChannelResponse
     */
    public function getUserById($id) {
        $path = self::searchChannelPath . http_build_query([
                'key'  => $this->apiKey,
                'part' => 'id,snippet,statistics',
                'id'   => $id
            ]);

        if(!($json = $this->makeRequest($path))){
            return false;
        }

        return new YoutubeChannelResponse($json);
    }

    public function getCommentsByVideoId($videoId) {
        $path = self::commentsPath . http_build_query([
                'key'       => $this->apiKey,
                'part'      => 'snippet',
                'videoId'   => $videoId,
                'order'     => self::orderByRelevance,
                'maxResults' => 5
            ]);

        if(!($json = $this->makeRequest($path))){
            return false;
        }

        $data = json_decode($json);

        $comments = [];
        foreach ($data->items as $item) {
            $comments[] = new YoutubeComment($item);
        }

        return $comments;
    }

    /**
     * @param $path
     *
     * @return bool|mixed
     */
    private function makeRequest($path)
    {
        $response = wp_remote_get($path);
        if(is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200){
            error_log($response['body']);
            throw new YoutubeException($response['body']);
        } else{
            return $response['body'];
        }
    }

	public function setApiKey( $key ) {
		if($key){
			$this->apiKey = $key;
		}
    }
}
