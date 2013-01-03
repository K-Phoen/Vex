<?php

namespace Vex\Result;


interface VideoResultInterface
{
    public function getLink();
    public function getDuration();
    public function getThumb();
    public function getCode();

    public function fromArray(array $data = array());
}
