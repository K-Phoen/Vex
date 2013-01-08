---
layout: layout
title: Vex
description: Parse any video URL
---

Vex - Parse any video URL [![Build Status](https://travis-ci.org/K-Phoen/Vex.png?branch=master)](https://travis-ci.org/K-Phoen/Vex)
=========================

**Vex** is a PHP library to extract a video from any URL.

Supported Services
------------------

For each video-sharing website of the following list, a _Platform_ class can identify
a supported URL and extract the video data.

* [Youtube](http://www.youtube.com/)
* [Dailymotion](http://www.dailymotion.com/)
* [Vimeo](http://vimeo.com/)
* [Wat](http://wat.tv/)
* [Rutube](http://rutube.ru/)
* [Qip](http://qip.ru/)
* [Veevr](http://veevr.com/)
* [TagTele](http://www.tagtele.com/)
* ... more to come

Installation
============

The recommended way to install Vex is through composer.

Just create a `composer.json` file for your project:

```json
{
    "require": {
        "kphoen/vex": "dev-master"
    }
}
```

And run these two commands to install it:

```bash
$ wget http://getcomposer.org/composer.phar
$ php composer.phar install
```


Now you can add the autoloader, and you will have access to the library:

```php
require 'vendor/autoload.php';
```

If you don't use neither **Composer** nor a _ClassLoader_ in your application, just require the provided autoloader:

```php
require_once 'src/autoload.php';
```

You're done.

Usage
=====

```php
use Vex\Vex;

$http_adapter = new \Vex\HttpAdapter\BuzzHttpAdapter();
$platform = \Vex\Platform\RutubePlatform($adapter);
$vex = new Vex($platform);

$video = $vex->extract('https://rutube.ru/video/b5a392c180ddfe3e1ebded38f9f9dc52/');

// Show the video title
echo $video->getTitle();
// Shows the embedded video HTML
echo $video->getCode();
// Show the video link
echo $video->getLink();
// Show the video duration
echo $video->getDuration();
// Show the video thumbnail
echo $video->getThumb();
```

Tests
=====

To run unit tests, you'll need cURL and a set of dependencies you can install
using Composer:

```bash
php composer.phar install --dev
```

Once installed, just launch the following command:

```bash
phpunit
```

Credits
=======

  * Kévin Gomez
  * [William Durand](https://github.com/willdurand/) - for the `HttpAdapter` part, which was borrowed from [Geocoder](https://github.com/willdurand/Geocoder)
  * [Jérôme Tamarelle](https://github.com/GromNaN/) - to whom I borrowed this README

License
=======

Vex is released under the MIT License. See the bundled LICENSE file for
details.
