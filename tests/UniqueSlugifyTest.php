<?php

namespace TOC;

use PHPUnit\Framework\TestCase;

class UniqueSlugifyTest extends TestCase
{
    public function testInstantiateWithDefaults(): void
    {
        $slugger = new UniqueSlugify();
        $this->assertInstanceOf(UniqueSlugify::class, $slugger);
    }

    public function testAsciiStrSlugify(): void
    {
        $slugger = new UniqueSlugify();
        $this->assertSame('abc123456', $slugger->slugify('abc123456'));
    }

    public function testMultipleStrings(): void
    {
        $slugger = new UniqueSlugify();
        $this->assertSame('test', $slugger->slugify('test'));
        $this->assertSame('test-1', $slugger->slugify('test'));
    }

    public function testUnicodeSlugify(): void
    {
        $slugger = new UniqueSlugify();
        $this->assertSame('s-c-oe-ue-g-i', $slugger->slugify('ş ç ö ü ğ ı'));
    }
}
