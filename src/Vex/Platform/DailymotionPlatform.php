<?php

namespace Vex\Platform;

use Vex\Exception\VideoNotFoundException;


class DailymotionPlatform extends AbstractPlatform
{
    const HTML_TMPL = '<iframe frameborder="0" width="560" height="315" src="http://www.dailymotion.com/embed/video/%s"></iframe>';
    const THUMB_REGEX = '`<meta property="og:image" content="([^"]+)" />`';
    const DURATION_REGEX = '`<meta property="video:duration" content="(\d+)" />`';


    public function support($url)
    {
        return strpos($url, 'dailymotion.com') !== false;
    }

    public function extract($url, array $options = array())
    {
        $video_data = array(
            'link'       => $url,
            'embed_code' => sprintf(self::HTML_TMPL, $this->findId($url)),
        );

        $find_thumb = array_key_exists('with_thumb', $options) && $options['with_thumb'];
        $find_duration = array_key_exists('with_duration', $options) && $options['with_duration'];

        if ($find_duration || $find_thumb) {
            $content = $this->getContent($url);
        }

        // retrieve the thumbnail url
        if ($find_thumb) {
            $video_data['thumb'] = $this->findThumb($content);
        }

        // retrieve the duration
        if ($find_duration) {
            $video_data['duration'] = $this->findDuration($content);
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
        if (!isset($data[0])) {
            throw new VideoNotFoundException('Impossible to retrieve the video\'s ID');
        }

        return $data[0];
    }

    protected function findThumb($page)
    {
        if (preg_match(self::THUMB_REGEX, $page, $matches)) {
            return $matches[1];
        }

        return null;
    }

    protected function findDuration($page)
    {
        if (preg_match(self::DURATION_REGEX, $page, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    public function getName()
    {
        return 'dailymotion';
    }
}
