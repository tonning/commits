# Get latest git commit messages

[![Latest Version on Packagist](https://img.shields.io/packagist/v/tonning/commits.svg?style=flat-square)](https://packagist.org/packages/tonning/commits)

A Laravel package to get latest git commit messages. This can be useful to notify your team of latest git commits, especially if you're deploying using Envoyer where you don't have access to Git after it's been pushed up.

## Postcardware

You're free to use this package (it's [MIT-licensed](LICENSE.md)).

## Installation

You can install the package via composer:

```bash
composer require tonning/commits
```

## Usage

``` php
// config/app.php

'providers' => [
    ...
    Tonning\GitCommits\GitCommitsServiceProvider::class,
];
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email tonning@gmail.com instead of using the issue tracker.

## Credits

- [Kristoffer Tonning](https://github.com/tonning)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
