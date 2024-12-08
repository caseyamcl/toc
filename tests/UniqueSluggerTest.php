<?php

namespace TOC;

use PHPUnit\Framework\TestCase;

class UniqueSluggerTest extends TestCase
{
    public function testInstantiateWithDefaults(): void
    {
        $slugger = new UniqueSlugger();
        $this->assertInstanceOf(SluggerInterface::class, $slugger);
    }

    public function testAsciiStrSlugify(): void
    {
        $slugger = new UniqueSlugger();
        $this->assertSame('abc123456', $slugger->makeSlug('abc123456'));
    }

    public function testMultipleStrings(): void
    {
        $slugger = new UniqueSlugger();
        $this->assertSame('test', $slugger->makeSlug('test'));
        $this->assertSame('test-1', $slugger->makeSlug('test'));
    }

    public function testUnicodeSlugify(): void
    {
        $slugger = new UniqueSlugger();
        $this->assertSame('s-c-o-u-g-i', $slugger->makeSlug('ş ç ö ü ğ ı'));
    }
}
