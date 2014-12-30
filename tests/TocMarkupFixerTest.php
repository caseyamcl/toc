<?php
use TOC\TocMarkupFixer;

/**
 * Created by PhpStorm.
 * User: casey
 * Date: 12/30/14
 * Time: 1:24 PM
 */

class TocMarkupFixerTest extends PHPUnit_Framework_TestCase
{
    public function testInstantiateSucceeds()
    {
        $obj = new TocMarkupFixer();
        $this->assertInstanceOf('\TOC\TocMarkupFixer', $obj);
    }

    // ---------------------------------------------------------------

    public function testFixAddsIdsOnlyToElementsWithoutThem()
    {
        $obj = new TocMarkupFixer();

        $html = "<h1>No ID</h1><h2>Existing ID</h2><h3>Ignored</h3>";

        $this->assertEquals(
            '<h1 id="no-id">No ID</h1><h2 id="existing-id">Existing ID</h2><h3>Ignored</h3>',
            $obj->fix($html, 1, 2)
        );
    }

    // ---------------------------------------------------------------

    public function testFixDoesNotDuplicateIdsWhenFixing()
    {
        $obj = new TocMarkupFixer();

        $html = "<h1>FooBar</h1><h2>FooBar</h2><h3>FooBar</h3>";

        $this->assertEquals(
            '<h1 id="foobar">FooBar</h1><h2 id="foobar-1">FooBar</h2><h3 id="foobar-2">FooBar</h3>',
            $obj->fix($html, 1, 3)
        );
    }

    // ---------------------------------------------------------------

    public function testFixUsesTitleAttributeWhenAvailable()
    {
        $obj = new TocMarkupFixer();

        $html = "<h1>No ID</h1><h2 title='b'>Existing ID</h2><h3>Ignored</h3>";

        $this->assertEquals(
          '<h1 id="no-id">No ID</h1><h2 title=\'b\' id="b">Existing ID</h2><h3>Ignored</h3>',
          $obj->fix($html, 1, 2)
        );
    }
}
