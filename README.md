FS Functions
============

[![Build Status](https://travis-ci.org/herrera-io/php-fs-functions.png)](http://travis-ci.org/herrera-io/php-fs-functions)

A collection of file system related functions.

Summary
-------

The library contains a collection of simple file system related functions:

- `canonical_path()` - Returns the canonical file path.
- `is_absolute_path()` - Check for an absolute path.
- `is_hidden_path()` - Checks for a hidden path.

Installation
------------

Add it to your list of Composer dependencies:

```sh
$ composer require herrera-io/file-system-functions=1.*
```

Usage
-----

```php
<?php

var_export(canonical_path('../../myDir')); // /var/lib/myDir
var_export(canonical_path('../../myDir')); // C:\\Projects\\myDir

var_export(is_absolute_path('../')); // false
var_export(is_absolute_path('C:\\Test\\Path')); // true

var_export(is_hidden_path('/path/to/.hidden')); // true
var_export(is_hidden_path('C:\\Path\\To\\Hidden.txt')); // true
```