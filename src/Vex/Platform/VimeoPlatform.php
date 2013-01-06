<?php

namespace Vex\Platform;

use Vex\Exception\VideoNotFoundException;


class VimeoPlatform extends AbstractPlatform
{
    const HTML_TMPL = '<iframe src="http://player.vimeo.com/video/%s" width="%d" height="%d" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
    const TITLE_REGEX = '`<meta property="og:title" content="([^"]+)">`';
    const THUMB_REGEX = '`<meta property="og:image" content="([^"]+)">`';
    const DURATION_REGEX = '`<meta itemprop="duration" content="([^"]+)">`';


    public function support($url)
    {
        return strpos($url, 'vimeo.com') !== false;
    }

    public function extract($url, array $options = array())
    {
        $options = array_merge($this->getDefaultOptions(), $options);
        $video_data = array(
            'link'       => $url,
            'embed_code' => sprintf(self::HTML_TMPL, $this->findId($url), $options['width'], $options['height']),
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
            if ($duration !== null) {
                $video_data['duration'] = $this->intervalToSeconds($duration);
            }
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

    protected function intervalToSeconds($interval)
    {
        $interval = new \DateInterval($interval);
        return $interval->h * 60 * 60 +
               $interval->i * 60 +
               $interval->s;
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
