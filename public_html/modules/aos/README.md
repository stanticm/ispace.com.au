Animate on Scroll
=================

INTRODUCTION
------------

Animate On Scroll (AOS) library allows you to animate elements as you scroll
down, and up.If you scroll back to top, elements will animate to it's previous
state and are ready to animate again if you scroll down.This module provides
integration with AOS library.

* For a full description of the module, visit the project page:
  https://www.drupal.org/project/aos
* To submit bug reports and feature suggestions, or to track changes:
  https://www.drupal.org/project/issues/aos

REQUIREMENTS
------------

This module requires no modules outside of Drupal core.

INSTALLATION
------------

Install the AOS module as you would normally install a contributed Drupal
module. Visit https://www.drupal.org/node/1897420 for further information.

By default AOS library is loaded from CDN. If you prefer to install it
locally download and unpack the library to the libraries directory. Make sure
the path to the library becomes: libraries/aos.

If you are using Composer for downloading third-party libraries turn off
the 'minified' setting as asset-packagist.org does not provide minified files.

See https://www.drupal.org/node/2718229/#third-party-libraries

USAGE
------------
Simply add `data-aos` attribute to element, for example:
`<div data-aos="animation_name"></div>` in your html.

```
<div data-aos="fade-zoom-in"
data-aos-offset="200"
data-aos-easing="ease-in-sine"
data-aos-duration="600">
</div>
```

 You can check all available animations & easing options at
 https://github.com/michalsnik/aos
