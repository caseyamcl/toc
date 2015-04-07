<?php
/**
 * toc
 *
 * @license ${LICENSE_LINK}
 * @link ${PROJECT_URL_LINK}
 * @version ${VERSION}
 * @package ${PACKAGE_NAME}
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * ------------------------------------------------------------------
 */

namespace TOC;

use Masterminds\HTML5;

/**
 * Class HtmlHelperTest
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class HtmlHelperTest extends \PHPUnit_Framework_TestCase
{
    use HtmlHelper;

    // ---------------------------------------------------------------

    public function testTraverseHeaderTagsReturnsExpectedOutput()
    {
        $h5 = new HTML5();
        $domDocument = $h5->loadHTML('<html><body><h1>Test</h1><h2>Test2</h2></body></html>');
        $nodes = $this->traverseHeaderTags($domDocument, 1, 2);

        $this->assertEquals(2, $nodes->count());
    }
}
