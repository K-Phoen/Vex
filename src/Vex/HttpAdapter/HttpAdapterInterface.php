<?php

namespace Vex\HttpAdapter;

/**
 * @author William Durand <william.durand1@gmail.com>
 */
interface HttpAdapterInterface
{
    /**
     * Returns the content fetched from a given URL.
     *
     * @param string $url
     *
     * @return string
     */
    public function getContent($url);

    /**
     * Returns the name of the HTTP Adapter.
     *
     * @return string
     */
    public function getName();
}
