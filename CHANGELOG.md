# ChangeLog - PHP TableOfContents (TOC)
All notable changes to this project are documented in this file.

## [2.0] - 2018-11-09
### Changed
- BREAKING: `TocGenerator::getMenu()` returns an empty menu item instead of an empty array when no
  markup exists.
- Improved unique identifier generation in `MarkupFixer`
### Added
- Markup test to test for non-standard attributes (e.g. Vue, Angular, etc)
- Added support for more recent versions of `cocur/slugify` library in `composer.json`

## [1.2] - 2018-03-23
### Changed
- Formatting changes
- PSR-2 code style compliance
### Fixed
- HtmlHelper evaluates body tags correctly (thanks @Schlaefer)
- Removed `composer.lock` from Git
- Removed IDE files (`.idea`) and replaced it with `.editorconfig`
### Added
- Added `CONTRIBUTING.md`, `CODE_OF_CONDUCT.md`, and `PULL_REQUEST_TEMPLATE.md` to make contributing easier
- Added tests for PHP5.5, 5.6, 7.0, 7.1, and 7.2

## [1.1] - 2015-05-06
### Changed
- Replaced `simple_dom_parser` with `masterminds/html5` parser library
### Fixed
- The `MarkupFixer` class now correctly preserves whitespace in HTML, correcting issues with code in *pre*...*/pre* tags
### Added
- Unit tests for Trait and Twig Extension to achieve 100% unit test coverage
- Created a `.gitattributes` file

## [1.0] - 2014-12-30
- Initial Version
