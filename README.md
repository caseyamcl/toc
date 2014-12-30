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
* Adds anchor IDs to content
* Specify which *H1*...*H6* heading tags to use at runtime
* Includes Twig Extension for generating TOC lists and compatible markup directly from templates
* PSR-0 thru PSR-2 Compliant
* Composer-compatible
* Unit-tested

In the spirit of KISS philosophy, this library assumes a few things:

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

Or, drop the `src` folder into your application and use a PSR-0 autoloader to include the files.


Usage
-----
This library does two things with any HTML content passed in:

1. Adds `id` tags to any H1..H6 tags (or header-levels specified at runtime) that do
   not already have any.  This is to enable anchor links in the content.
2. Generates HTML (or an associative array) of anchor links that can be rendered in your
   template.

Basic Example:

```php
$myHtmlContent = <<<END
    <h1>This is a header tag with no anchor id</h1>
    <p>Lorum ipsum doler sit amet</p>
    <h2 id='foo'>This is a header tag with an anchor id</h2>
    <p>Stuff here</p>
    <h3 id='bar'>This is a header tag with an anchor id</h3>
END;

$tocContent = new \TOC\TocContent($myHtmlContent);

// Generate HTML list of links
echo "<ul>" . $tocContent->getTocListItems('h1', 'h2') . "</ul>";

```

Twig Integration
----------------
This library includes a [Twig](http://twig.sensiolabs.org) extension that enables you to load
TOC lists and compatible markup from your Twig templates.

Specifically, the extension adds three Twig functions:

```twig
{# Generates HTML markup for given htmlContent #}
{# The second two parameters are optional (defaults are h1, h3) #}
<ul>{{ toc(htmlContent, 'h1', 'h3') }}</ul>

{# Generates an array of anchor links for given htmlContent #}
{# The second two parameters are optional (defaults are h1, h3) #}
<ul>
    {% for anchor, title in toc_items(htmlContent 'h1', 'h3'): %}
        <li class='whatever'><a href='{{ anchor }}'>{{ title }}</a></li>
    {% endfor %}
</ul>

{# Adds anchor links (id tags) for given htmlContent #}
{# The second two parameters are optional (defaults are h1, h3) #}
<div class='my_content'>
    {{ toc_content(htmlContent, 'h1', 'h3') }}
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
   {{ toc(block('my_writeup'), 'h1', 'h2') }}
</div>

<div class='page_content'>
   {{ toc_content(block('my_writeup'), 'h1', 'h2') }}
</div>
{% endblock %}

{% block my_writeup %}
<h1>
{% endblock %}
```
