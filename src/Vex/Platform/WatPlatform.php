<?php

namespace Vex\Platform;

use Vex\Exception\VideoNotFoundException;


class WatPlatform extends AbstractPlatform
{
    const HTML_TMPL = '<iframe src="%s" frameborder="0" style="width: %dpx; height: %dpx;"></iframe>';
    const PLAYER_URL_REGEX = '`<meta name="twitter:player" content="([^"]+)">`';
    const TITLE_REGEX = '`<meta property="og:title" content="([^"]+)">`';
    const THUMB_REGEX = '`<meta property="og:image" content="([^"]+)">`';
    const DURATION_REGEX = '`<meta property="video:duration" content="(\d+)">`';

    const REVERSE_EMBED_URL     = '`src="([^"]+)"`';
    const REVERSE_VIDEO_URL     = '`mediaurl : "([^"]+)"`';

    const WAT_BASE_URL = 'http://www.wat.tv';


    public function support($url)
    {
        return strpos($url, 'wat.tv') !== false;
    }

    public function extract($url, array $options = array())
    {
        $options = array_merge($this->getDefaultOptions(), $options);
        $video_data = array('link' => $url);

        $content = $this->getContent($url);
        $video_player_url = $this->findPlayerUrl($content);

        // get the html embed code
        $video_data['embed_code'] = sprintf(self::HTML_TMPL, $video_player_url, $options['width'], $options['height']);

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
        $video_url = $this->searchRegex(self::REVERSE_VIDEO_URL, $this->getContent($url));

        return empty($video_url) ? $video_url : self::WAT_BASE_URL . $video_url;
    }

    protected function findPlayerUrl($page)
    {
        if (!preg_match(self::PLAYER_URL_REGEX, $page, $matches)) {
            throw new VideoNotFoundException('Impossible to retrieve the video\'s player URL');
        }

        return $matches[1];
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
        return 'wat';
    }
}
