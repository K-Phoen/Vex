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
        $this->addPlatforms($platforms);
    }

    /**
     * Add a platform.
     *
     * @param PlatformInterface $platform
     */
    public function addPlatform(PlatformInterface $platform)
    {
        $this->platforms[] = $platform;
        return $this;
    }

    /**
     * Add several platforms.
     *
     * @param PlatformInterface $platform
     */
    public function addPlatforms(array $platforms)
    {
        foreach ($platforms as $platform) {
            $this->addPlatform($platform);
        }

        return $this;
    }

    /**
     * Returns registered platforms indexed by name.
     *
     * @return PlatformInterface[]
     */
    public function getPlatforms()
    {
        return $this->platforms;
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
