<?php

namespace Vex\Platform;

use Vex\Exception\VideoNotFoundException;


class VimeoPlatform extends AbstractPlatform
{
    const API_URL = 'http://vimeo.com/api/v2/video/%s.json';
    const HTML_TMPL = '<iframe src="http://player.vimeo.com/video/%s" width="%d" height="%d" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';


    public function support($url)
    {
        return strpos($url, 'vimeo.com') !== false;
    }

    public function extract($url, array $options = array())
    {
        $options = array_merge($this->getDefaultOptions(), $options);
        $video_data = array('link' => $url);

        $response = json_decode($this->getContent(sprintf(self::API_URL, $this->findId($url))));
        if (!$response || count($response) !== 1) {
            throw new VideoNotFoundException('Impossible to query the API');
        }

        $video_data['embed_code'] = sprintf(self::HTML_TMPL, $response[0]->id, $options['width'], $options['height']);

        // retrieve the video's title
        if (array_key_exists('with_title', $options) && $options['with_title']) {
            $video_data['title'] = $response[0]->title;
        }

        // retrieve the thumbnail url
        if (array_key_exists('with_thumb', $options) && $options['with_thumb']) {
            $video_data['thumb'] = $response[0]->thumbnail_large;
        }

        // retrieve the duration
        if ( array_key_exists('with_duration', $options) && $options['with_duration']) {
            $video_data['duration'] = $response[0]->duration;
        }

        return $this->returnData($video_data);
    }

    protected function findId($url)
    {
        $data = explode('/', $url);
        if (!count($data) || empty($data[3]) || !is_numeric($data[3])) {
            throw new VideoNotFoundException('Impossible to retrieve the video\'s ID');
        }

        return $data[3];
    }

    public function getDefaultOptions()
    {
        return array(
            'width'  => 500,
            'height' => 281
        );
    }

    public function getName()
    {
        return 'vimeo';
    }
}
