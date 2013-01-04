<?php

namespace Vex\HttpAdapter;

use Buzz\Browser;


/**
 * @author William Durand <william.durand1@gmail.com>
 */
class BuzzHttpAdapter implements HttpAdapterInterface
{
    /**
     * @var Browser
     */
    protected $browser;

    /**
     * @param Browser $browser Browser object
     */
    public function __construct(Browser $browser = null)
    {
        $this->browser = null === $browser ? new Browser() : $browser;
    }

    /**
     * {@inheritDoc}
     */
    public function getContent($url)
    {
        try {
            $response = $this->browser->get($url);
            $content  = $response->getContent();
        } catch (\Exception $e) {
            $content = null;
        }

        return $content;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'buzz';
    }
}
