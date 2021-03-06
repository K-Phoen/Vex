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

Status
======

This project is **DEPRECATED** and should NOT be used. 

If someone magically appears and wants to maintain this project, I'll gladly give access to this repository.

Installation
============

The recommended way to install Vex is through composer.

```json
{
    "require": {
        "kphoen/vex": "1.1.*"
    }
}
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

$adapter = new \Vex\HttpAdapter\BuzzHttpAdapter();
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


$url = $vex->reverse('<iframe width="640" height="360" src="http://rutube.ru/video/embed/6236741" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowfullscreen scrolling="no"></iframe>');
// shows http://rutube.ru/video/9f4dc6bc2db6b6051ea07fb20234c6cc/
echo $url
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

  * [Kévin Gomez](https://github.com/K-Phoen/)
  * [William Durand](https://github.com/willdurand/) - for the `HttpAdapter` part, which was borrowed from [Geocoder](https://github.com/willdurand/Geocoder)
  * [Jérôme Tamarelle](https://github.com/GromNaN/) - to whom I borrowed this README

License
=======

Vex is released under the MIT License. See the bundled LICENSE file for
details.
