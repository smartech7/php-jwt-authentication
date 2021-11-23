## Credits
[This repository it a fork from original tymonsdesigns/jwt-auth](https://github.com/tymondesigns/jwt-auth/wiki), we decided to fork and work independent because the original one was not being updated for long time and keep doing support for the application.

## Migrating from [`tymondesigns/jwt-auth`](https://github.com/tymondesigns/jwt-auth)

This uses different namespace, then `tymondesigns/jwt-auth`, but overall, provides the same API, that makes migration to this repository pretty easy:

1) Run `composer require php-open-source-saver/jwt-auth`
2) Replace all the occurrences of `Tymon\JWTAuth` with `PHPOpenSourceSaver\JWTAuth`.
   > **Tip**: You can use *Find and Replace* feature of your IDE. Try it with <kbd>Ctrl</kbd> + <kbd>Shift</kbd> + <kbd>R</kbd>
3) Run `composer remove tymon/jwt-auth`

### Notes

Due to new features, added in our library, there are some incompatibilities. _This won't hurt you in most cases_, unless you have [implicitly disabled autodiscovery](https://laravel.com/docs/8.x/packages#opting-out-of-package-discovery) for original Tymon's package.

Current compatability breaks:
- [`JWTGuard`](src/JWTGuard.php) have new required constructor parameter [`$eventDispatcher`](src/Providers/AbstractServiceProvider.php#L97) 

## Documentation

Work in progress.

-----------------------------------

## Security

If you discover any security related issues, please email messhias@gmail.com or eric.schricker@adiutabyte.de instead of using the issue tracker.

## License

The MIT License (MIT)
