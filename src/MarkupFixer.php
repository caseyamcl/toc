<?php

/**
 * PHP TableOfContents Library
 *
 * @license http://opensource.org/licenses/MIT
 * @link https://github.com/caseyamcl/toc
 * @version 1.0
 * @package caseyamcl/toc
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * ------------------------------------------------------------------
 */

namespace TOC;

use Masterminds\HTML5;
use RuntimeException;

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
     * Constructor
     *
     * @param HTML5 $htmlParser
     */
    public function __construct(HTML5 $htmlParser = null)
    {
        $this->htmlParser = $htmlParser ?: new HTML5();
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
    public function fix($markup, $topLevel = 1, $depth = 6)
    {
        if ( ! $this->isFullHtmlDocument($markup)) {
            $partialID = 'toc_generator_' . mt_rand(1000, 4000);
            $markup = sprintf("<body id='%s'>%s</body>", $partialID, $markup);
        }

        $domDocument = $this->htmlParser->loadHTML($markup);
        $domDocument->preserveWhiteSpace = true; // do not clobber whitespace

        $sluggifier = new UniqueSluggifier();

        /** @var \DOMElement $node */
        foreach ($this->traverseHeaderTags($domDocument, $topLevel, $depth) as $node) {
            if ($node->getAttribute('id')) {
                continue;
            }

            $node->setAttribute('id', $sluggifier->slugify($node->getAttribute('title') ?: $node->textContent));
        }

        return $this->htmlParser->saveHTML((isset($partialID)) ? $domDocument->getElementById($partialID)->childNodes : $domDocument);
    }
}

/* EOF: TocMarkupFixer.php */
