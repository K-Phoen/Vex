<?php

namespace Vex\Platform;

use Vex\Exception\VideoNotFoundException;


class TagTelePlatform extends AbstractPlatform
{
     const HTML_TMPL = '<object width="425" height="350"><param name="movie" value="http://www.tagtele.com/v/%s"></param><param name="wmode" value="transparent"></param><embed src="http://www.tagtele.com/v/%s" type="application/x-shockwave-flash" wmode="transparent" width="425" height="350"></embed></object>';
    const THUMB_REGEX = '`<meta property="og:image" content="([^"]+)" />`';


    public function support($url)
    {
        return strpos($url, 'tagtele.com') !== false;
    }

    public function extract($url)
    {
        $video_data = array('link' => $url);

        $data = array_filter(explode('/', $url));
        if (empty($data[count($data) - 1])) {
            throw new VideoNotFoundException('Impossible to retrieve the video\'s ID');
        }

        $id = array_pop($data);
        $video_data['embed_code'] = sprintf(self::HTML_TMPL, $id, $id);

        $content = $this->getUrlContent($url);

        // retrieve the thumbnail url
        if (preg_match(self::THUMB_REGEX, $content, $matches)) {
            $video_data['thumb'] = $matches[1];
        }

        return $video_data;
    }

    public function getName()
    {
        return 'tagtele';
    }
}
