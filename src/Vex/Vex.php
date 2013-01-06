<?php

namespace Vex;

use Vex\Platform\PlatformInterface;
use Vex\Result\Video;


class Vex
{
    protected $platforms = array();
    protected $platform = null;
    protected $options = array();


    public function __construct(PlatformInterface $platform = null, array $options = array())
    {
        $this->platform = $platform;
        $this->options = $options;
    }

    public function extract($url, array $options = array())
    {
        if (empty($url)) {
            return null;
        }

        $video_data = $this->getPlatform()->extract($url, array_merge($this->options, $options));
        return new Video($video_data);
    }

    public function setOption($key, $value)
    {
        $this->options[$key] = $value;
        return $this;
    }

    public function setOptions(array $options = array())
    {
        $this->options = array_merge($this->options, $options);
        return $this;
    }

    public function getOption($key)
    {
        if (!array_key_exists($key, $this->options)) {
            throw new \RuntimeException(sprintf('Option %s is not defined', $key));
        }

        return $this->options[$key];
    }

    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Add a platform.
     *
     * @param PlatformInterface $platform
     */
    public function addPlatform(PlatformInterface $platform)
    {
        $this->platforms[$platform->getName()] = $platform;
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
     * Sets the platform to use.
     *
     * @param string $name A platform's name
     *
     * @return Vex
     */
    public function using($name)
    {
        if (isset($this->platforms[$name])) {
            $this->platform = $this->platforms[$name];
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

    /**
     * Returns the platform to use.
     *
     * @return PlatformInterface
     */
    public function getPlatform()
    {
        if (null === $this->platform) {
            if (0 === count($this->platforms)) {
                throw new \RuntimeException('No platform registered.');
            } else {
                $this->platform = $this->platforms[key($this->platforms)];
            }
        }

        return $this->platform;
    }
}
