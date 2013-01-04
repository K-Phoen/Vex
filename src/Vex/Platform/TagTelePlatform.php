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

    public function extract($url, array $options = array())
    {
        $video_data = array('link' => $url);

        $video_id = $this->findId($url);
        $video_data['embed_code'] = sprintf(self::HTML_TMPL, $video_id, $video_id);

        // retrieve the thumbnail url
        if (array_key_exists('with_thumb', $options) && $options['with_thumb']) {
            $content = $this->getContent($url);
            $video_data['thumb'] = $this->findThumb($content);
        }

        return $this->returnData($video_data);
    }

    protected function findId($url)
    {
        $data = array_filter(explode('/', $url));
        if (empty($data[count($data) - 1])) {
            throw new VideoNotFoundException('Impossible to retrieve the video\'s ID');
        }

        return array_pop($data);
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
        return 'tagtele';
    }
}
