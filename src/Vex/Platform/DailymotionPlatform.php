<?php

namespace Vex\Platform;

use Vex\Exception\VideoNotFoundException;


class DailymotionPlatform extends AbstractPlatform
{
    const HTML_TMPL = '<iframe frameborder="0" width="%d" height="%d" src="http://www.dailymotion.com/embed/video/%s"></iframe>';
    const TITLE_REGEX = '`<meta property="og:title" content="([^"]+)" />`';
    const THUMB_REGEX = '`<meta property="og:image" content="([^"]+)" />`';
    const DURATION_REGEX = '`<meta property="video:duration" content="(\d+)" />`';


    public function support($url)
    {
        return strpos($url, 'dailymotion.com') !== false;
    }

    public function extract($url, array $options = array())
    {
        $options = array_merge($this->getDefaultOptions(), $options);
        $video_data = array(
            'link'       => $url,
            'embed_code' => sprintf(self::HTML_TMPL, $options['width'], $options['height'], $this->findId($url)),
        );

        $find_title = array_key_exists('with_title', $options) && $options['with_title'];
        $find_thumb = array_key_exists('with_thumb', $options) && $options['with_thumb'];
        $find_duration = array_key_exists('with_duration', $options) && $options['with_duration'];

        if ($find_duration || $find_thumb || $find_title) {
            $content = $this->getContent($url);
        }

        // retrieve the video's title
        if ($find_title) {
            $video_data['title'] = $this->searchRegex(self::TITLE_REGEX, $content);
        }

        // retrieve the thumbnail url
        if ($find_thumb) {
            $video_data['thumb'] = $this->searchRegex(self::THUMB_REGEX, $content);
        }

        // retrieve the duration
        if ($find_duration) {
            $duration = $this->searchRegex(self::DURATION_REGEX, $content);
            $video_data['duration'] = $duration !== null ? (int) $duration : null;
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
