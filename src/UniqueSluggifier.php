<?php
/**
 * Created by PhpStorm.
 * User: casey
 * Date: 12/30/14
 * Time: 12:48 PM
 */

namespace TOC;

use Cocur\Slugify\Slugify;

/**
 * UniqueSluggifier creates slugs text without repeating the same string twice per instance
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

        $ct = 1;
        $orig = $slugged;
        while (in_array($slugged, $this->used)) {
            $slugged = $orig . '-' . $ct;
            $ct++;
        }

        $this->used[] = $slugged;
        return $slugged;
    }
}

/* EOF: UniqueSluggifier.php */
