# Menu

Menu is menu builder for PHP.


![Version](http://img.shields.io/packagist/v/lavary/menu.svg?style=flat-square)
![Build](http://img.shields.io/travis/lavary/menu.svg?style=flat-square)



To install it, use composer:

```bash
composer require lavary/menu
```

You can create a menu by using `make()` method. Here's a basic usage:

```php
<?php

use Lavary\Menu;

$factory = new Menu();

$factory->make('MyNavBar', function($menu) {
  $menu->add('Home');
  $menu->add('About', 'about');
  $menu->add('services', 'services');
  $menu->add('Contact', 'contact');
})
```

Please checkout the [documentation](https://github.com/lavary/menu/wiki) to learn about all the features.


## If You Need Help

Please submit all issues and questions using GitHub issues and I will try to help you.


## License

*Menu* is free software distributed under the terms of the MIT license.
