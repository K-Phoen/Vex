<?php

namespace Vex\Platform;

use Vex\Exception\VideoNotFoundException;


class VeevrPlatform extends AbstractPlatform
{
    const HTML_TMPL = '<iframe src="http://veevr.com/embed/%s" width="640" height="360" scrolling="no" frameborder="0"></iframe>';
    const ID_REGEX = '<meta property="og:url" content="http://veevr.com/videos/(\w+)" />';
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
            throw new VideoNotFoundException('Impossible to retrieve the video\'s ID');
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
