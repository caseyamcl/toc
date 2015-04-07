<?php
/**
 * toc
 *
 * @license ${LICENSE_LINK}
 * @link ${PROJECT_URL_LINK}
 * @version ${VERSION}
 * @package ${PACKAGE_NAME}
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * ------------------------------------------------------------------
 */

namespace TOC\Util;

use Knp\Menu\ItemInterface;
use Knp\Menu\MenuItem;

class TOCTestUtils
{
    /**
     * Get a flattened array containing references to all of the items
     *
     * @param ItemInterface $item   The menu item
     * @param bool          $isTop  Is the initial menu item starting at the top-level?
     * @return array|MenuItem[]
     */
    public static function flattenMenuItems(ItemInterface $item, $isTop = true)
    {
        $arr = $isTop ? [] : [$item];

        foreach ($item->getChildren() as $child) {
            $arr = array_merge($arr, self::flattenMenuItems($child, false));
        }

        return $arr;
    }
}
