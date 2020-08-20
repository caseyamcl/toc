<?php

/**
 * PHP TableOfContents Library
 *
 * @license http://opensource.org/licenses/MIT
 * @link https://github.com/caseyamcl/toc
 * @version 3
 * @package caseyamcl/toc
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 *
 * ------------------------------------------------------------------
 */

declare(strict_types=1);

namespace TOC;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Table of Contents Twig Extension Integrates with Twig
 *
 * Adds filter:
 * - add_anchors
 *
 * Adds functions:
 * - toc (returns HTML list)
 * - toc_items (returns KnpMenu iterator)
 * - toc_ordered (returns KnpMenu iterator with <ol>...</ol>)
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class TocTwigExtension extends AbstractExtension
{
    /**
     * @var TocGenerator
     */
    private $generator;

    /**
     * @var MarkupFixer
     */
    private $fixer;

    /**
     * Constructor
     *
     * @param TocGenerator|null $generator
     * @param MarkupFixer|null $fixer
     */
    public function __construct(?TocGenerator $generator = null, ?MarkupFixer $fixer = null)
    {
        $this->generator = $generator ?: new TocGenerator();
        $this->fixer     = $fixer     ?: new MarkupFixer();
    }

    /**
     * @return array|TwigFilter[]
     */
    public function getFilters(): array
    {
        $filters = parent::getFilters();

        $filters[] = new TwigFilter('add_anchors', function ($str, $top = 1, $depth = 6) {
            return $this->fixer->fix($str, $top, $depth);
        }, ['is_safe' => ['html']]);

        return $filters;
    }

    /**
     * @return array|TwigFunction[]
     */
    public function getFunctions(): array
    {
        $functions = parent::getFunctions();

        // ~~~

        $functions[] = new TwigFunction('toc', function ($markup, $top = 1, $depth = 6) {
            return $this->generator->getHtmlMenu($markup, $top, $depth);
        }, ['is_safe' => ['html']]);

        $functions[] = new TwigFunction('toc_ordered', function ($markup, $top = 1, $depth = 6) {
            return $this->generator->getHtmlMenu($markup, $top, $depth, null, true);
        }, ['is_safe' => ['html']]);

        // ~~~

        $functions[] = new TwigFunction('toc_items', function ($markup, $top = 1, $depth = 6) {
            return $this->generator->getMenu($markup, $top, $depth);
        });

        $functions[] = new TwigFunction('add_anchors', function ($markup, $top = 1, $depth = 6) {
            return $this->fixer->fix($markup, $top, $depth);
        }, ['is_safe' => ['html']]);

        return $functions;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName(): string
    {
        return 'toc';
    }
}
