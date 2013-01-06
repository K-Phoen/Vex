<?php

namespace Vex\Platform;

use Vex\Exception\VideoNotFoundException;


class DailymotionPlatform extends AbstractPlatform
{
    const API_URL = 'https://api.dailymotion.com/video/%s?fields=title,embed_html,duration,thumbnail_url';
    const HTML_TMPL = '<iframe frameborder="0" width="%d" height="%d" src="http://www.dailymotion.com/embed/video/%s"></iframe>';


    public function support($url)
    {
        return strpos($url, 'dailymotion.com') !== false;
    }

    public function extract($url, array $options = array())
    {
        $options = array_merge($this->getDefaultOptions(), $options);
        $video_id = $this->findId($url);
        $video_data = array(
            'link'       => $url,
            'embed_code' => sprintf(self::HTML_TMPL, $options['width'], $options['height'], $video_id),
        );

        $response = json_decode($this->getContent(sprintf(self::API_URL, $video_id)));
        if (!$response) {
            throw new VideoNotFoundException('Impossible to query the API');
        }

        // retrieve the video's title
        if (array_key_exists('with_title', $options) && $options['with_title']) {
            $video_data['title'] = $response->title;
        }

        // retrieve the thumbnail url
        if (array_key_exists('with_thumb', $options) && $options['with_thumb']) {
            $video_data['thumb'] = $response->thumbnail_url;
        }

        // retrieve the duration
        if ( array_key_exists('with_duration', $options) && $options['with_duration']) {
            $video_data['duration'] = $response->duration;
        }

        return $this->returnData($video_data);
    }

    protected function findId($url)
    {
        $data = explode('/', $url);
        if (!count($data) || empty($data[4])) {
            throw new VideoNotFoundException('Impossible to retrieve the video\'s ID');
        }

        $data = explode('_', $data[4]);
        return $data[0];
    }

    public function getDefaultOptions()
    {
        return array(
            'width'  => 560,
            'height' => 315
        );
    }

    public function getName()
    {
        return 'dailymotion';
    }
}
