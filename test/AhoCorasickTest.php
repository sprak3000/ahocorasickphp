<?php
use PHPUnit\Framework\TestCase;

require 'AhoCorasick.php';

class AhoCorasickTest extends TestCase
{
    protected $ahoCorasick;

    public function setUp()
    {
        $this->ahoCorasick = new AhoCorasick();

        parent::setUp();
    }

    public function testSearch()
    {
        $expectedResult = [
            ['cart', 2],
            ['art', 3],
            ['ted', 5],
            ['art', 10],
            ['ted', 27],
        ];

        $this->ahoCorasick->addNeedle('art');
        $this->ahoCorasick->addNeedle('cart');
        $this->ahoCorasick->addNeedle('ted');
        $this->ahoCorasick->finalize();

        $found = $this->ahoCorasick->search('a carted mart lot one blue ted');

        $this->assertSame($expectedResult, $found);
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Must call finalize() before search.
     */
    public function testCannotSearchUnlessFinalized()
    {
        $this->ahoCorasick->addNeedle('art');
        $this->ahoCorasick->addNeedle('cart');
        $this->ahoCorasick->addNeedle('ted');

        $this->ahoCorasick->search('a carted mart lot one blue ted');
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Cannot add word to finalized ahocorasick.
     */
    public function testCannotAddNeedleAfterFinalize()
    {
        $this->ahoCorasick->addNeedle('art');
        $this->ahoCorasick->addNeedle('cart');
        $this->ahoCorasick->addNeedle('ted');

        $this->ahoCorasick->finalize();

        $this->ahoCorasick->addNeedle('mart');
    }
}
