PHP TOC Generator
=================

Generates a Table of Contents from *H1...H6* Tags in HTML Content

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Github Build][ico-ghbuild]][link-ghbuild]
[![Code coverage][ico-coverage]](coverage.svg)
[![PHPStan Level 8][ico-phpstan]][link-phpstan]
[![Total Downloads][ico-downloads]][link-downloads]

**NOTE: This library now requires PHP 7.2 or newer; to retain PHP5-7.1 support, use the following composer directive:** 
`composer require caseyamcl/toc ~2.0.0`

This package provides a simple, framework-agnostic library to build
a Table-of-Contents from HTML markup.  It does so by evaluating your *H1...H6* tags.
It can also automatically add appropriate *id* anchor attributes to header tags so that in-page links work.

Features:

* Generates HTML menus and [KnpMenu Item](https://github.com/KnpLabs/KnpMenu) Menus
* Adds anchor ID attributes to *H1*...*H6* tags in your content where they do not already exist
* You can specify which *H1*...*H6* heading tag levels to include in the TOC
* Includes a Twig Extension for generating TOCs and compatible markup directly in your templates
* Uses the flexible [KnpMenu Library](https://github.com/KnpLabs/KnpMenu) to generate menus
* [PSR-12](https://www.php-fig.org/psr/psr-12/) compliant
* Composer-compatible
* Unit-tested (95% coverage)

In the spirit of [KISS philosophy](http://en.wikipedia.org/wiki/KISS_principle), this library makes a few assumptions:

1. The hierarchy of your content is defined solely by the header (*H1*...*H6*) tags.  All other tags are ignored when generating the TOC.
2. The link titles in the Table of Contents match either the `title` attribute of the header tag, or if there is no `title`, the (slugified) plaintext body of the header tag.

Installation Options
--------------------
Install via [Composer](http://getcomposer.org/) by including the following in your `composer.json` file: 
 
    {
        "require": {
            "caseyamcl/toc": "^3.0",
        }
    }

Or, drop the `src` folder into your application and use a [PSR-4 autoloader](http://www.php-fig.org/psr/psr-4/) to include the files.

Usage
-----
This package contains two main classes:

1. `TOC\MarkupFixer`: Adds `id` anchor attributes to any *H1*...*H6* tags that do not already have any (you can specify which header tag levels to use at runtime)
2. `TOC\TocGenerator`: Generates a Table of Contents from HTML markup

Basic Example:

```php
$myHtmlContent = <<<END
    <h1>This is a header tag with no anchor id</h1>
    <p>Lorum ipsum doler sit amet</p>
    <h2 id='foo'>This is a header tag with an anchor id</h2>
    <p>Stuff here</p>
    <h3 id='bar'>This is a header tag with an anchor id</h3>
END;

$markupFixer  = new TOC\MarkupFixer();
$tocGenerator = new TOC\TocGenerator();

// This ensures that all header tags have `id` attributes so they can be used as anchor links
$htmlOut  = "<div class='content'>" . $markupFixer->fix($myHtmlContent) . "</div>";

// This generates the Table of Contents in HTML
$htmlOut .= "<div class='toc'>" . $tocGenerator->getHtmlMenu($myHtmlContent) . "</div>";

echo $htmlOut;
```

This produces the following output:

```html
<div class='content'>
    <h1 id="this-is-a-header-tag-with-no-anchor-id">This is a header tag with no anchor id</h1>
    <p>Lorum ipsum doler sit amet</p>
    <h2 id="foo">This is a header tag with an anchor id</h2>
    <p>Stuff here</p>
    <h3 id="bar">This is a header tag with an anchor id</h3>
</div>
<div class='toc'>
    <ul>
        <li class="first last">
        <span></span>
            <ul class="menu_level_1">
                <li class="first last">
                    <a href="#foo">This is a header tag with an anchor id</a>
                    <ul class="menu_level_2">
                        <li class="first last">
                            <a href="#bar">This is a header tag with an anchor id</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </li>
    </ul>
</div>
```

Twig Integration
----------------

This library includes a [Twig](http://twig.sensiolabs.org) extension that 
enables you to load TOC lists and add anchors to markup from your Twig templates.

In order to enable Twig integration, you must register the
 `TocTwigExtension` with your Twig environment:

```php
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$myTwig = new Environment(new FilesystemLoader());
$myTwig->addExtension(new TOC\TocTwigExtension());
```

Specifically, the extension adds a Twig function for generating Table of Contents HTML:

```twig
{# Generates HTML markup for given htmlContent #}
<ul>{{ toc(htmlContent) }}</ul>
```

It also provides a function and a filter for ensuring that your content 
includes anchors for all HTML header tags.  They both do the same thing, 
so choose which one suits your needs best:

```twig
{# Adds anchor links (id tags) for given htmlContent #}
{{ add_anchors(htmlContent) }}

{# You can also use it as a filter #}
<div class='my_content'>
    {{ htmlContent | add_anchors }}
</div>
```

Your HTML content may be hard-coded in your Twig Template. An easy way 
to accommodate this is to make sure the content is surrounded by 
`{% block %}...{% endblock %}` tags, and then just pass in that block to the *toc* function.

For example:

```twig
{% extends 'base.html.twig' %}
{% block page_content %}
<div class='page_sidebar'>
   {{ toc(add_anchors(block('my_writeup'))) }}
</div>

<div class='page_content'>
   {{ add_anchors(block('my_writeup')) }}
</div>
{% endblock %}

{% block my_writeup %}
<h1>Hi There</h1>
<p>Lorum ipsum baz biz etecetra</p>
<h2>This is some content</h2>
<p>More content here.  Blah blah</p>
{% endblock %}
```


Specifying Heading Levels to Include
-------------------------------------------
You can choose to include only specific *h1...h6* heading levels in your TOC. 
 To do this, pass two additional arguments to the 
 `TocGenerator::getHtmlMenu()` method: `$topLevel` and `$depth`.  For example:

```php
$tocGenerator = new TOC\TocGenerator();
$someHtmlContent = '<div><h1>Test</h1><p>Lorum ipsum</p><h2>Test2</h2><p>Lorum ipsum</p></div>';

// Get TOC using h2, h3, h4
$tocGenerator->getHtmlMenu($someHtmlContent, 2, 3);

// Get TOC using h1, h2
$tocGenerator->getHtmlMenu($someHtmlContent, 1, 2);

// Get TOC using h4, h5, h6
$tocGenerator->getHtmlMenu($someHtmlContent, 4, 3);
```

Most other methods in the package handle these arguments as well:

```php
$tocGenerator = new TOC\TocGenerator();
$markupFixer = new TOC\MarkupFixer();
$someHtmlContent = '<div><h1>Test</h1><p>Lorum ipsum</p><h2>Test2</h2><p>Lorum ipsum</p></div>';


// Get KnpMenu using h1, h2, h3
$tocGenerator->getMenu($someHtmlContent, 1, 3);

// Fix markup for h3, h4 tags only
$markupFixer->fix($someHtmlContent, 3, 2);
```

Twig functions and filters accept these arguments as well:

```twig
{# Generate TOC using h2, h3 tags #}
{{ toc(my_content, 2, 3) }}

{# Add anchors to h4, h5, h6 tags #}
{{ my_content | add_anchors(4, 3) }}
```

Customizing Menu Output
-----------------------

You can customize the rendering of lists that the `TocGenerator` class
outputs.  By default, `TocGenerator` uses the [KnpMenu ListRenderer](https://github.com/KnpLabs/KnpMenu/blob/master/src/Knp/Menu/Renderer/ListRenderer.php) 
class to output the HTML.

You can pass your own instance of the `ListRenderer` class to 
`TocGenerator::getHtmlMenu()`. Or, you can pass in your own renderer 
(implements [`Knp\Menu\Renderer\RendererInterface`](https://github.com/KnpLabs/KnpMenu/blob/master/src/Knp/Menu/Renderer/RendererInterface.php)).

For example, you may wish to use different CSS classes for your list items:

```php
$someHtmlContent = '<div><h1>Test</h1><p>Lorum ipsum</p><h2>Test2</h2><p>Lorum ipsum</p></div>';


$options = [
    'currentAsLink' => false,
    'currentClass'  => 'curr_page',
    'ancestorClass' => 'curr_ancestor',
    'branch_class'  => 'branch'
];

$renderer = new Knp\Menu\Renderer\ListRenderer(new Knp\Menu\Matcher\Matcher(), $options);

// Render the list
$tocGenerator = new TOC\TocGenerator();
$listHtml = $tocGenerator->getHtmlMenu($someHtmlContent, 1, 6, $renderer);

```

#### Customizing with Twig

The KnpMenu library offers more robust integration with the [Twig Templating System](http://twig.sensiolabs.org/)
than is offered by default with this library.  You can take advantage of it by using the [TwigRenderer](https://github.com/KnpLabs/KnpMenu/blob/master/doc/02-Twig-Integration.markdown#using-the-twigrenderer)
that is bundled with KnpMenu:

```php
use Knp\Menu\Matcher\Matcher;
use Knp\Menu\Renderer\TwigRenderer;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$someHtmlContent = '<div><h1>Test</h1><p>Lorum ipsum</p><h2>Test2</h2><p>Lorum ipsum</p></div>';

$twigLoader = new FilesystemLoader(array(
    __DIR__.'/vendor/KnpMenu/src/Knp/Menu/Resources/views',
    // ...paths to your own Twig templates that render KnpMenus...
));

$twig = new Environment($twigLoader);
$itemMatcher = new Matcher();
$menuRenderer = new TwigRenderer($twig, 'knp_menu.html.twig', $itemMatcher);

$tocGenerator = new TOC\TocGenerator();

// Output the Menu using the template 
echo $menuRenderer->render($tocGenerator->getMenu($someHtmlContent));
```

Ordered vs Unordered Lists
--------------------------

The KnpMenu library produces unordered lists (`ul`) by default.  This 
library contains a custom renderer for ordered lists, whether you're using
Twig or not:

```php
$someHtmlContent = '<div><h1>Test</h1><p>Lorum ipsum</p><h2>Test2</h2><p>Lorum ipsum</p></div>';

// Ordered List
$orderedRenderedList = (new TOC\TocGenerator())->getOrderedHtmlMenu($someHtmlContent);
```

Twig Usage:

```twig
{# Generate ordered TOC #}
{{ toc_ordered(my_content) }}

{# The same options can be used for ordered lists as unordered lists #}
{{ toc_ordered(my_content, 2, 3) }}
```

[ico-version]: https://img.shields.io/packagist/v/caseyamcl/toc.svg
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg
[ico-ghbuild]: https://github.com/caseyamcl/toc/workflows/Github%20Build/badge.svg
[ico-phpstan]: https://img.shields.io/badge/PHPStan-level%205-brightgreen.svg
[ico-coverage]: https://github.com/caseyamcl/toc/blob/master/coverage.svg
[ico-downloads]: https://img.shields.io/packagist/dt/caseyamcl/toc.svg

[link-packagist]: https://packagist.org/packages/caseyamcl/toc
[link-phpstan]: https://phpstan.org/
[link-ghbuild]: https://github.com/caseyamcl/toc/actions?query=workflow%3A%22Github+Build%22
[link-downloads]: https://packagist.org/packages/caseyamcl/toc
[link-downloads]: https://packagist.org/packages/caseyamcl/toc
[link-author]: https://github.com/caseyamcl
[link-contributors]: ../../contributors
