This module provides simple way to allow social login/registration for your site.

It creates a new page called /social-login/ (you can move/rename that later),
which does quick social login. It also adds social_id field to user template,
that is used to match with the social account.

This module uses the HybridAuth PHP library (https://github.com/hybridauth/hybridauth):
HybridAuth goal is to act as an abstract API between your application and various
social APIs and identities providers such as Facebook, Twitter and Google.


INSTALL INSTRUCTIONS

1. Install the module
2. If the automatic copy fails, copy social-login.php and hybridauth.php to your /site/templates/ folder
3. Create a new app from Facebook Developers site: https://developers.facebook.com/apps/
4. Edit the module and add Facebook App ID and App Secret
5. Start logging in by visiting the www.yoursite.com/social-login/