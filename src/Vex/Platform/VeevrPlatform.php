<?php

namespace Vex\Platform;

use Vex\Exception\VideoNotFoundException;


class VeevrPlatform extends AbstractPlatform
{
    const HTML_TMPL = '<iframe src="http://veevr.com/embed/%s" width="%d" height="%d" scrolling="no" frameborder="0"></iframe>';
    const VIDEO_ID_REGEX = '<meta property="og:url" content="http://veevr.com/videos/(\w+)" />';
    const TITLE_REGEX = '`<meta property="og:title" content="([^"]+)" />`';
    const THUMB_REGEX = '`<meta property="og:image" content="([^"]+)" />`';


    public function support($url)
    {
        return strpos($url, 'veevr.com') !== false;
    }

    public function extract($url, array $options = array())
    {
        $options = array_merge($this->getDefaultOptions(), $options);
        $video_data = array('link' => $url);

        $content = $this->getContent($url);
        $video_data['embed_code'] = sprintf(self::HTML_TMPL, $this->findId($content), $options['width'], $options['height']);

        // retrieve the video's title
        if (array_key_exists('with_title', $options) && $options['with_title']) {
            $video_data['title'] = $this->searchRegex(self::TITLE_REGEX, $content);
        }

        // retrieve the thumbnail url
        if (array_key_exists('with_thumb', $options) && $options['with_thumb']) {
            $video_data['thumb'] = $this->searchRegex(self::THUMB_REGEX, $content);
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

    public function getName()
    {
        return 'veevr';
    }
}
