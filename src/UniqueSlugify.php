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

use Cocur\Slugify\SlugifyInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\String\Slugger\SluggerInterface as SymfonyStringSluggerInterface;

/**
 * UniqueSlugify creates slugs from text without repeating the same slug twice per instance
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class UniqueSlugify implements SluggerInterface, SlugifyInterface
{
    /**
     * @var SymfonyStringSluggerInterface
     */
    private $slugger;

    /**
     * @var array
     */
    private $used;

    /**
     * Constructor
     *
     * @param SymfonyStringSluggerInterface|null $slugger
     */
    public function __construct(?SymfonyStringSluggerInterface $slugger = null)
    {
        $this->used = array();
        $this->slugger = $slugger ?: new AsciiSlugger();
    }

    public function slugify(string $string, $options = null): string
    {
        return $this->makeSlug($string);
    }

    /**
     * Slugify
     *
     * @param string $string
     * @return string
     */
    public function makeSlug(string $string): string
    {
        $slugged = $this->slugger->slug($string)->lower()->toString();

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
