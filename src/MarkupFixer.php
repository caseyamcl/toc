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

use DOMElement;
use Masterminds\HTML5;
use RuntimeException;
use Cocur\Slugify\SlugifyInterface;

/**
 * TOC Markup Fixer adds `id` attributes to all H1...H6 tags where they do not
 * already exist
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class MarkupFixer
{
    use HtmlHelper;

    /**
     * @var HTML5
     */
    private $htmlParser;

    /**
     * @var SlugifyInterface
     */
    private $slugifier;

    /**
     * Constructor
     *
     * @param HTML5|null $htmlParser
     * @param SlugifyInterface|null $slugify
     */
    public function __construct(?HTML5 $htmlParser = null, ?SlugifyInterface $slugify = null)
    {
        $this->htmlParser = $htmlParser ?? new HTML5();
        $this->slugifier = $slugify ?? new UniqueSlugify();
    }

    /**
     * Fix markup
     *
     * @param string $markup
     * @param int    $topLevel
     * @param int    $depth
     * @return string Markup with added IDs
     * @throws RuntimeException
     */
    public function fix(string $markup, int $topLevel = 1, int $depth = 6): string
    {
        if (! $this->isFullHtmlDocument($markup)) {
            $partialID = uniqid('toc_generator_');
            $markup = sprintf("<body id='%s'>%s</body>", $partialID, $markup);
        }

        $domDocument = $this->htmlParser->loadHTML($markup);
        $domDocument->preserveWhiteSpace = true; // do not clobber whitespace

        // If using the default slugifier, ensure that a unique instance of the class
        $slugger = $this->slugifier instanceof UniqueSlugify ? new UniqueSlugify() : $this->slugifier;

        /** @var DOMElement $node */
        foreach ($this->traverseHeaderTags($domDocument, $topLevel, $depth) as $node) {
            // If no id is found, try the title attribute
            $id = $node->getAttribute('id') ?: $node->getAttribute('title');

            // If no title attribute, use the text content
            $id = $slugger->slugify($id ?: $node->textContent);

            // If the first character begins with a numeric, prepend 'toc-' on it.
            if (ctype_digit(substr($id, 0, 1))) {
                $id = 'toc-' . $id;
            }

            // Overwrite the id attribute
            $node->setAttribute('id', $id);
        }

        return $this->htmlParser->saveHTML(
            (isset($partialID)) ? $domDocument->getElementById($partialID)->childNodes : $domDocument
        );
    }
}
