#Sanitizer

Creates a slug-url from a string.

## Usage

```php
use Lilweb\ExtraBundle\Sanitizer\Urlizer;
...
$slug = Urlizer::urlize($myString);
```

*Note : Inspired by the doctrine implementation of Slug behaviour*.
