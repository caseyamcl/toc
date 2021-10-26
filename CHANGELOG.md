# ChangeLog - PHP TableOfContents (TOC)
All notable changes to this project are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [3.0.2] - 2021-10-26
### Added
- UniqueSlugifyTest to test the slugger on its own
### Changed
- Bumped minimum PHP version up to v7.2
- Minor test code enhancements (thanks @peter279k!)
### Removed
- Support for most pre-PHP7 versions of `cocur/slugify`

## [3.0.1] - 2020-12-07
### Added
- PHP >=8.0 support in `composer.json` (fixes #13)
- GitHub Actions build status badge in `README.md`
- PHPStan in dev dependencies
- Additional build checks (PHPStan and PHP-CS)
- Automatic SVG badge generation for code coverage

### Removed
- `.travis.yml` build support (switched to Github Actions)
- Build dependency on scrutinizer.org service

## [3.0] - 2020-08-20
### Changed
- BREAKING: Renamed internal class `UniqueSluggifier` to `UniqueSlugify`
- Minor comment fixes and make arguments optional in service constructors
- (dev) Added `*.cache` to gitignore (mostly for PHPUnit)
- (dev) Made compatible with PHPUnit v9 and updated `phpunit.xml.dist` schema

### Fixed
- Fixed bug from v2.3: If default slugifier is used (UniqueSlugify.php), then ensure unique instance each time `MarkupFixer::fix()` is run
  This will prevent it from continuing to generate unique slugs if used more than once.

## [2.3] - 2020-07-16
### Added
- Ability to inject the slugify class (#26) (thanks @yaquawa)

### Changed
- Renamed internal class `UniqueSluggifier` to `UniqueSlugify`

## [2.2] - 2020-04-12
### Changed
- Added support Twig v3 in `composer.json`
- Minor change to `README.md` to get PhpStorm IDE to stop warning

### Removed
- Dropped support for Twig < v2.4
- Dropped support for KnpMenu < v3.0

### Fixed
- Bug with type-hints and older version of KnpMenu in `OrderedListRenderer`

## [2.1.1] - 2019-12-23
### Added
- Tests for PHP7.4 in `.travis.yml`
- Additional header text to this CHANGELOG

### Changed
- Added compatibility with [v4](https://github.com/cocur/slugify/releases/tag/v4.0.0) of `cocur/slugify` library
- Require minimum v3.5 of `squizlabs/php_codesniffer` (the lowest version that supports PSR-12 checking)

### Fixed
- Only prefer lowest version of dependencies on lowest tested version of PHP
- Typo in `cocur/slugify` library dependency that affected v2.0
- Typo in `README.md` related to requirements and other stuff too.
- Updated Twig usage examples in `README.md` to reflect the class names from more recent versions of Twig

## [2.1] - 2019-10-01
### Added
- Ability to easily render `<ol>` and `<ul>` lists (thanks @swapnilbanga) (fixes #2)
- PHP 7 goodness: `declare(strict_types=1)` and method argument & return signatures

### Fixed
- Several issues in the README (typos, etc)
- Version number in COPYRIGHT notice
- Empty levels are now automatically trimmed from the generated output (fixes #1) 

### Changed
- Updated PHP requirements to modern versions (7.1+)
- Updated dependencies in `composer.json`: `knplabs/knp-menu` now allows v3.0
- Updated dependencies in `composer.json`: `twig/twig` now allows v2.0
- Updated dependencies in `composer.json`: minimum version of PHPunit is now v7.5
- Updated PHP CodeSniffer to use PSR-12 standard
- Modified `phpunit.xml.dist` to include coverage report generation 
- Renamed `LICENSE` to `LICENSE.md`

### Removed
- Support for old PHP versions in `.travis.yml`
- Redundant `bootstrap.php` file in tests
- `--dev` option in `travis.yml` (which is now deprecated) 

## [2.0.1] - 2019-06-26
### Fixed
- Duplicate headings are identified correctly ([#7](https://github.com/caseyamcl/toc/issues/7))
- `composer:check-style` and `composer:fix-style` commands now work (fixed missing codesniffer library) 

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
