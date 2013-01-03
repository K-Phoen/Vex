<?php

namespace Vex\Platform;


class VeevrPlatform extends AbstractPlatform
{
    const HTML_TMPL = '<iframe src="http://veevr.com/embed/%s" width="640" height="360" scrolling="no" frameborder="0"></iframe>';
    const ID_REGEX = '<link rel="video_src" href="http://video.rutube.ru/(\w+)" />';
    const THUMB_REGEX = '`<meta property="og:image" content="([^"]+)" />`';


    public function support($url)
    {
        return strpos($url, 'veevr.com') !== false;
    }

    public function extract($url)
    {
        $video_data = array('link' => $url);

        $content = $this->getUrlContent($url);
        if (!preg_match(self::ID_REGEX, $content, $matches)) {
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
        return 'veevr';
    }
}
