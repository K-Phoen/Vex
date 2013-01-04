<?php

namespace Vex\Platform;

use Vex\Exception\VideoNotFoundException;


class VeevrPlatform extends AbstractPlatform
{
    const HTML_TMPL = '<iframe src="http://veevr.com/embed/%s" width="640" height="360" scrolling="no" frameborder="0"></iframe>';
    const VIDEO_ID_REGEX = '<meta property="og:url" content="http://veevr.com/videos/(\w+)" />';
    const THUMB_REGEX = '`<meta property="og:image" content="([^"]+)" />`';


    public function support($url)
    {
        return strpos($url, 'veevr.com') !== false;
    }

    public function extract($url, array $options = array())
    {
        $video_data = array('link' => $url);

        $content = $this->getContent($url);
        $video_id = $this->findId($content);
        $video_data['embed_code'] = sprintf(self::HTML_TMPL, $video_id, $video_id);

        // retrieve the thumbnail url
        if (array_key_exists('with_thumb', $options) && $options['with_thumb']) {
            $video_data['thumb'] = $this->findThumb($content);
        }

        return $this->returnData($video_data);
    }

    protected function findId($page)
    {
        if (!preg_match(self::VIDEO_ID_REGEX, $page, $matches)) {
            throw new VideoNotFoundException('Impossible to retrieve the video\'s ID');
        }

        return $matches[1];
    }

    protected function findThumb($page)
    {
        if (preg_match(self::THUMB_REGEX, $page, $matches)) {
            return $matches[1];
        }

        return null;
    }

    public function getName()
    {
        return 'veevr';
    }
}
