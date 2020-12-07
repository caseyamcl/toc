<?php

/**
 *
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

use Cocur\Slugify\Slugify;
use Cocur\Slugify\SlugifyInterface;

/**
 * UniqueSluggifier creates slugs from text without repeating the same slug twice per instance
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class UniqueSlugify implements SlugifyInterface
{
    /**
     * @var SlugifyInterface
     */
    private $slugify;

    /**
     * @var array
     */
    private $used;

    /**
     * Constructor
     *
     * @param SlugifyInterface|null $slugify
     */
    public function __construct(?SlugifyInterface $slugify = null)
    {
        $this->used = array();
        $this->slugify = $slugify ?: new Slugify();
    }

    /**
     * Slugify
     *
     * @param string $text
     * @param null $options
     * @return string
     */
    public function slugify($text, $options = null): string
    {
        $slugged = $this->slugify->slugify($text, $options);

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
