<?php

namespace TOC;

use Twig_Extension;

/**
 * Class TocTwigExtension
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class TocTwigExtension extends Twig_Extension
{
    /**
     * @var \TOC\TocGenerator
     */
    private $generator;

    /**
     * @var \TOC\TocMarkupFixer
     */
    private $fixer;

    // ---------------------------------------------------------------

    /**
     * Constructor
     *
     * @param \TOC\TocGenerator   $generator
     * @param \TOC\TocMarkupFixer $fixer
     */
    public function __construct(TocGenerator $generator = null, TocMarkupFixer $fixer = null)
    {
        $this->generator = $generator ?: new TocGenerator();
        $this->fixer     = $fixer     ?: new TocMarkupFixer();
    }

    // ---------------------------------------------------------------

    public function getFilters()
    {
        $filters = parent::getFilters();

        $filters[] = new \Twig_SimpleFilter('add_anchors', function($str, $top = 1, $depth = 2) {
            return $this->fixer->fix($str, $top, $depth);
        });

        return $filters;
    }

    // ---------------------------------------------------------------

    public function getFunctions()
    {
        $functions = parent::getFunctions();

        // ~~~

        $functions[] = new \Twig_SimpleFunction('toc', function($markup, $top = 1, $depth = 2, $titleTemplate = null) {
            return ($titleTemplate)
                ? $this->generator->getHtmlItems($markup, $top, $depth, $titleTemplate)
                : $this->generator->getHtmlItems($markup, $top, $depth);
        });

        // ~~~

        $functions[] = new \Twig_SimpleFunction('toc_items', function($markup, $top = 1, $depth = 2) {
            return $this->generator->getItems($markup, $top, $depth);
        });

        return $functions;
    }

    // ---------------------------------------------------------------

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'toc';
    }
}