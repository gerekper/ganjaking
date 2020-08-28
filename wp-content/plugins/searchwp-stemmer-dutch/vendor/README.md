# PHP Dutch Stemmer

[![Build Status](https://travis-ci.org/simplicitylab/php-dutch-stemmer.svg?branch=master)](https://travis-ci.org/simplicitylab/php-dutch-stemmer)

## About

PHP Dutch Stemmer is a PHP class that stems Dutch words. It based on the algorithm that is described on the page http://snowball.tartarus.org/algorithms/dutch/stemmer.html . It is being distributed under a LGPL license so that it can be used in a commercial setting. 

In linguistic morphology, stemming is the process for reducing inflected (or sometimes derived) words to their stem, base or root form generally a written word form. http://en.wikipedia.org/wiki/Stemming

It can be used in search engines, text mining , classifiers, etc. For example you can search for 'fish' and with stemming also return words like fishing or fishes.

## How to use the stemmer
    
    use Simplicitylab\Stemmer\DutchStemmer;
    
    $stemmer = new Stemmer();
    
    echo $stemmer->stemWord("lichamelijk") ."\n";       // licham
    echo $stemmer->stemWord("lichamelijke" )."\n";      // licham
    echo $stemmer->stemWord("lichamelijkheden") ."\n";  // licham
    echo $stemmer->stemWord("lichamen") ."\n";          // licham

  
## Some sample output

lichamelijk -> licham

lichamelijke -> licham

lichamelijkheden -> licham

lichamen -> licham

opheffen -> opheff

opheffende -> opheff

opheffing -> opheff

opglimpen -> opglimp

opglimpende -> opglimp

opglimping -> opglimp

opglimpingen -> opglimp 


## Copyright
Copyright (c) 2011 Glenn De Backer. See License for details.