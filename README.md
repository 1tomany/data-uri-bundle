# Data URI Bundle for Symfony

This package wraps the `1tomany/data-uri` library into an easy to use Symfony bundle.

## Installation

Install the bundle using Composer:

```
composer require 1tomany/data-uri-bundle
```

## Configuration

No configuration is necessary for the bundle. The denormalizer for objects that implement the interface `OneToMany\DataUri\Contract\Record\TemporaryFileInterface` is automatically tagged and configured.

## Components

- `TemporaryFileInterface` denormalizer via the `OneToMany\DataUriBundle\Serializer\DataUriNormalizer` class.
- `onetomany:data-uri:encode-file` console command to generate a base64 encoded data URI representation of a file.

## Credits

- [Vic Cherubini](https://github.com/viccherubini), [1:N Labs, LLC](https://1tomany.com)

## License

The MIT License
