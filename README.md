PHP TOC Generator
=================

Geneates Table of Contents from H1...H6 Tags in HTML Content
------------------------------------------------------------

[![Build Status](https://travis-ci.org/caseyamcl/toc)](https://travis-ci.org/caseyamcl/toc.png)

This package provides a simple, framework-agnostic library to build
a Table-of-Contents from HTML markup.  It does so by parsing H1...H6
tags.  It can also automatically add appropriate "id" anchor links to header tags.

Features:
* Generates menu iterator or HTML &lt;li*gt; tags
* Can add anchor IDs to *H1*...*H6* where they do not already exist
* Specify which *H1*...*H6* heading tags to include in the TOC
* Includes Twig Extension for generating TOC lists and compatible markup directly from templates
* Uses the flexible [KnpMenu Library](https://github.com/KnpLabs/KnpMenu) to generate menus
* PSR-0 thru PSR-2 Compliant
* Composer-compatible
* Unit-tested

In the spirit of KISS philosophy, this library makes a few assumptions:

1. The hierarchy of your content is defined solely by the header (*H1*...*H6*) tags.  All other tags
   are ignored.
2. The link titles in the Table of Contents should match either the `title` attribute of the header tag,
   or if there is no `title`, the plaintext body of the header tag.

Installation Options
--------------------
Install via [Composer](http://getcomposer.org/) by including the following in your `composer.json` file: 
 
    {
        "require": {
            "caseyamcl/toc": "~1.0",
        }
    }

Or, drop the `src` folder into your application and use a PSR-4 autoloader to include the files.


Usage
-----
This contains two basic classes:

1. `MarkupFixer`: Adds `id` anchor attributes to any *H1*...*H6* tags that do not already have any.
2. `TocGenerator`: Generates HTML (or an associative array) of anchor links that can be rendered in your template.

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

// This ensures that all header tags have `id` attributes so they can be used as anchors
$htmlOut  = "<div class='content'>" . $markupFixer->fix($myHtmlContent) . "</div>";

// This generates the Table of Contents List
$htmlOut .= "<div class='toc'><ul>" . $tocGenerator->getHtmlMenu($myHtmlContent) . "</ul></div>";

echo $htmlOut;
```

Twig Integration
----------------
This library includes a [Twig](http://twig.sensiolabs.org) extension that enables you to load
TOC lists and add anchors to markup from your Twig templates.

Specifically, the extension adds a Twig function for generating Table of Contents HTML:

```twig
{# Generates HTML markup for given htmlContent #}
{# The second two parameters are optional (defaults are 1, 6) #}
<ul>{{ toc(htmlContent, '1', '3') }}</ul>
```

It also adds one function and one filter for ensuring that your content includes anchors for 
all HTML tags:

```twig
{# Adds anchor links (id tags) for given htmlContent #}
{# The second two parameters are optional (defaults are 1, 6) #}
{{ add_anchors(htmlContent, '1', '2')

{# You can also use it as a filter #}
<div class='my_content'>
    {{ htmlContent | add_anchors('h1', 'h3') }}
</div>
```

You may have content in hard-coded in your Twig Template that you want to TOC-ize.  An
easy way to do this is to make sure the content is surrounded by `{% block %}...{% endblock %}`
tags, and then just pass in that content to the *toc* functions>

For example:

```twig
{% extends 'base.html.twig' %}
{% block page_content %}
<div class='page_sidebar'>
   {{ toc(add_anchors(block('my_writeup'), 'h1', 'h2'), 'h1', 'h2') }}
</div>

<div class='page_content'>
   {{ add_anchors(block('my_writeup'), 'h1', 'h2') }}
</div>
{% endblock %}

{% block my_writeup %}
<h1>Hi There</h1>
<p>Lorum ipsum baz biz etecetra</p>
<h2>This is some content</h2>
<p>More content here.  Blah blah</p>
{% endblock %}
```

In order to enable this functionality, you must register the `TocTwigExtension` with your Twig environment:

```php
$myTwig = new \Twig_Environment();
$myTwig->addExtension(new TocTwigExtension());
```

Customizing Menu Output
-----------------------

The `TocGenerator` class outputs HTML by default, but you can customize the rendering of the list.  By default
`TocGenerator` uses the [KnpMenu Library](https://github.com/KnpLabs/KnpMenu) `ListRenderer` class to output the HTML.

You can pass your instance of the `ListRenderer` class to `TocGenerator::getHtmlMenu()`. Or, you can pass in 
your own renderer (implements [`Knp\Menu\Renderer\RendererInterface`](https://github.com/KnpLabs/KnpMenu/blob/master/src/Knp/Menu/Renderer/RendererInterface.php)).

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
