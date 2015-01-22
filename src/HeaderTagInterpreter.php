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

/**
 * Trait that interprets HTML Header tags from a list of integers
 *
 * @package TOC
 */
trait HeaderTagInterpreter
{
    /**
     * Convert a topLevel and depth to H1..H6 tags array
     *
     * @param int $topLevel
     * @param int $depth
     * @return array  Array of header tags; ex: ['h1', 'h2', 'h3']
     */
    protected function determineHeaderTags($topLevel, $depth)
    {
        $desired = range((int) $topLevel, (int) $topLevel + ((int) $depth - 1));
        $allowed = [1, 2, 3, 4, 5, 6];

        return array_map(function($val) { return 'h'.$val; }, array_intersect($desired, $allowed));
    }
}
