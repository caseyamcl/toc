<?php
/**
 * Created by PhpStorm.
 * User: casey
 * Date: 12/30/14
 * Time: 12:20 PM
 */

namespace TOC;

use RuntimeException;
use Sunra\PhpSimple\HtmlDomParser;

/**
 * TOC Markup Fixer adds `id` attributes to all H1...H6 tags where they do not
 * already exist
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class TocMarkupFixer
{
    use HeaderTagInterpreter;

    // ---------------------------------------------------------------

    /**
     * @var HtmlDomParser
     */
    private $domParser;

    // ---------------------------------------------------------------

    /**
     * Constructor
     *
     * @param HtmlDomParser $domParser
     */
    public function __construct(HtmlDomParser $domParser = null)
    {
        $this->domParser = $domParser ?: new HtmlDomParser();
    }

    // ---------------------------------------------------------------

    /**
     * Fix markup
     *
     * @param string $markup
     * @param int    $topLevel
     * @param int    $depth
     * @return string Markup with added IDs
     * @throws RuntimeException
     */
    public function fix($markup, $topLevel = 1, $depth = 2)
    {
        $sluggifier = new Sluggifier();

        $tags   = $this->determineHeaderTags($topLevel, $depth);
        $parsed = $this->domParser->str_get_html($markup);

        // Runtime exception for bad code
        if ( ! $parsed) {
            throw new RuntimeException("Could not parse HTML");
        }

        // Extract items
        foreach ($parsed->find(implode(', ', $tags)) as $tag) {

            // Ignore tags that already have IDs
            if ($tag->id) {
                continue;
            }

            $tag->id = $sluggifier->slugify($tag->title ?: $tag->plaintext);
        }

        return (string) $parsed;
    }
}

/* EOF: TocMarkupFixer.php */