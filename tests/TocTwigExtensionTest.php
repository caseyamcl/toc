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
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 *
 * ------------------------------------------------------------------
 */

declare(strict_types=1);

namespace TOC;

use Exception;
use Knp\Menu\ItemInterface;
use PHPUnit\Framework\TestCase;
use Twig_SimpleFilter;
use Twig_SimpleFunction;

/**
 * TOC Twig Extensions Test
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class TocTwigExtensionTest extends TestCase
{
    public function testInstantiateSucceeds(): void
    {
        $obj = new TocTwigExtension();
        $this->assertInstanceOf('\TOC\TocTwigExtension', $obj);
    }

    public function testGetFiltersContainsExpectedFilters(): void
    {
        $obj = new TocTwigExtension();
        $expected = ['add_anchors'];

        $this->assertEquals(count($expected), count(array_map(function (Twig_SimpleFilter $v) {
            return $v->getName();
        }, $obj->getFilters())));
    }



    public function testGetFunctionsReturnsExpectedFunctions(): void
    {
        $obj = new TocTwigExtension();
        $expected = ['toc', 'toc_items', 'add_anchors', 'toc_ordered'];

        $this->assertEquals(count($expected), count(array_map(function (Twig_SimpleFunction $v) {
            return $v->getName();
        }, $obj->getFunctions())));
    }

    /**
     * @throws Exception
     */
    public function testTwigTocFunctionReturnsString(): void
    {
        $func = $this->findFunctionByName(new TocTwigExtension(), 'toc');
        $result = $func->getCallable()->__invoke("<h1 id='a'>hi</h1><h2 id='b'>bye</h2>");

        $this->assertIsString($result);
    }

    public function testTwigTocOrderedFunctionReturnsString(): void
    {
        $func = $this->findFunctionByName(new TocTwigExtension(), 'toc_ordered');
        $result = $func->getCallable()->__invoke("<h1 id='a'>hi</h1><h2 id='b'>bye</h2>");
        $this->assertIsString($result);
    }

    /**
     * @throws Exception
     */
    public function testTwigTocItemsFunctionReturnsKnpMenuItem(): void
    {
        $func = $this->findFunctionByName(new TocTwigExtension(), 'toc_items');
        $result = $func->getCallable()->__invoke("<h1 id='a'>hi</h1><h2 id='b'>bye</h2>");

        $this->assertInstanceOf(ItemInterface::class, $result);
    }


    /**
     * @throws Exception
     */
    public function testTwigAddAnchorsFunctionReturnsString(): void
    {
        $func = $this->findFunctionByName(new TocTwigExtension(), 'add_anchors');
        $result = $func->getCallable()->__invoke("<h1>hi</h1><h2>bye</h2>");

        $this->assertIsString($result);
    }


    /**
     * @throws Exception
     */
    public function testTwigAddAnchorsFilterReturnsString(): void
    {
        $filter = $this->findFilterByName(new TocTwigExtension(), 'add_anchors');
        $result = $filter->getCallable()->__invoke("<h1>hi</h1><h2>bye</h2>");

        $this->assertIsString($result);
    }



    public function testGetNameReturnsExpectedName(): void
    {
        $obj = new TocTwigExtension();
        $this->assertEquals('toc', $obj->getName());
    }


    /**
     * @param TocTwigExtension $ext
     * @param $name
     * @return mixed|Twig_SimpleFunction
     * @throws Exception
     */
    private function findFunctionByName(TocTwigExtension $ext, $name)
    {
        foreach ($ext->getFunctions() as $func) {
            if ($name == $func->getName()) {
                return $func;
            }
        }

        throw new Exception("Invalid function name: " . $name);
    }


    /**
     * @param TocTwigExtension $ext
     * @param $name
     * @return mixed|Twig_SimpleFilter
     * @throws Exception
     */
    private function findFilterByName(TocTwigExtension $ext, $name)
    {
        foreach ($ext->getFilters() as $filter) {
            if ($name == $filter->getName()) {
                return $filter;
            }
        }

        throw new Exception("Invalid filter name: " . $name);
    }
}
