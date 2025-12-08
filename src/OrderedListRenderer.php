<?php

/**
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

use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\MatcherInterface;
use Knp\Menu\Renderer\ListRenderer;
use Knp\Menu\Renderer\RendererInterface;

/**
 * Class OrderedListRenderer
 *
 * @package TOC
 */
class OrderedListRenderer implements RendererInterface
{
    private RendererInterface $innerListRenderer;

    /**
     * @param MatcherInterface $matcher
     * @param array<string,mixed> $defaultOptions
     * @param string|null $charset
     */
    public function __construct(
        protected MatcherInterface $matcher,
        protected array $defaultOptions,
        ?string $charset = null
    ) {
        $this->innerListRenderer = new ListRenderer($this->matcher, $this->defaultOptions, $charset);
    }

    public function render(ItemInterface $item, array $options = []): string
    {
        $output = $this->innerListRenderer->render($item, $options);
        return str_replace(['<ul', '</ul>'], ['<ol', '</ol>'], $output);
    }
}
