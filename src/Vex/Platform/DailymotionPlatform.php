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

    public function extract($url)
    {
        $video_data = array('link' => $url);

        $data = explode('/', $url);
        if (!count($data) || empty($data[4])) {
            throw new VideoNotFoundException('Impossible to retrieve the video\'s ID');
        }

        $data = explode('_', $data[4]);
        if (!isset($data[0])) {
            throw new VideoNotFoundException('Impossible to retrieve the video\'s ID');
        }
        $video_data['embed_code'] = sprintf(self::HTML_TMPL, $data[0]);

        $content = $this->getUrlContent($url);

        // retrieve the thumbnail url
        if (preg_match(self::THUMB_REGEX, $content, $matches)) {
            $video_data['thumb'] = $matches[1];
        }

        // retrieve the duration
        if (preg_match(self::DURATION_REGEX, $content, $matches)) {
            $video_data['duration'] = $matches[1];
        }

        return $video_data;
    }

    public function getName()
    {
        return 'dailymotion';
    }
}
