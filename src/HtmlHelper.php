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

use ArrayIterator;
use DOMDocument;
use DomElement;
use DOMXPath;

/**
 * Trait that helps with HTML-related operations
 *
 * @package TOC
 */
trait HtmlHelper
{
    /**
     * Convert a topLevel and depth to H1..H6 tags array
     *
     * @param int $topLevel
     * @param int $depth
     * @return array|string[]  Array of header tags; ex: ['h1', 'h2', 'h3']
     */
    protected function determineHeaderTags(int $topLevel, int $depth): array
    {
        $desired = range((int) $topLevel, (int) $topLevel + ((int) $depth - 1));
        $allowed = [1, 2, 3, 4, 5, 6];

        return array_map(function ($val) {
            return 'h' . $val;
        }, array_intersect($desired, $allowed));
    }



    /**
     * Traverse Header Tags in DOM Document
     *
     * @param DOMDocument $domDocument
     * @param int          $topLevel
     * @param int          $depth
     * @return ArrayIterator<int,DomElement>
     */
    protected function traverseHeaderTags(DOMDocument $domDocument, int $topLevel, int $depth): ArrayIterator
    {
        $xQueryResults = new DOMXPath($domDocument);

        $xpathQuery = sprintf(
            "//*[%s]",
            implode(' or ', array_map(function ($v) {
                return sprintf('local-name() = "%s"', $v);
            }, $this->determineHeaderTags($topLevel, $depth)))
        );

        $nodes = [];
        $xQueryResults = $xQueryResults->query($xpathQuery);

        if ($xQueryResults) {
            foreach ($xQueryResults as $node) {
                $nodes[] = $node;
            }
            return new ArrayIterator($nodes);
        } else {
            return new ArrayIterator([]);
        }
    }

    /**
     * Is this a full HTML document
     *
     * Guesses, based on presence of <body>...</body> tags
     *
     * @param string $markup
     * @return bool
     */
    protected function isFullHtmlDocument(string $markup): bool
    {
        return (strpos($markup, "<body") !== false && strpos($markup, "</body>") !== false);
    }
}
