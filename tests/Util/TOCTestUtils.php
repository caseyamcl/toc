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

namespace TOC\Util;

use Knp\Menu\ItemInterface;

class TOCTestUtils
{
    /**
     * Get a flattened array containing references to all of the items
     *
     * @param ItemInterface $item   The menu item
     * @param bool          $isTop  Is the initial menu item starting at the top-level?
     * @return array<ItemInterface>
     */
    public static function flattenMenuItems(ItemInterface $item, $isTop = true): array
    {
        $arr = $isTop ? [] : [$item];

        foreach ($item->getChildren() as $child) {
            $arr = array_merge($arr, self::flattenMenuItems($child, false));
        }

        return $arr;
    }
}
