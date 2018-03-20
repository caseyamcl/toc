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

use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\Matcher;
use Knp\Menu\MenuFactory;
use Knp\Menu\Renderer\ListRenderer;
use Knp\Menu\Renderer\RendererInterface;
use Masterminds\HTML5;

/**
 * Table Of Contents Generator generates TOCs from HTML Markup
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class TocGenerator
{
    use HtmlHelper;



    /**
     * @var HTML5
     */
    private $domParser;

    /**
     * @var MenuFactory
     */
    private $menuFactory;



    /**
     * Constructor
     *
     * @param MenuFactory $menuFactory
     * @param HTML5       $htmlParser
     */
    public function __construct(MenuFactory $menuFactory = null, HTML5 $htmlParser = null)
    {
        $this->domParser   = $htmlParser  ?: new HTML5();
        $this->menuFactory = $menuFactory ?: new MenuFactory();
    }



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
        $tagsToMatch = $this->determineHeaderTags($topLevel, $depth);


        // Initial settings
        $lastElem = $menu;

        // Do it...
        $domDocument = $this->domParser->loadHTML($markup);
        foreach ($this->traverseHeaderTags($domDocument, $topLevel, $depth) as $node) {

            // Skip items without IDs
            if ( ! $node->hasAttribute('id')) {
                continue;
            }

            // Get the TagName and the level
            $tagName = $node->tagName;
            $level   = array_search(strtolower($tagName), $tagsToMatch) + 1;

            // Determine parent item which to add child
            if ($level == 1) {
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

            $lastElem = $parent->addChild(
                $node->getAttribute('title') ?: $node->textContent,
                ['uri' => '#' . $node->getAttribute('id')]
            );
        }

        return $menu;
    }



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
