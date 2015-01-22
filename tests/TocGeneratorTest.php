<?php
/**
 * Created by PhpStorm.
 * User: casey
 * Date: 12/30/14
 * Time: 12:39 PM
 */

use Knp\Menu\Renderer\ListRenderer;
use TOC\TocGenerator;

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

    public function testGetItemsTraversesLevelsCorrectly()
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

    public function testGetItemsMatchesOnlyElementsWithIDs()
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
//
//    public function testGetItemsUsesTitleForDisplayTextWhenAvailableAndPlainTextWhenNot()
//    {
//        $obj = new TocGenerator();
//
//        $html  = '<h1 id="a" title="Foo Bar!">A Header</h1>';
//        $html .= '<h2 id="b">B Header</h2>';
//        $html .= '<h3 id="c" title="Baz Biz~">C Header</h3>';
//
//        $this->assertEquals(
//            ['a' => 'Foo Bar!', 'b' => 'B Header', 'c' => 'Baz Biz~'],
//            $obj->getItems($html, 1, 3)
//        );
//    }
//
//    // ---------------------------------------------------------------
//
//    public function testGetItemsGetsOnlyHeaderLevelsSpecified()
//    {
//        $obj = new TocGenerator();
//
//        $html  = '<h1 id="a" title="Foo Bar!">A Header</h1>';
//        $html .= '<h2 id="b">B Header</h2>';
//        $html .= '<h3 id="c" title="Baz Biz~">C Header</h3>';
//        $html .= '<h4 id="d" title="Bal Baf#">D Header</h4>';
//        $html .= '<h5 id="e" title="Cak Coy%">E Header</h5>';
//        $html .= '<h6 id="f" title="Dar Dul^">F Header</h6>';
//
//        $this->assertCount(1, $obj->getItems($html, 5, 1));
//        $this->assertCount(2, $obj->getItems($html, 5, 5));
//        $this->assertCount(6, $obj->getItems($html, -1, 20));
//    }
//
//    // ---------------------------------------------------------------
//
//    public function testGetHtmlItemsReturnsExpectedListItems()
//    {
//        $obj = new TocGenerator();
//
//        $html  = '<h1 id="a" title="Foo Bar!">A Header</h1>';
//        $html .= '<h2 id="b">B Header</h2>';
//        $html .= '<h3 id="c" title="Baz Biz~">C Header</h3>';
//
//        $this->assertEquals(
//            "<li><a title='Go to Foo Bar!' href='#a'>Foo Bar!</a></li><li><a title='Go to B Header' href='#b'>B Header</a></li><li><a title='Go to Baz Biz~' href='#c'>Baz Biz~</a></li>",
//           $obj->getHtmlItems($html, 1, 3)
//        );
//    }
//
//    // ---------------------------------------------------------------
//
//    public function testGetItemsReturnsAnEmptyArrayWhenNoContentOrMatches()
//    {
//        $obj = new TocGenerator();
//        $this->assertEquals([], $obj->getItems("<h1>Boo</h1><h2>Bar</h2>"));
//        $this->assertEquals([], $obj->getItems(""));
//    }

}

/* EOF: TocGeneratorTest.php */
