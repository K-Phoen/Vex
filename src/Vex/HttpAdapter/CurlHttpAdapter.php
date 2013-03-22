<?php

namespace Vex\HttpAdapter;

use Vex\Exception\ExtensionNotLoadedException;

/**
 * @author William Durand <william.durand1@gmail.com>
 */
class CurlHttpAdapter implements HttpAdapterInterface
{
    protected $curl_opts = array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_FOLLOWLOCATION => true,
    );


    public function __construct(array $curl_opts = array())
    {
        $this->curl_opts = $this->curl_opts + $curl_opts;
    }

    /**
     * {@inheritDoc}
     */
    public function getContent($url)
    {
        if (!function_exists('curl_init')) {
            throw new ExtensionNotLoadedException('cURL has to be enabled.');
        }

        $c = curl_init();
        foreach ($this->curl_opts as $opt => $value) {
            curl_setopt($c, $opt, $value);
        }
        curl_setopt($c, CURLOPT_URL, $url);
        $content = curl_exec($c);
        curl_close($c);

        if (false === $content) {
            $content = null;
        }

        return $content;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'curl';
    }
}
