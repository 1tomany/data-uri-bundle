# Data URI Bundle for Symfony
This package wraps the `1tomany/data-uri` library into an easy to use Symfony bundle.

## Installation
Install the bundle using Composer:

```
composer require 1tomany/data-uri-bundle
```

## Configuration
No configuration is necessary for the bundle. The `DataUriNormalizer` is automatically tagged and configured to denormalize instances of `DataUriInterface` objects.

## Components
* `DataUriInterface` denormalizer via the `OneToMany\DataUriBundle\Serializer\DataUriNormalizer` class.

## Credits
- [Vic Cherubini](https://github.com/viccherubini), [1:N Labs, LLC](https://1tomany.com)

## License
The MIT License
