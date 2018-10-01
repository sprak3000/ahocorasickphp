<?php
use codeplea\AhoCorasick\Search;

/* This program will benchmark searching for 1,000 keywords in a 5,000 word text all at once. */
/* It compares our ahocorasick method with regex and strpos. */


require 'vendor/autoload.php';
require 'benchmark_setup.php'; /* keywords and text */

$loops = 10;

print('Loaded ' . count($needles) . ' keywords to search on a text of ' .
    strlen($haystack) . " characters.\n");

print("\nSearching with strpos...\n");

$st = microtime(1);
for ($loop = 0; $loop < $loops; ++$loop) {
    $found = array();
    foreach ($needles as $n) {
        $k = 0;
        while (($k = strpos($haystack, $n, $k)) !== false) {
            $found[] = array($n, $k);
            ++$k;
        }
    }
}
$et = microtime(1);
print('time: ' . ($et - $st) . "\n");
$found_strpos = $found;


print("\nSearching with preg_match...\n");
//Note, this actually sucks and misses cases where one needle is a prefix or
//suffix of another.
$regex = '/' . implode('|', $needles) . '/';

$st = microtime(1);
for ($loop = 0; $loop < $loops; ++$loop) {
    $found = array();
    $k = 0;
    while (preg_match($regex, $haystack, $m, PREG_OFFSET_CAPTURE, $k)) {
        $found[] = $m[0];
        $k = $m[0][1] + 1;
    }
}
$et = microtime(1);
print('time: ' . ($et - $st) . "\n");


print("\nSearching with preg_match_all...\n");
//Note, this actually sucks and misses cases where one needle is a prefix or
//suffix of another.
$regex = '/' . implode('|', $needles) . '/';

$st = microtime(1);
for ($loop = 0; $loop < $loops; ++$loop) {
    $found = array();
    $k = 0;
    preg_match_all($regex, $haystack, $found, PREG_OFFSET_CAPTURE);
    $found = $found[0];
}
$et = microtime(1);
print('time: ' . ($et - $st) . "\n");


print("\nSearching with aho corasick...\n");
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
print('time: ' . ($et - $st) . "\n");


//Check that the answers match.
//First sort the arrays.
$comp = function ($a, $b) {
    return ($a[1] === $b[1]) ? ($a[0] > $b[0]) : ($a[1] > $b[1]);
};
usort($found, $comp);
usort($found_strpos, $comp);

if ($found_strpos !== $found) {
    print("ERROR - Aho Corasick got the wrong result.\n");

    print('strpos size: ' . count($found_strpos) . "\n");
    print('aho corasick size: ' . count($found) . "\n");

    $numberFound = count($found);

    for ($i = 0; $i < $numberFound; ++$i) {
        if ($found_strpos[$i] !== $found[$i]) {
            print("Mismatch $i\n");
            print_r($found_strpos[$i]);
            print_r($found[$i]);
        }
    }
}
