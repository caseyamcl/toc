PHP TOC Generator
=================

Geneates Table of Contents from H1...H6 Tags in HTML Content
------------------------------------------------------------

[![Build Status](https://travis-ci.org/caseyamcl/toc)](https://travis-ci.org/caseyamcl/toc.png)

This library provides a simple, framework-agnostic class to build
a Table-of-Contents from HTML markup.  It does so by parsing H1...H6
tags.  It can also automatically add appropriate "id" anchor links to content. 

Features:
* Generates arrays or HTML &lt;li*gt; tags
* Adds anchor IDs to content where they do not exist
* Specify which *H1*...*H6* heading tags to use at runtime
* Includes Twig Extension for generating TOC lists and compatible markup directly from templates
* PSR-0 thru PSR-2 Compliant
* Composer-compatible
* Unit-tested

In the spirit of KISS philosophy, this library assumes a few things:

1. The hierarchy of your content is defined solely by the header (*H1*...*H6*) tags.  All other tags
   are ignored.
2. The hierarchy is well-formed, meaning that you don't randomly distribute header tags around.  They occur in a
   predictable order (*H2*s are children of *H1*s, *H3*s are children of *H2*s, etc).
3. The link titles in the Table of Contents should match either the `title` attribute of the header tag,
   or if there is no `title`, the plaintext body of the header tag.

Installation Options
--------------------
Install via [Composer](http://getcomposer.org/) by including the following in your `composer.json` file: 
 
    {
        "require": {
            "caseyamcl/toc": "~1.0",
        }
    }

Or, drop the `src` folder into your application and use a PSR-0 autoloader to include the files.


Usage
-----
This library does two things:

1. Adds `id` anchor tags to any *H1*..*H6* tags that do not already have any.
2. Generates HTML (or an associative array) of anchor links that can be rendered in your template.

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
$htmlOut .= "<div class='toc'><ul>" . $tocGenerator->getHtmlItems($myHtmlContent) . "</ul></div>";

echo $htmlOut;
```

There are two service classes: `TOC\TocGenerator` and `TOC\MarkupFixer`:

The `TocGenerator` class accepts HTML markup and generates a list of anchor links


Twig Integration
----------------
This library includes a [Twig](http://twig.sensiolabs.org) extension that enables you to load
TOC lists and add anchors to markup from your Twig templates.

Specifically, the extension adds two Twig functions for generating Table of Contents lists items:

```twig
{# Generates HTML markup for given htmlContent #}
{# The second two parameters are optional (defaults are h1, h6) #}
<ul>{{ toc(htmlContent, 'h1', 'h3') }}</ul>

{# Generates an array of anchor links for given htmlContent #}
{# The second two parameters are optional (defaults are h1, h6) #}
<ul>
    {% for anchor, title in toc_items(htmlContent, 'h1', 'h3'): %}
        <li class='whatever'><a href='{{ anchor }}'>{{ title }}</a></li>
    {% endfor %}
</ul>
```

It also adds one function and one filter for ensuring that your content includes anchors for 
all HTML tags:

```twig
{# Adds anchor links (id tags) for given htmlContent #}
{# The second two parameters are optional (defaults are h1, h6) #}
{{ add_anchors(htmlContent, 'h1', 'h2')

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
