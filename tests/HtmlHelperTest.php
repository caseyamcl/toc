<?php

/**
 *
 * PHP TableOfContents Library
 *
 * @license http://opensource.org/licenses/MIT
 * @link https://github.com/caseyamcl/toc
 * @version 2
 * @package caseyamcl/toc
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * ------------------------------------------------------------------
 */

namespace TOC;

use Masterminds\HTML5;
use PHPUnit\Framework\TestCase;

/**
 * Class HtmlHelperTest
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class HtmlHelperTest extends TestCase
{
    use HtmlHelper;

    /**
     * @dataProvider isFullHtmlDocumentDataProvider
     */
    public function testIsFullHtmlDocumentReturnsExpectedOutput($expected, $htmlMarkup)
    {
        $this->assertEquals($expected, $this->isFullHtmlDocument($htmlMarkup));
    }

    public function isFullHtmlDocumentDataProvider()
    {
        return [
            [true,  '<html><body><h1>Test</h1></body></html>'],
            [false, '<p>Test</p>'],
            [true,  '<html><body class="test"><h2>Test</h2></body></html>']
        ];
    }

    public function testTraverseHeaderTagsReturnsExpectedOutput()
    {
        $h5 = new HTML5();
        $domDocument = $h5->loadHTML('<html><body><h1>Test</h1><h2>Test2</h2></body></html>');
        $nodes = $this->traverseHeaderTags($domDocument, 1, 2);

        $this->assertEquals(2, $nodes->count());
    }
}
