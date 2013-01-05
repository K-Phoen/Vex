<?php

namespace Vex\Platform;

use Vex\Exception\VideoNotFoundException;


class YoutubePlatform extends AbstractPlatform
{
    const HTML_TMPL = '<iframe width="560" height="315" src="http://www.youtube.com/embed/%s" frameborder="0" allowfullscreen></iframe>';
    const THUMB_REGEX = '`<meta property="og:image" content="([^"]+)">`';


    public function support($url)
    {
        return strpos($url, 'youtube.') !== false || strpos($url, 'youtu.be') !== false;
    }

    public function extract($url, array $options = array())
    {
        $video_data = array(
            'link'       => $url,
            'embed_code' => sprintf(self::HTML_TMPL, $this->findId($url)),
        );

        // retrieve the thumbnail url
        if (array_key_exists('with_thumb', $options) && $options['with_thumb']) {
            $content = $this->getContent($url);
            $video_data['thumb'] = $this->findThumb($content);
        }

        return $this->returnData($video_data);
    }

    protected function findId($url)
    {
        $data = parse_url($url);

        if (empty($data['host'])) {
            throw new VideoNotFoundException('Impossible to retrieve the video\'s ID');
        }

        $query = array();
        if (isset($data['query'])) {
            parse_str($data['query'], $query);
        }

        if (false !== strpos($data['host'], 'youtube.')
            && in_array($data['path'], array('/watch', '/all_comments'))
            && isset($query['v'])
            && preg_match('#^[\w-]{11}$#', $query['v'])
        ) {
            $id = $query['v'];
        } elseif (false !== strpos($data['host'], 'youtu.be')
            && preg_match('#^/?[\w-]{11}/?$#', $data['path'])
        ) {
            $id = trim($data['path'], '/');
        } elseif (false != preg_match('/^www\.youtube(-nocookie)?\.com$/',$data['host'])
            && preg_match('{^/embed/([\w-]{11})}', $data['path'], $matches)
        ) {
            $id = $matches[1];
        } elseif (false != preg_match('/^www\.youtube(-nocookie)?\.com$/',$data['host'])
            && preg_match('{^/v/([\w-]{11})}', $data['path'], $matches)
        ) {
            $id = $matches[1];
        } elseif (false != preg_match('/^www\.youtube(-nocookie)?\.com$/',$data['host'])
            && preg_match('{^/p/([\w-]{16})}', $data['path'], $matches)
        ) {
            $id = $matches[1];
        } else {
            throw new VideoNotFoundException('Impossible to retrieve the video\'s ID');
        }

        return $id;
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
        return 'youtube';
    }
}
