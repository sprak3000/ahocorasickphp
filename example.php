<?php

/* Just an example of how to use the library. */

require 'AhoCorasick.php';



/* Say we have the following text:
 * "a carted mart lot one blue ted"
 * and we want to find each occurrence in
 * it of the following list of keywords:
 * 'art', 'cart', 'ted'
 */


/* Create the search engine. */
$ac = new AhoCorasick();


/* Add each keyword we'll search for. */
$ac->addNeedle('art');
$ac->addNeedle('cart');
$ac->addNeedle('ted');


/* Now call finalize. This lets the engine
 * build the search tree. It might take a while
 * if you have a lot of keywords.
 */
$ac->finalize();


/* And finally do the search. It should be fast. */

/*                    012345678901234567890123456789 */
$found = $ac->search('a carted mart lot one blue ted');


/* It'll return an array with each keyword found and its
 * position in the search text. */
print_r($found);
