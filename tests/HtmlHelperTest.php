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

use Masterminds\HTML5;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Class HtmlHelperTest
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class HtmlHelperTest extends TestCase
{
    use HtmlHelper;

    #[dataProvider('isFullHtmlDocumentDataProvider')]
    public function testIsFullHtmlDocumentReturnsExpectedOutput(bool $expected, string $htmlMarkup): void
    {
        $this->assertEquals($expected, $this->isFullHtmlDocument($htmlMarkup));
    }

    /**
     * @return array<array{0: bool, 1: string}>
     */
    public static function isFullHtmlDocumentDataProvider(): array
    {
        return [
            [true,  '<html lang="en-US"><body><h1>Test</h1></body></html>'],
            [false, '<p>Test</p>'],
            [true,  '<html lang="en-US"><body class="test"><h2>Test</h2></body></html>']
        ];
    }

    public function testTraverseHeaderTagsReturnsExpectedOutput(): void
    {
        $h5 = new HTML5();
        $domDocument = $h5->loadHTML('<html lang="en-US"><body><h1>Test</h1><h2>Test2</h2></body></html>');
        $nodes = $this->traverseHeaderTags($domDocument, 1, 2);

        $this->assertEquals(2, $nodes->count());
    }
}
