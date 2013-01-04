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
}
