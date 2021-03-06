<?php

namespace Vex\Platform;

use Vex\Exception\VideoNotFoundException;


class QipPlatform extends AbstractPlatform
{
    const HTML_TMPL = '<object id="smotriComVideoPlayer" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" width="%d" height="%d"><param name="movie" value="http://pics.video.qip.ru/player.swf?file=%3$s&autoStart=false&str_lang=rus&xmlsource=http%%3A%%2F%%2Fpics%%2Esmotri%%2Ecom%%2Fcskins%%2Fblue%%2Fskin%%5Fcolor%%2Exml&xmldatasource=http%%3A%%2F%%2Fpics.video.qip.ru%%2Fskin_ng.xml" /><param name="allowScriptAccess" value="always" /><param name="allowFullScreen" value="true" /><param name="bgcolor" value="#ffffff" /><embed name="smotriComVideoPlayer" src="http://pics.video.qip.ru/player.swf?file=%3$s&autoStart=false&str_lang=rus&xmlsource=http%%3A%%2F%%2Fpics%%2Esmotri%%2Ecom%%2Fcskins%%2Fblue%%2Fskin%%5Fcolor%%2Exml&xmldatasource=http%%3A%%2F%%2Fpics.video.qip.ru%%2Fskin_ng.xml" quality="high" allowscriptaccess="always" allowfullscreen="true" wmode="window" width="%1$d" height="%2$d" type="application/x-shockwave-flash"></embed></object>';
    const TITLE_REGEX = '`<meta property="og:title" content="([^"]+)" />`';
    const THUMB_REGEX = '`<meta property="og:image" content="([^"]+)" />`';
    const DURATION_REGEX = '`<meta property="og:duration" content="(\d+)" />`';


    public function support($url)
    {
        return strpos($url, 'qip.ru') !== false || strpos($url, 'smotri.com') || strpos($url, 'smotri.ru');
    }

    public function extract($url, array $options = array())
    {
        $options = array_merge($this->getDefaultOptions(), $options);
        $video_data = array('link' => $url);

        $video_data['embed_code'] = sprintf(self::HTML_TMPL, $options['width'], $options['height'], $this->findId($url));

        $find_title = array_key_exists('with_title', $options) && $options['with_title'];
        $find_thumb = array_key_exists('with_thumb', $options) && $options['with_thumb'];
        $find_duration = array_key_exists('with_duration', $options) && $options['with_duration'];

        if ($find_duration || $find_thumb || $find_title) {
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

        // retrieve the duration
        if ($find_duration) {
            $duration = $this->searchRegex(self::DURATION_REGEX, $content);
            $video_data['duration'] = $duration !== null ? (int) $duration : null;
        }

        return $this->returnData($video_data);
    }

    protected function findId($url)
    {
        $data = explode('?id=', $url);
        if (count($data) !== 2) {
            throw new VideoNotFoundException('Impossible to retrieve the video\'s ID');
        }

        return $data[1];
    }

    public function getName()
    {
        return 'qip';
    }
}
