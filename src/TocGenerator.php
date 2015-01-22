<?php

// Header here
// ---------------------------------------------------------------

namespace TOC;

use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\Matcher;
use Knp\Menu\MenuFactory;
use Knp\Menu\Renderer\ListRenderer;
use Knp\Menu\Renderer\RendererInterface;
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
     * Get Menu
     *
     * Returns a KNP Menu object, which can be traversed or rendered
     *
     * @param string  $markup    Content to get items fro $this->getItems($markup, $topLevel, $depth)m
     * @param int     $topLevel  Top Header (1 through 6)
     * @param int     $depth     Depth (1 through 6)
     * @return ItemInterface     KNP Menu
     */
    public function getMenu($markup, $topLevel = 1, $depth = 6)
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
     * @param string            $markup   Content to get items from
     * @param int               $topLevel Top Header (1 through 6)
     * @param int               $depth    Depth (1 through 6)
     * @param RendererInterface $renderer
     * @return string HTML <LI> items
     */
    public function getHtmlMenu($markup, $topLevel = 1, $depth = 6, RendererInterface $renderer = null)
    {
        if ( ! $renderer) {
            $renderer = new ListRenderer(new Matcher(), [
                'currentClass'  => 'active',
                'ancestorClass' => 'active_ancestor'
            ]);
        }

        return $renderer->render($this->getMenu($markup, $topLevel, $depth));
    }
}

/* EOF: TocGenerator.php */
