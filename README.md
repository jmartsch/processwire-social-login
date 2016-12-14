ProcessWire Social Login
==================

This module provides simple way to allow social login via HybridAuth or a standard login

It also offers a registration form and the ability to edit your user profile on your website.

This module was originally written by [Mauro Mascia](https://bitbucket.org/mauro_mascia/processwire-social-login).

Compatibility for ProcessWire 3 and latest HybridAuth was added by [Jens Martsch](https://github.com/jmartsch/processwire-social-login)


It creates a new page called /social-login/ which does quick social login or standard.
It creates also a page called /register/ in which a new user can create an account for the site.
It adds social_id field to user template, that is used to match with the social account,
as well a oauth field in which are saved all the social info of the user. 

This module uses the [HybridAuth PHP library](https://github.com/hybridauth/hybridauth):
HybridAuth goal is to act as an abstract API between your application and various
social APIs and identities providers such as Facebook, Twitter and Google.


##Installation

1. Install the module just like any other ProcessWire module. Check out the guide [How-To Install or Uninstall Modules](http://modules.processwire.com/install-uninstall/)
2. If the automatic copy fails, copy the files `social-login.php` and `register.php` from `/site/modules/processwire-social-login/templates` folder to your `/site/templates/` folder
3. run `composer install` from inside the module
3. Enable fields to be shown under the profile and the register page
4. Create a new app from Facebook Developers site: https://developers.facebook.com/apps/
5. Go to the modules setting and enable Facebook 
6. Enter your App ID and the App Secret
6. Start logging in by visiting the www.yoursite.com/social-login/