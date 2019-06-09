# Sign In with Apple for WordPress

A WordPress plugin to configure and enable [Sign In with Apple](https://developer.apple.com/sign-in-with-apple/) for existing WordPress users.

**Important:** This pugin is currently work in progress! Please don't use it on production sites.


## Requirements

- [Apple Developer Account](https://developer.apple.com/programs/enroll/) ($99 per year) for registering your site with Apple.

- [Configured Sign In with Apple for the web](https://help.apple.com/developer-account/#/dev1c0e25352) -- new or existing [App ID](https://developer.apple.com/account/resources/identifiers/list/bundleId) for iOS _or_ macOS with "Sign In with Apple" capability enabled and a [Service ID](https://developer.apple.com/account/resources/identifiers/list/serviceId) (such as `com.yourdomain.signinwithappleplugin`) with "Sign In with Apple" service enabled.

- Specify the `Return URL` as your WordPress login URL such as `https://example.com/wp-login.php`.


## Install

The plugin must be installed as [a Composer dependency](https://packagist.org/packages/kasparsd/sign-in-with-apple) since it uses a third party package for parsing JSON Web Token (JWT) responses:

	composer require kasparsd/sign-in-with-apple


## Configuration

1. Visit the site options at `https://example.com/wp-admin/options.php` and specify your Service ID (such as `com.yourdomain.applelogin`) in `siwa_plugin_service_id`.

2. Visit your WordPress profile `https://example.com/wp-admin/profile.php` and click on the "Sign In with Apple" button to associate your WordPress user with your Apple ID.

3. Use the "Log In with Apple" button at the bottom of the WordPress login page to log-in using your Apple ID.


## To Do

- [ ] Add `id_token` validation based on the [Apple public key](https://developer.apple.com/documentation/signinwithapplerestapi/fetch_apple_s_public_key_for_verifying_token_signature).
- [ ] Enable new user signup for WooCommerce customers.


## Known Issues

- [ ] Apple currently doesn't include user name and email in their JWT `id_token`. The only available field is the user ID in the `sub` field.


## Credits

Created by [Kaspars Dambis](https://kaspars.net).
