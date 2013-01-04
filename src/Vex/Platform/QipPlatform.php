<?php

namespace Vex\Platform;

use Vex\Exception\VideoNotFoundException;


class QipPlatform extends AbstractPlatform
{
    const HTML_TMPL = '<object id="smotriComVideoPlayer" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" width="640" height="360"><param name="movie" value="http://pics.video.qip.ru/player.swf?file=%s&autoStart=false&str_lang=rus&xmlsource=http%%3A%%2F%%2Fpics%%2Esmotri%%2Ecom%%2Fcskins%%2Fblue%%2Fskin%%5Fcolor%%2Exml&xmldatasource=http%%3A%%2F%%2Fpics.video.qip.ru%%2Fskin_ng.xml" /><param name="allowScriptAccess" value="always" /><param name="allowFullScreen" value="true" /><param name="bgcolor" value="#ffffff" /><embed name="smotriComVideoPlayer" src="http://pics.video.qip.ru/player.swf?file=%s&autoStart=false&str_lang=rus&xmlsource=http%%3A%%2F%%2Fpics%%2Esmotri%%2Ecom%%2Fcskins%%2Fblue%%2Fskin%%5Fcolor%%2Exml&xmldatasource=http%%3A%%2F%%2Fpics.video.qip.ru%%2Fskin_ng.xml" quality="high" allowscriptaccess="always" allowfullscreen="true" wmode="window" width="640" height="360" type="application/x-shockwave-flash"></embed></object>';
    const THUMB_REGEX = '`<meta property="og:image" content="([^"]+)" />`';
    const DURATION_REGEX = '`<meta property="og:duration" content="(\d+)" />`';


    public function support($url)
    {
        return strpos($url, 'qip.ru') !== false || strpos($url, 'smotri.com') || strpos($url, 'smotri.ru');
    }

    public function extract($url, array $options = array())
    {
        $video_data = array('link' => $url);

        $video_id = $this->findId($url);
        $video_data['embed_code'] = sprintf(self::HTML_TMPL, $video_id, $video_id);

        $find_thumb = array_key_exists('with_thumb', $options) && $options['with_thumb'];
        $find_duration = array_key_exists('with_duration', $options) && $options['with_duration'];

        if ($find_duration || $find_thumb) {
            $content = $this->getContent($url);
        }

        // retrieve the thumbnail url
        if ($find_thumb) {
            $video_data['thumb'] = $this->findThumb($content);
        }

        // retrieve the duration
        if ($find_duration) {
            $video_data['duration'] = $this->findDuration($content);
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

    protected function findThumb($page)
    {
        if (preg_match(self::THUMB_REGEX, $page, $matches)) {
            return $matches[1];
        }

        return null;
    }

    protected function findDuration($page)
    {
        if (preg_match(self::DURATION_REGEX, $page, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    public function getName()
    {
        return 'qip';
    }
}
