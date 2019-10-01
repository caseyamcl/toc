<?php

/**
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

use PHPUnit\Framework\TestCase;

/**
 * Markup Fixer Test
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class MarkupFixerTest extends TestCase
{
    public function testInstantiateSucceeds()
    {
        $obj = new MarkupFixer();
        $this->assertInstanceOf('\TOC\MarkupFixer', $obj);
    }

    public function testFixAddsIdsOnlyToElementsWithoutThem()
    {
        $obj = new MarkupFixer();

        $html = "<h1>No ID</h1><h2 id='it-exists'>Existing ID</h2><h3>Ignored</h3>";

        $this->assertEquals(
            '<h1 id="no-id">No ID</h1><h2 id="it-exists">Existing ID</h2><h3>Ignored</h3>',
            $obj->fix($html, 1, 2)
        );
    }

    public function testFixDoesNotDuplicateIdsWhenFixing()
    {
        $obj = new MarkupFixer();

        $html = "<h1>FooBar</h1><h2>FooBar</h2><h3>FooBar</h3>";

        $this->assertEquals(
            '<h1 id="foobar">FooBar</h1><h2 id="foobar-1">FooBar</h2><h3 id="foobar-2">FooBar</h3>',
            $obj->fix($html, 1, 3)
        );
    }

    public function testFixUsesTitleAttributeWhenAvailable()
    {
        $obj = new MarkupFixer();

        $html = "<h1>No ID</h1><h2 title='b'>Existing ID</h2><h3>Ignored</h3>";

        $this->assertEquals(
            '<h1 id="no-id">No ID</h1><h2 title="b" id="b">Existing ID</h2><h3>Ignored</h3>',
            $obj->fix($html, 1, 2)
        );
    }

    /**
     * This test ensures that line endings in the DOM content aren't destroyed
     *
     * Destroying line-endings can break pre.../pre tag output
     */
    public function testFixDoesNotEraseLineEndingsBetweenPreTags()
    {
        $htmlContent = file_get_contents(__DIR__ . '/fixtures/htmlWithPre.html');

        $obj = new MarkupFixer();
        $out = $obj->fix($htmlContent, 1, 2);

        preg_match('/\<pre\>(.+?)\<\/pre\>/s', $out, $matches);
        $this->assertEquals(3, preg_match_all("/(\n|\r\n)/s", $matches[1]));
    }

    public function testFixPreservesNonStandardHtmlAttributes()
    {
        $htmlContent = file_get_contents(__DIR__ . '/fixtures/htmlWithVueCode.html');
        $obj = new MarkupFixer();
        $out = $obj->fix($htmlContent, 1, 2);
        $this->assertStringContainsString('v-on:click', $out);
        $this->assertStringContainsString(':class', $out);
        $this->assertStringContainsString('v-cloak', $out);
        $this->assertStringContainsString('{{ item.markup }}', $out);
        $this->assertStringContainsString('v-for', $out);
    }
}
