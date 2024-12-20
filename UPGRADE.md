## Upgrading from version 3.x to 4.x

This upgrade is mostly a drop-in for implementers using v3 and PHP 8.2 or newer. The only API changes are to the
slug generation logic (see below).

* Ensure your PHP version is at least 8.2 or higher.
* If you implement a custom slugger (previously "slugifier"), you must upgrade the logic to implement the new `SluggerInterface` class.
