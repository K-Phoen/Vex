<?php

namespace Vex\Result;


class Video
{
    protected $duration = null;
    protected $thumb = null;
    protected $link = null;
    protected $code = null;


    public function __construct(array $data = array())
    {
        $this->fromArray($data);
    }

    public function getDuration()
    {
        return $this->duration;
    }

    public function getThumb()
    {
        return $this->thumb;
    }

    public function getLink()
    {
        return $this->link;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function fromArray(array $data = array())
    {
        if (isset($data['link'])) {
            $this->link = $data['link'];
        }

        if (isset($data['thumb'])) {
            $this->thumb = $data['thumb'];
        }

        if (isset($data['duration'])) {
            $this->duration = (int) $data['duration'];
        }

        if (isset($data['embed_code'])) {
            $this->code = $data['embed_code'];
        }
    }
}
