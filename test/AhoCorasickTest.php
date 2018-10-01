<?php
use PHPUnit\Framework\TestCase;
use codeplea\AhoCorasick\Search;

class AhoCorasickTest extends TestCase
{
    protected $search;

    public function setUp()
    {
        $this->search = new Search();

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

        $this->search->addNeedle('art');
        $this->search->addNeedle('cart');
        $this->search->addNeedle('ted');
        $this->search->finalize();

        $found = $this->search->execute('a carted mart lot one blue ted');

        $this->assertSame($expectedResult, $found);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Must call finalize() before search.
     */
    public function testCannotSearchUnlessFinalized()
    {
        $this->search->addNeedle('art');
        $this->search->addNeedle('cart');
        $this->search->addNeedle('ted');

        $this->search->execute('a carted mart lot one blue ted');
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Cannot add word to finalized ahocorasick.
     */
    public function testCannotAddNeedleAfterFinalize()
    {
        $this->search->addNeedle('art');
        $this->search->addNeedle('cart');
        $this->search->addNeedle('ted');

        $this->search->finalize();

        $this->search->addNeedle('mart');
    }
}
