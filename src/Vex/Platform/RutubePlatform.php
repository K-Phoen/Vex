<?php

namespace Vex\Platform;


class RutubePlatform extends AbstractPlatform
{
    const VIDEO_ID_REGEX = '`<meta name="twitter:player" value="https://video.rutube.ru/(\d+)" />`';
    const THUMB_REGEX = '`<meta property="og:image" content="([^"]+)" />`';
    const HTML_TMPL = '<iframe width="640" height="360" src="http://rutube.ru/embed/%s" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowfullscreen scrolling="no"></iframe>';


    public function support($url)
    {
        return strpos($url, 'rutube.ru') !== false;
    }

    public function extract($url)
    {
        $video_data = array('link' => $url);

        $content = $this->getUrlContent($url);

        // get the html embed code
        if (!preg_match(self::VIDEO_ID_REGEX, $content, $matches)) {
            throw new \RuntimeException('Impossible to retrieve the video\'s ID');
        }
        $video_data['embed_code'] = sprintf(self::HTML_TMPL, $matches[1], $matches[1]);

        // retrieve the thumbnail url
        if (preg_match(self::THUMB_REGEX, $content, $matches)) {
            $video_data['thumb'] = $matches[1];
        }

        return $video_data;
    }

    public function getName()
    {
        return 'rutube';
    }
}
