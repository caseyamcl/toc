<?php
/**
 * Created by PhpStorm.
 * User: casey
 * Date: 12/30/14
 * Time: 12:39 PM
 */

namespace TOC;

use TOC\Util\TOCTestUtils;

/**
 * Class TocGeneratorTest
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class TocGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiateSucceeds()
    {
        $obj = new TocGenerator();
        $this->assertInstanceOf('\TOC\TocGenerator', $obj);
    }

    // ---------------------------------------------------------------

    public function testGetMenuTraversesLevelsCorrectly()
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

        $fixture = array_filter(array_map('trim', file(__DIR__ . '/fixtures/testHtmlList.html')));
        $actual  = array_filter(array_map('trim', explode(PHP_EOL, $obj->getHtmlMenu($html, 1, 6))));

        $this->assertEquals($fixture, $actual);
    }

    // ---------------------------------------------------------------

    public function testGetMenuMatchesOnlyElementsWithIDs()
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

    // ---------------------------------------------------------------

    public function testGetMenuUsesTitleForDisplayTextWhenAvailableAndPlainTextWhenNot()
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

    // ---------------------------------------------------------------

    public function testGetMenuGetsOnlyHeaderLevelsSpecified()
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

    // ---------------------------------------------------------------

    public function testGetMenuReturnsAnEmptyArrayWhenNoContentOrMatches()
    {
        $obj = new TocGenerator();
        $this->assertEquals(0, count($obj->getMenu("<h1>Boo</h1><h2>Bar</h2>")));
        $this->assertEquals(0, count($obj->getMenu("")));
    }
}

/* EOF: TocGeneratorTest.php */
