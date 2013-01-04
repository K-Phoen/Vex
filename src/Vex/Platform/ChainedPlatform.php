<?php

namespace Vex\Platform;

use Vex\Exception\VideoNotFoundException;


class ChainedPlatform implements PlatformInterface
{
    /**
     * @var array
     */
    protected $platforms = array();


    /**
     * Constructor
     *
     * @param array $platforms
     */
    public function __construct(array $platforms = array())
    {
        $this->platforms = $platforms;
    }

    /**
     * Add a platform
     *
     * @param PlatformInterface $platform
     */
    public function addPlatform(PlatformInterface $platform)
    {
        $this->platforms[] = $platform;
    }

    public function support($url)
    {
        foreach ($this->platforms as $platform) {
            if ($platform->support($url)) {
                return true;
            }
        }

        return false;
    }

    public function extract($url, array $options = array())
    {
        foreach ($this->platforms as $platform) {
            if (!$platform->support($url)) {
                continue;
            }

            try {
                return $platform->extract($url, $options);
            } catch (VideoNotFoundException $e) {
                // do nothing and try another platform
            }
        }

        throw new VideoNotFoundException('No platform could retrieve the video at: ' . $url);
    }

    public function getName()
    {
        return 'chained';
    }
}
