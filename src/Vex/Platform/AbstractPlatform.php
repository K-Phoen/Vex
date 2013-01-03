<?php

namespace Vex\Platform;


abstract class AbstractPlatform implements PlatformInterface
{
    protected function getUrlContent($url)
    {
        $session = curl_init($url);
        curl_setopt_array($session, array(
            CURLOPT_RETURNTRANSFER  => 1,
            CURLOPT_FOLLOWLOCATION  => 1,
            CURLOPT_TIMEOUT         => 6,
            CURLOPT_USERAGENT       => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/534.27 (KHTML, like Gecko) Ubuntu/10.10 Chromium/12.0.714.0 Chrome/12.0.714.0 Safari/534.27',
            CURLOPT_HTTPHEADER      => array(
                'Accept-Language: fr-fr,fr;q=0.8,en;q=0.6,en-us;q=0.4,es;q=0.2',
                'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.3',
            )
        ));
        $result = curl_exec($session);
        curl_close($session);

        return $result;
    }
}
