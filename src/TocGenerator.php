<?php

// Header here
// ---------------------------------------------------------------

namespace TOC;

use Sunra\PhpSimple\HtmlDomParser;
use RuntimeException;

/**
 * Class TocGenerator
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class TocGenerator
{
    use HeaderTagInterpreter;

    // ---------------------------------------------------------------

    /**
     * @var \Sunra\PhpSimple\HtmlDomParser
     */
    private $domParser;

    // ---------------------------------------------------------------

    /**
     * Constructor
     *
     * @param \Sunra\PhpSimple\HtmlDomParser $domParser
     */
    public function __construct(HtmlDomParser $domParser = null)
    {
        $this->domParser = $domParser ?: new HtmlDomParser();
    }

    // ---------------------------------------------------------------

    /**
     * Get Link Items
     *
     * @param string  $markup    Content to get items from
     * @param int     $topLevel  Top Header (1 through 6)
     * @param int     $depth     Depth (1 through 6)
     * @return array  Array of items  ['anchor' => 'display text', ...]
     */
    public function getItems($markup, $topLevel = 1, $depth = 2)
    {
        // Empty?  Do nothing.
        if (trim($markup) == '') {
            return [];
        }

        // Parse HTML
        $items  = [];
        $tags   = $this->determineHeaderTags($topLevel, $depth);
        $parsed = $this->domParser->str_get_html($markup);

        // Runtime exception for bad code
        if ( ! $parsed) {
            throw new RuntimeException("Could not parse HTML");
        }

        // Extract items
        foreach ($parsed->find(implode(', ', $tags)) as $tag) {

            if ( ! $tag->id) {
                continue;
            }

            $dispText = $tag->title ?: $tag->plaintext;
            $items[$tag->id] = $dispText;
        }

        return $items;
    }

    // ---------------------------------------------------------------

    /**
     * Get HTML Links in list form
     *
     * @param string   $markup    Content to get items from
     * @param int      $topLevel  Top Header (1 through 6)
     * @param int      $depth     Depth (1 through 6)
     * @return string  HTML <LI> items
     */
    public function getHtmlItems($markup, $topLevel = 1, $depth = 2, $titleTemplate = 'Go to %s')
    {
        $arr = [];

        foreach ($this->getItems($markup, $topLevel, $depth) as $anchor => $displayText) {
            $arr[] = sprintf(
                "<li><a title='%s' href='#%s'>%s</a></li>",
                sprintf($titleTemplate, $displayText),
                $anchor,
                $displayText
            );
        }

        return implode('', $arr);
    }
}

/* EOF: TocGenerator.php */