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

namespace TOC;

use Knp\Menu\ItemInterface;
use Knp\Menu\Renderer\ListRenderer;

use function str_repeat;

/**
 * Class OrderedListRenderer
 *
 * @package TOC
 */
class OrderedListRenderer extends ListRenderer
{
    /**
     * @param ItemInterface $item
     * @param array<string> $attributes
     * @param array<mixed> $options
     * @return string
     */
    protected function renderList(ItemInterface $item, array $attributes, array $options): string
    {
        if (!$item->hasChildren() || 0 === $options['depth'] || !$item->getDisplayChildren()) {
            return '';
        }

        $html = $this->format(
            '<ol' . $this->renderHtmlAttributes($attributes) . '>',
            'ol',
            $item->getLevel(),
            $options
        );

        $html .= $this->renderChildren($item, $options);
        $html .= $this->format('</ol>', 'ol', $item->getLevel(), $options);

        return $html;
    }

    /**
     * @param string $html
     * @param string $type
     * @param int $level
     * @param array<string, mixed> $options
     * @return string
     */
    protected function format(string $html, string $type, int $level, array $options): string
    {
        return $type === 'ol'
            ? str_repeat(' ', $level * 4) . $html . "\n"
            : parent::format($html, $type, $level, $options);
    }
}
