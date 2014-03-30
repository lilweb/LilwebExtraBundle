# LilwebExtraBundle

A set of components for Symfony 2 projects.

## Installation

Simply add the bundle to the composer.json file :

```json
"lilweb/extra-bundle": "dev-master"
```

## FilenameSanitizer

Creates a slug-url from a string

### Usage

```php
$slug = Urlizer::urlize($myString);
```
