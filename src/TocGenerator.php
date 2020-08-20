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

use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\Matcher;
use Knp\Menu\MenuFactory;
use Knp\Menu\MenuItem;
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

    private const DEFAULT_NAME = 'TOC';

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
     * @param MenuFactory|null $menuFactory
     * @param HTML5|null $htmlParser
     */
    public function __construct(?MenuFactory $menuFactory = null, ?HTML5 $htmlParser = null)
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
    public function getMenu(string $markup, int $topLevel = 1, int $depth = 6): ItemInterface
    {
        // Setup an empty menu object
        $menu = $this->menuFactory->createItem(static::DEFAULT_NAME);

        // Empty?  Return empty menu item
        if (trim($markup) == '') {
            return $menu;
        }

        // Parse HTML
        $tagsToMatch = $this->determineHeaderTags($topLevel, $depth);

        // Initial settings
        $lastElem = $menu;

        // Do it...
        $domDocument = $this->domParser->loadHTML($markup);
        foreach ($this->traverseHeaderTags($domDocument, $topLevel, $depth) as $i => $node) {
            // Skip items without IDs
            if (! $node->hasAttribute('id')) {
                continue;
            }

            // Get the TagName and the level
            $tagName = $node->tagName;
            $level   = array_search(strtolower($tagName), $tagsToMatch) + 1;

            // Determine parent item which to add child
            /** @var MenuItem $parent */
            if ($level == 1) {
                $parent = $menu;
            } elseif ($level == $lastElem->getLevel()) {
                $parent = $lastElem->getParent();
            } elseif ($level > $lastElem->getLevel()) {
                $parent = $lastElem;
                for ($i = $lastElem->getLevel(); $i < ($level - 1); $i++) {
                    $parent = $parent->addChild('');
                }
            } else { //if ($level < $lastElem->getLevel())
                $parent = $lastElem->getParent();
                while ($parent->getLevel() > $level - 1) {
                    $parent = $parent->getParent();
                }
            }

            $lastElem = $parent->addChild(
                $node->getAttribute('id'),
                [
                    'label' => $node->getAttribute('title') ?: $node->textContent,
                    'uri' => '#' . $node->getAttribute('id')
                ]
            );
        }

        return $this->trimMenu($menu);
    }

    /**
     * Trim empty items from the menu
     *
     * @param ItemInterface $menuItem
     * @return ItemInterface
     */
    protected function trimMenu(ItemInterface $menuItem): ItemInterface
    {
        // if any of these circumstances are true, then just bail and return the menu item
        if (
            count($menuItem->getChildren()) === 0
            or count($menuItem->getChildren()) > 1
            or ! empty($menuItem->getFirstChild()->getLabel())
        ) {
            return $menuItem;
        }

        // otherwise, find the first level where there is actual content and use that.
        while (count($menuItem->getChildren()) == 1 && empty($menuItem->getFirstChild()->getLabel())) {
            $menuItem = $menuItem->getFirstChild();
        }

        return $menuItem;
    }

    /**
     * Get HTML menu in unordered list form
     *
     * @param string $markup Content to get items from
     * @param int $topLevel Top Header (1 through 6)
     * @param int $depth Depth (1 through 6)
     * @param RendererInterface|null $renderer
     * @param bool $ordered
     * @return string HTML <li> items
     */
    public function getHtmlMenu(
        string $markup,
        int $topLevel = 1,
        int $depth = 6,
        ?RendererInterface $renderer = null,
        bool $ordered = false
    ): string {
        if (! $renderer) {
            $options = ['currentClass'  => 'active', 'ancestorClass' => 'active_ancestor'];
            $renderer = $ordered
                ? new OrderedListRenderer(new Matcher(), $options)
                : new ListRenderer(new Matcher(), $options);
        }

        $menu = $this->getMenu($markup, $topLevel, $depth);
        return $renderer->render($menu);
    }

    /**
     * Get HTML menu in ordered list form
     *
     * @param string $markup Content to get items from
     * @param int $topLevel Top Header (1 through 6)
     * @param int $depth Depth (1 through 6)
     * @param RendererInterface|null $renderer
     * @return string HTML <li> items
     */
    public function getOrderedHtmlMenu(
        string $markup,
        int $topLevel = 1,
        int $depth = 6,
        RendererInterface $renderer = null
    ): string {
        return $this->getHtmlMenu($markup, $topLevel, $depth, $renderer, true);
    }
}
