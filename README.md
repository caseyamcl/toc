PHP TOC Generator
=================

Generates a Table of Contents from *H1...H6* Tags in HTML Content

[![Build Status](https://travis-ci.org/caseyamcl/toc.svg?branch=master)](https://travis-ci.org/caseyamcl/toc)

This package provides a simple, framework-agnostic library to build
a Table-of-Contents from HTML markup.  It does so by parsing *H1...H6* tags.  It can also automatically add appropriate *id* anchor attributes to header tags.

Features:

* Generates HTML menus and [KnpMenu Item](https://github.com/KnpLabs/KnpMenu) Menus
* Adds anchor ID attributes to *H1*...*H6* tags in your content where they do not already exist
* You can specify which *H1*...*H6* heading tag levels to include in the TOC
* Includes a Twig Extension for generating TOCs and compatible markup directly from templates
* Uses the flexible [KnpMenu Library](https://github.com/KnpLabs/KnpMenu) to generate menus
* PSR-0 thru PSR-2 Compliant
* Composer-compatible
* Unit-tested

In the spirit of [KISS philosophy](http://en.wikipedia.org/wiki/KISS_principle), this library makes a few assumptions:

1. The hierarchy of your content is defined solely by the header (*H1*...*H6*) tags.  All other tags are ignored.
2. The link titles in the Table of Contents match either the `title` attribute of the header tag, or if there is no `title`, the plaintext body of the header tag.

Installation Options
--------------------
Install via [Composer](http://getcomposer.org/) by including the following in your `composer.json` file: 
 
    {
        "require": {
            "caseyamcl/toc": "~1.0",
        }
    }

Or, drop the `src` folder into your application and use a [PSR-4 autoloader](http://www.php-fig.org/psr/psr-4/) to include the files.


Usage
-----
This package contains two basic classes:

1. `TOC\MarkupFixer`: Adds an `id` anchor attributes to any *H1*...*H6* tags that do not already have any (you can which header tag levels to use)
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
$htmlOut .= "<div class='toc'><ul>" . $tocGenerator->getHtmlMenu($myHtmlContent) . "</ul></div>";

echo $htmlOut;
```

Twig Integration
----------------

This library includes a [Twig](http://twig.sensiolabs.org) extension that enables you to load TOC lists and add anchors to markup from your Twig templates.

In order to enable Twig integration, you must register the `TocTwigExtension` with your Twig environment:

```php
$myTwig = new \Twig_Environment();
$myTwig->addExtension(new TOC\TocTwigExtension());
```

Specifically, the extension adds a Twig function for generating Table of Contents HTML:

```twig
{# Generates HTML markup for given htmlContent #}
<ul>{{ toc(htmlContent) }}</ul>
```

It also provides a function and a filter for ensuring that your content includes anchors for all HTML header tags.  They both do the same thing, so choose which one suits your needs best:

```twig
{# Adds anchor links (id tags) for given htmlContent #}
{{ add_anchors(htmlContent) }}

{# You can also use it as a filter #}
<div class='my_content'>
    {{ htmlContent | add_anchors }}
</div>
```

Your HTML content may be hard-coded in your Twig Template.  An easy way to accomodate this is to make sure the content is surrounded by `{% block %}...{% endblock %}` tags, and then just pass in that block to the *toc* function.

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
You can choose to include only specific *h1...h6* heading levels in your TOC.  To do this, pass two additional arguments to the `TocGenerator::getHtmlMenu()` method: `$topLevel` and `$depth`.  For example:

```php
$tocGenerator = new TOC\TocGenerator();

// Get TOC using h2, h3, h4
$toc->getHtmlMenu($someHtmlContent, 2, 3);

// Get TOC using h1, h2
$toc->getHtmlMenu($someHtmlContent, 1, 2);

// Get TOC using h4, h5, h6
$toc->getHtmlMenu($someHtmlContent, 4, 3);
```

Most other methods in the package handle these arguments as well:

```php
$tocGenerator = new TOC\TocGenerator();
$markupFixer = new TOC\MarkupFixer();

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

You can customize the rendering of lists that the `TocGenerator` class outputs.  By default, `TocGenerator` uses the [KnpMenu ListRenderer](https://github.com/KnpLabs/KnpMenu/blob/master/src/Knp/Menu/Renderer/ListRenderer.php) class to output the HTML.

You can pass your own instance of the `ListRenderer` class to `TocGenerator::getHtmlMenu()`. Or, you can pass in your own renderer (implements [`Knp\Menu\Renderer\RendererInterface`](https://github.com/KnpLabs/KnpMenu/blob/master/src/Knp/Menu/Renderer/RendererInterface.php)).

For example, you may wish to use different CSS classes for your list items:

```php

$options = [
    'currentAsLink' => false,
    'currentClass'  => 'curr_page',
    'ancestorClass' => 'curr_ancestor',
    'branch_class'  => 'branch
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

$twigLoader = new \Twig_Loader_Filesystem(array(
    __DIR__.'/vendor/KnpMenu/src/Knp/Menu/Resources/views',
    // ...paths to your own Twig templates that render KnpMenus...
));

$twig = new \Twig_Environment($twigLoader);
$itemMatcher = \Knp\Menu\Matcher\Matcher();
$menuRenderer = new \Knp\Menu\Renderer\TwigRenderer($twig, 'knp_menu.html.twig', $itemMatcher);


$tocGenerator = new TOC\TocGenerator();

// Output the Menu using the template 
echo $menuRenderer->render($tocGenerator->getMenu($someHtmlContent));

```
