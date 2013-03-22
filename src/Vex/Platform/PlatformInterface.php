<?php

namespace Vex\Platform;


interface PlatformInterface
{
    public function getName();

    public function support($url);
    public function extract($url, array $options = array());
    public function reverse($embed_code);
}
