PHP toolkit for PO files
========================

This is a simple PHP library to parse and play with PO files. Still under heavy development.

Getting started
---------------

Simply include the main library into your PHP code:
```php
require_once('POFile.php');
```

Then, suppose you want to parse a file ```sample.po```, simply use:
```php
$poFile = new POFile('sample.po');
```

Features
--------

From here, ```pophp``` provides two levels of funtionality: 

### File level ###

Each parsed PO file exposes the following methods: 

```php
$poFile->getEntries([ array $fromFiles ])
```
Retrieve entries from specific source files (given as an optional array of filenames).
If no parameter is given, then the method returns all the entries of the PO file.


