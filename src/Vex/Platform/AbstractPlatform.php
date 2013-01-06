<?php

namespace Vex\Platform;

use Vex\HttpAdapter\HttpAdapterInterface;


abstract class AbstractPlatform implements PlatformInterface
{
    /**
     * @var HttpAdapterInterface
     */
    protected $adapter = null;


    /**
     * @param HttpAdapterInterface $adapter An HTTP adapter.
     */
    public function __construct(HttpAdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Returns the HTTP adapter.
     *
     * @return HttpAdapterInterface
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * Sets the HTTP adapter to be used for further requests.
     *
     * @param HttpAdapterInterface $adapter
     *
     * @return AbstractProvider
     */
    public function setAdapter($adapter)
    {
        $this->adapter = $adapter;
        return $this;
    }

    protected function getContent($url)
    {
        return $this->getAdapter()->getContent($url);
    }

    protected function searchRegex($regex, $content, $default = null)
    {
        if (preg_match($regex, $content, $matches)) {
            return $matches[1];
        }

        return $default;
    }

    protected function returnData(array $data)
    {
        return array_merge($this->getDefaults(), $data);
    }

    protected function getDefaults()
    {
        return array(
            'title'         => null,
            'link'          => null,
            'embed_code'    => null,
            'duration'      => null,
            'thumb'         => null,
        );
    }

    public function getDefaultOptions()
    {
        return array(
            'width'  => 640,
            'height' => 360
        );
    }
}
