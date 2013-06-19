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

License
-------

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.


