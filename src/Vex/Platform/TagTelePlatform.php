<?php

namespace Vex\Platform;

use Vex\Exception\VideoNotFoundException;


class TagTelePlatform extends AbstractPlatform
{
     const HTML_TMPL = '<object width="%1$d" height="%2$d"><param name="movie" value="http://www.tagtele.com/v/%3$s"></param><param name="wmode" value="transparent"></param><embed src="http://www.tagtele.com/v/%3$s" type="application/x-shockwave-flash" wmode="transparent" width="%1$d" height="%2$d"></embed></object>';
    const TITLE_REGEX = '`<meta property="og:title" content="([^"]+)" />`';
    const THUMB_REGEX = '`<meta property="og:image" content="([^"]+)" />`';


    public function support($url)
    {
        return strpos($url, 'tagtele.com') !== false;
    }

    public function extract($url, array $options = array())
    {
        $options = array_merge($this->getDefaultOptions(), $options);
        $video_data = array('link' => $url);

        $video_id = $this->findId($url);
        $video_data['embed_code'] = sprintf(self::HTML_TMPL, $options['width'], $options['height'], $video_id);

        $find_title = array_key_exists('with_title', $options) && $options['with_title'];
        $find_thumb = array_key_exists('with_thumb', $options) && $options['with_thumb'];

        if ($find_thumb || $find_title) {
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

    public function getDefaultOptions()
    {
        return array(
            'width'  => 425,
            'height' => 350
        );
    }

    public function getName()
    {
        return 'tagtele';
    }
}
