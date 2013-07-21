<?php

namespace Vex\Platform;

use Vex\Exception\VideoNotFoundException;


class RutubePlatform extends AbstractPlatform
{
    const VIDEO_ID_REGEX = '`<link rel="video_src" href="http://rutube.ru/video/embed/(\d+)"/>`';
    const TITLE_REGEX = '`<meta property="og:title" content="([^"]+)" />`';
    const THUMB_REGEX = '`<meta property="og:image" content="([^"]+)" />`';
    const HTML_TMPL = '<iframe width="%d" height="%d" src="http://rutube.ru/video/embed/%s" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowfullscreen scrolling="no"></iframe>';
    const DURATION_REGEX = '`<meta property="video:duration" content="(\d+)" />`';

    const REVERSE_EMBED_URL     = '`src="([^"]+)"`';
    const REVERSE_VIDEO_URL     = '`<link rel="canonical" href="([^"]+)"/>`';


    public function support($url)
    {
        return strpos($url, 'rutube.ru') !== false;
    }

    public function extract($url, array $options = array())
    {
        $options = array_merge($this->getDefaultOptions(), $options);
        $video_data = array('link' => $url);

        $content = $this->getContent($url);
        $video_id = $this->findId($content);

        // get the html embed code
        $video_data['embed_code'] = sprintf(self::HTML_TMPL, $options['width'], $options['height'], $video_id, $video_id);

        // retrieve the video's title
        if (array_key_exists('with_title', $options) && $options['with_title']) {
            $video_data['title'] = $this->searchRegex(self::TITLE_REGEX, $content);
        }

        // retrieve the thumbnail url
        if (array_key_exists('with_thumb', $options) && $options['with_thumb']) {
            $video_data['thumb'] = $this->searchRegex(self::THUMB_REGEX, $content);
        }

        // retrieve the duration
        if (array_key_exists('with_duration', $options) && $options['with_duration']) {
            $duration = $this->searchRegex(self::DURATION_REGEX, $content);
            $video_data['duration'] = $duration !== null ? (int) $duration : null;
        }

        return $this->returnData($video_data);
    }

    public function reverse($embed_code)
    {
        $url = $this->searchRegex(self::REVERSE_EMBED_URL, $embed_code);
        return $this->searchRegex(self::REVERSE_VIDEO_URL, $this->getContent($url));
    }

    protected function findId($page)
    {
        if (!preg_match(self::VIDEO_ID_REGEX, $page, $matches)) {
            throw new VideoNotFoundException('Impossible to retrieve the video\'s ID');
        }

        return $matches[1];
    }

    public function getName()
    {
        return 'rutube';
    }
}
