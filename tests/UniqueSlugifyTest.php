<?php

namespace TOC;

use Cocur\Slugify\SlugifyInterface;
use PHPUnit\Framework\TestCase;

class UniqueSlugifyTest extends TestCase
{
    public function testInstantiateWithDefaults(): void
    {
        $slugger = new UniqueSlugify();
        $this->assertInstanceOf(SluggerInterface::class, $slugger);
        $this->assertInstanceOf(SlugifyInterface::class, $slugger);
    }

    public function testAsciiStrSlugify(): void
    {
        $slugger = new UniqueSlugify();
        $this->assertSame('abc123456', $slugger->makeSlug('abc123456'));
    }

    public function testMultipleStrings(): void
    {
        $slugger = new UniqueSlugify();
        $this->assertSame('test', $slugger->makeSlug('test'));
        $this->assertSame('test-1', $slugger->makeSlug('test'));
    }

    public function testUnicodeSlugify(): void
    {
        $slugger = new UniqueSlugify();
        $this->assertSame('s-c-o-u-g-i', $slugger->makeSlug('ş ç ö ü ğ ı'));
    }

    public function testSlugifyMethod(): void
    {
        $slugger = new UniqueSlugify();
        $this->assertSame('test', $slugger->slugify('test'));
        $this->assertSame('test-1', $slugger->slugify('test'));
    }
}
