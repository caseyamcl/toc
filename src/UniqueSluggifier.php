<?php

/**
 *
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

use Cocur\Slugify\Slugify;

/**
 * UniqueSluggifier creates slugs from text without repeating the same slug twice per instance
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class UniqueSluggifier
{
    /**
     * @var \Cocur\Slugify\Slugify
     */
    private $slugify;

    /**
     * @var array
     */
    private $used;

    // ---------------------------------------------------------------

    /**
     * Constructor
     *
     * @param \Cocur\Slugify\Slugify $slugify
     */
    public function __construct(Slugify $slugify = null)
    {
        $this->used = array();
        $this->slugify = $slugify ?: new Slugify();
    }

    // ---------------------------------------------------------------

    /**
     * Slugify
     *
     * @param string $text
     * @return string
     */
    public function slugify($text)
    {
        $slugged = $this->slugify->slugify($text);

        $count = 1;
        $orig = $slugged;
        while (in_array($slugged, $this->used)) {
            $slugged = $orig . '-' . $count;
            $count++;
        }

        $this->used[] = $slugged;
        return $slugged;
    }
}

/* EOF: UniqueSluggifier.php */
