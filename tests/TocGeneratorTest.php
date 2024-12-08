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
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TOC\Util\TOCTestUtils;

/**
 * Class TocGeneratorTest
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class TocGeneratorTest extends TestCase
{
    public function testInstantiateSucceeds(): void
    {
        $obj = new TocGenerator();
        $this->assertInstanceOf(TocGenerator::class, $obj);
    }

    public function testDuplicateHeadingsAreEnumerated(): void
    {
        $obj = new TocGenerator();

        $html = "<h1 id='x'>A-Header</h1><h1 id='y'>A-Header</h1>";
        $this->assertCount(2, $obj->getMenu($html));
    }

    public function testGetMenuTraversesLevelsCorrectly(): void
    {
        $obj = new TocGenerator();

        $html = "
            <h1 id='a'>A-Header</h1><p>Foobar</p>
            <h2 id='b'>B-Header</h2>
            <h2 id='c'>C-Header</h2>
            <h3 id='d'>D-Header</h3>
            <h4 id='e'>E-Header</h4>
            <h2 id='f'>F-Header</h2>
            <h5 id='g'>G-Header</h5>
            <h1 id='h'>H-Header</h1><div>Hi</div>
        ";

        $fileResult = (array) file(__DIR__ . '/fixtures/testHtmlList.html');
        // @phpstan-ignore-next-line
        $fixture = array_filter(array_map('trim', $fileResult));
        $actual  = array_filter(array_map('trim', explode(PHP_EOL, $obj->getHtmlMenu($html))));

        $this->assertEquals($fixture, $actual);
    }

    public function testGetMenuDoesNotGenerateIDsForElementsWithoutIDs(): void
    {
        $html = "
            <h1 id='a'>A-Header</h1><p>Foobar</p>
            <h1 id='b'>B-Header</h1>
            <h1>C-Header</h1>
        ";

        $obj = new TocGenerator();
        $menu = $obj->getMenu($html, 1);

        $this->assertCount(2, $menu);
        $this->assertEquals('A-Header', $menu->getFirstChild()->getLabel());
        $this->assertEquals('B-Header', $menu->getLastChild()->getLabel());
    }

    public function testIDThatBeginsWithNumbersGeneratesCorrectMarkup(): void
    {
        $html = "
            <h1 id='1a'>A-Header</h1><p>Foobar</p>
            <h1 id='0b'>B-Header</h1>
            <h1>C-Header</h1>
        ";

        $menu = (new TocGenerator())->getMenu($html);
        $this->assertCount(2, $menu);
        $this->assertEquals('1a', $menu->getFirstChild()->getName());
        $this->assertEquals('0b', $menu->getLastChild()->getName());
    }

    public function testGetMenuUsesTitleForDisplayTextWhenAvailableAndPlainTextWhenNot(): void
    {
        $obj = new TocGenerator();

        $html  = '<h1 id="a" title="Foo Bar!">A Header</h1>';
        $html .= '<h2 id="b">B Header</h2>';
        $html .= '<h3 id="c" title="Baz Biz~">C Header</h3>';

        $menu = $obj->getMenu($html, 1, 3);
        $arr = TOCTestUtils::flattenMenuItems($menu);

        $this->assertEquals('Foo Bar!', $arr[0]->getLabel());
        $this->assertEquals('B Header', $arr[1]->getLabel());
        $this->assertEquals('Baz Biz~', $arr[2]->getLabel());
    }

    public function testGetMenuGetsOnlyHeaderLevelsSpecified(): void
    {
        $obj = new TocGenerator();

        $html  = '<h1 id="a" title="Foo Bar!">A Header</h1>';
        $html .= '<h2 id="b">B Header</h2>';
        $html .= '<h3 id="c" title="Baz Biz~">C Header</h3>';
        $html .= '<h4 id="d" title="Bal Baf#">D Header</h4>';
        $html .= '<h5 id="e" title="Cak Coy%">E Header</h5>';
        $html .= '<h6 id="f" title="Dar Dul^">F Header</h6>';

        $this->assertCount(1, TOCTestUtils::flattenMenuItems($obj->getMenu($html, 5, 1)));
        $this->assertCount(2, TOCTestUtils::flattenMenuItems($obj->getMenu($html, 5, 5)));

        // What's up with this?
        //$this->assertCount(6, TOCTestUtils::flattenMenuItems($obj->getMenu($html, -1, 20)));
    }

    public function testGetMenuReturnsEmptyMenuItemWhenNoContentOrMatches(): void
    {
        $obj = new TocGenerator();
        $this->assertCount(0, $obj->getMenu("<h1>Boo</h1><h2>Bar</h2>"));
        $this->assertCount(0, $obj->getMenu(""));
    }

    public function testGetOrderedMenu(): void
    {
        $obj = new TocGenerator();
        $html = "<h1 id='x'>A-Header</h1><h1 id='y'>A-Header</h1>";
        $menuHtml = $obj->getOrderedHtmlMenu($html, 1, 6, null);
        $this->assertStringStartsWith('<ol>', $menuHtml);
    }

    /**
     * @param ItemInterface $menuItem
     * @param int $expectedTopLevelItems
     * @param int $expectedSubItems
     */
    #[dataProvider('unusedHeadingLevelsAreTrimmedDataProvider')]
    public function testUnusedHeadingLevelsAreTrimmedFromGeneratedMenu(
        ItemInterface $menuItem,
        int $expectedTopLevelItems,
        int $expectedSubItems = 0
    ): void {
        $this->assertCount($expectedTopLevelItems, $menuItem->getChildren());

        if ($expectedSubItems > 0) {
            $this->assertCount($expectedSubItems, $menuItem->getFirstChild()->getChildren());
        }
    }

    /**
     * @return array<int,array<int|ItemInterface>>
     */
    public static function unusedHeadingLevelsAreTrimmedDataProvider(): array
    {
        $obj = new TocGenerator();

        return [
            [
                $obj->getMenu(
                    "<h3 id='a'>A-Header</h3><h4 id='y'>Y-Header</h4><h4 id='z'>Z-Header</h4><h3 id='x'>X-Header</h3>",
                    1,
                    6
                ), 2, 2
            ],
            [$obj->getMenu("<h1 id='x'>X-Header</h1>", 1, 6), 1],
            [$obj->getMenu('<h5 id="x">X-Header</h5><h5 id="y">Y-Header</h5>', 1, 6), 2],
            [$obj->getMenu('<h6 id="y">Y-Header</h6>', 1, 5), 0],
            [$obj->getMenu("<h1 /><h2 id='x'>X-Header</h2>", 1, 6), 1, 0]
        ];
    }
}
