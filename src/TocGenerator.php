<?php

// Header here
// ---------------------------------------------------------------

namespace TOC;

use Knp\Menu\MenuFactory;
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

    /**
     * @var \Knp\Menu\MenuFactory
     */
    private $menuFactory;

    // ---------------------------------------------------------------

    /**
     * Constructor
     *
     * @param \Knp\Menu\MenuFactory          $menuFactory
     * @param \Sunra\PhpSimple\HtmlDomParser $domParser
     */
    public function __construct(MenuFactory $menuFactory = null, HtmlDomParser $domParser = null)
    {
        $this->domParser   = $domParser ?: new HtmlDomParser();
        $this->menuFactory = $menuFactory ?: new MenuFactory();
    }

    // ---------------------------------------------------------------

    /**
     * Get Link Items
     *
     * Returns a multi-level associative array of items
     *
     * @TODO: TEST THIS OUT >> And then refactor the getHtmlItems method to use the built-in KNP List library to do so...
     *
     * @param string  $markup    Content to get items from
     * @param int     $topLevel  Top Header (1 through 6)
     * @param int     $depth     Depth (1 through 6)
     * @return \Traversable  Menu items
     */
    public function getItems($markup, $topLevel = 1, $depth = 6)
    {
        // Setup an empty menu object
        $menu = $this->menuFactory->createItem('TOC');

        // Empty?  Do nothing.
        if (trim($markup) == '') {
            return [];
        }

        // Parse HTML
        $tagsToMatch   = $this->determineHeaderTags($topLevel, $depth);
        $parsed = $this->domParser->str_get_html($markup);

        // Runtime exception for bad code
        if ( ! $parsed) {
            throw new RuntimeException("Could not parse HTML");
        }

        // Extract items

        // Initial settings
        $lastElem = $menu;

        // Do it...
        foreach ($parsed->find(implode(', ', $tagsToMatch)) as $element) {

            // Skip items without IDs
            if ( ! $element->id) {
                continue;
            }

            // Get the TagName and the level
            $tagName = $element->tag;
            $level   = array_search(strtolower($tagName), $tagsToMatch) + 1;

            // TEST DEBUG
            var_dump($element->plaintext . '; ' . $tagName . ' is level ' . $level . ' and lastLevel is ' . $lastElem->getLevel());

            // Determine parent item which to add child
            if ($level == 0) {
                $parent = $menu;
            }
            elseif ($level == $lastElem->getLevel()) {
                $parent = $lastElem->getParent();
            }
            elseif ($level > $lastElem->getLevel()) {
                $parent = $lastElem;
                for ($i = $lastElem->getLevel(); $i < ($level - 1); $i++) {
                    $parent = $parent->addChild('');
                }
            }
            else { //if ($level < $lastElem->getLevel())
                $parent = $lastElem->getParent();
                while ($parent->getLevel() > $level - 1) {
                    $parent = $parent->getParent();
                }
            }

            $lastElem = $parent->addChild($element->title ?: $element->plaintext, ['uri' => '#' . $element->id]);
        }

        return $menu;
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
    public function getHtmlItems($markup, $topLevel = 1, $depth = 6, $titleTemplate = 'Go to %s')
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
