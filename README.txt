----------------
- Social Login -
----------------

This module provides simple way to allow social login, standard login, registration and user profile for your site.

It creates a new page called /social-login/ which does quick social login or standard.
It creates also a page called /register/ in which a new user can create an account for the site.
It adds social_id field to user template, that is used to match with the social account,
as well a oauth field in which are saved all the social info of the user. 

This module uses the HybridAuth PHP library (https://github.com/hybridauth/hybridauth):
HybridAuth goal is to act as an abstract API between your application and various
social APIs and identities providers such as Facebook, Twitter and Google.


INSTALL INSTRUCTIONS

1. Install the module
2. If the automatic copy fails, copy social-login.php and register.php from this templates folder to your /site/templates/ folder
3. Enable fields to be shown under the profile and the register page


To test an example of social login:

1. Create a new app from Facebook Developers site: https://developers.facebook.com/apps/
2. Edit the module enabling Facebook and add the App ID and the App Secret
3. Start logging in by visiting the www.yoursite.com/social-login/