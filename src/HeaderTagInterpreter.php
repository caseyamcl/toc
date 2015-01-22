<?php
/**
 * Created by PhpStorm.
 * User: casey
 * Date: 12/30/14
 * Time: 12:32 PM
 */

namespace TOC;

/**
 * Interprets header tags
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
