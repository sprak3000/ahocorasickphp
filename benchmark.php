<?php
use codeplea\AhoCorasick\Benchmark;

require 'vendor/autoload.php';

/* keywords and text */
require 'benchmark_setup.php';

// Benchmark searching for 1,000 keywords in a 5,000 word text all at once.
$benchmark = new Benchmark();
$benchmark->run($needles, $haystack);
