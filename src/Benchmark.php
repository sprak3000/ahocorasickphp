<?php
namespace codeplea\AhoCorasick;

use codeplea\AhoCorasick\Search;

class Benchmark
{
    protected $foundStrpos = [];

    /**
     * @param array $needles
     * @param string $haystack
     * @param int $loops
     */
    protected function benchmarkStrpos(array $needles, string $haystack, int $loops)
    {
        print "\nSearching with strpos...\n";

        $st = microtime(1);
        for ($loop = 0; $loop < $loops; ++$loop) {
            $found = [];
            foreach ($needles as $n) {
                $k = 0;
                while (($k = strpos($haystack, $n, $k)) !== false) {
                    $found[] = [$n, $k];
                    ++$k;
                }
            }
        }

        $et = microtime(1);
        print 'time: ' . ($et - $st) . "\n";
        $this->foundStrpos = $found;
    }

    /**
     * @param array $needles
     * @param string $haystack
     * @param int $loops
     */
    protected function benchmarkPregMatch(array $needles, string $haystack, int $loops)
    {
        print "\nSearching with preg_match...\n";

        // Note, this actually sucks and misses cases where one needle is a prefix or
        // suffix of another.
        $regex = '/' . implode('|', $needles) . '/';

        $st = microtime(1);
        for ($loop = 0; $loop < $loops; ++$loop) {
            $k = 0;
            while (preg_match($regex, $haystack, $m, PREG_OFFSET_CAPTURE, $k)) {
                $k = $m[0][1] + 1;
            }
        }
        $et = microtime(1);
        print 'time: ' . ($et - $st) . "\n";
    }

    /**
     * @param array $needles
     * @param string $haystack
     * @param int $loops
     */
    protected function benchmarkPregMatchAll(array $needles, string $haystack, int $loops)
    {
        print "\nSearching with preg_match_all...\n";

        // Note, this actually sucks and misses cases where one needle is a prefix or
        // suffix of another.
        $regex = '/' . implode('|', $needles) . '/';

        $st = microtime(1);
        for ($loop = 0; $loop < $loops; ++$loop) {
            preg_match_all($regex, $haystack, $found, PREG_OFFSET_CAPTURE);
        }
        $et = microtime(1);
        print 'time: ' . ($et - $st) . "\n";
    }

    /**
     * @param array $needles
     * @param string $haystack
     * @param int $loops
     * @throws \Exception
     */
    protected function benchmarkAhoCorasick(array $needles, string $haystack, int $loops)
    {
        print "\nSearching with aho corasick...\n";

        $ac = new Search();
        foreach ($needles as $n) {
            $ac->addNeedle($n);
        }
        $ac->finalize();

        $st = microtime(1);
        for ($loop = 0; $loop < $loops; ++$loop) {
            $found = $ac->execute($haystack);
        }
        $et = microtime(1);
        print 'time: ' . ($et - $st) . "\n";

        // Check that the answers match.
        // First sort the arrays.
        $comp = function ($a, $b) {
            return ($a[1] === $b[1]) ? ($a[0] > $b[0]) : ($a[1] > $b[1]);
        };
        usort($found, $comp);
        usort($this->foundStrpos, $comp);

        if ($this->foundStrpos !== $found) {
            print "ERROR - Aho Corasick got the wrong result.\n";

            print 'strpos size: ' . count($this->foundStrpos) . "\n";
            print 'aho corasick size: ' . count($found) . "\n";

            $numberFound = count($found);

            for ($i = 0; $i < $numberFound; ++$i) {
                if ($this->foundStrpos[$i] !== $found[$i]) {
                    print "Mismatch $i\n";
                    print_r($this->foundStrpos[$i]);
                    print_r($found[$i]);
                }
            }
        }
    }

    /**
     * Compares the performance of Aho Corasick against strpos, preg_match, and preg_match_all
     *
     * @param array $needles
     * @param string $haystack
     * @param int $loops
     * @throws \Exception
     */
    public function run(array $needles, string $haystack, int $loops = 10)
    {
        print 'Loaded ' . count($needles) . ' keywords to search on a text of ' . strlen($haystack) . " characters.\n";

        $this->benchmarkStrpos($needles, $haystack, $loops);
        $this->benchmarkPregMatch($needles, $haystack, $loops);
        $this->benchmarkPregMatchAll($needles, $haystack, $loops);
        $this->benchmarkAhoCorasick($needles, $haystack, $loops);
    }
}
