<?php

/**
 *
 * PHP TableOfContents Library
 *
 * @license http://opensource.org/licenses/MIT
 * @link https://github.com/caseyamcl/toc
 * @version 4
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

use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\String\Slugger\SluggerInterface as SymfonyStringSluggerInterface;

/**
 * UniqueSlugify creates slugs from text without repeating the same slug twice per instance
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class UniqueSlugger implements SluggerInterface
{
    private SymfonyStringSluggerInterface $slugger;

    /**
     * @var array<string>
     */
    private array $used = [];

    /**
     * Constructor
     *
     * @param SymfonyStringSluggerInterface|null $slugger
     */
    public function __construct(?SymfonyStringSluggerInterface $slugger = null)
    {
        $this->slugger = $slugger ?: new AsciiSlugger();
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

    public function reset(): void
    {
        $this->used = [];
    }
}
