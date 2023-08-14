<?php

namespace SearchWP\Dependencies\Wamania\Snowball;

use SearchWP\Dependencies\voku\helper\UTF8;
use SearchWP\Dependencies\Wamania\Snowball\Stemmer\Catalan;
use SearchWP\Dependencies\Wamania\Snowball\Stemmer\Danish;
use SearchWP\Dependencies\Wamania\Snowball\Stemmer\Dutch;
use SearchWP\Dependencies\Wamania\Snowball\Stemmer\English;
use SearchWP\Dependencies\Wamania\Snowball\Stemmer\Finnish;
use SearchWP\Dependencies\Wamania\Snowball\Stemmer\French;
use SearchWP\Dependencies\Wamania\Snowball\Stemmer\German;
use SearchWP\Dependencies\Wamania\Snowball\Stemmer\Italian;
use SearchWP\Dependencies\Wamania\Snowball\Stemmer\Norwegian;
use SearchWP\Dependencies\Wamania\Snowball\Stemmer\Portuguese;
use SearchWP\Dependencies\Wamania\Snowball\Stemmer\Romanian;
use SearchWP\Dependencies\Wamania\Snowball\Stemmer\Russian;
use SearchWP\Dependencies\Wamania\Snowball\Stemmer\Spanish;
use SearchWP\Dependencies\Wamania\Snowball\Stemmer\Stemmer;
use SearchWP\Dependencies\Wamania\Snowball\Stemmer\Swedish;
class StemmerFactory
{
    const LANGS = [Catalan::class => ['ca', 'cat', 'catalan'], Danish::class => ['da', 'dan', 'danish'], Dutch::class => ['nl', 'dut', 'nld', 'dutch'], English::class => ['en', 'eng', 'english'], Finnish::class => ['fi', 'fin', 'finnish'], French::class => ['fr', 'fre', 'fra', 'french'], German::class => ['de', 'deu', 'ger', 'german'], Italian::class => ['it', 'ita', 'italian'], Norwegian::class => ['no', 'nor', 'norwegian'], Portuguese::class => ['pt', 'por', 'portuguese'], Romanian::class => ['ro', 'rum', 'ron', 'romanian'], Russian::class => ['ru', 'rus', 'russian'], Spanish::class => ['es', 'spa', 'spanish'], Swedish::class => ['sv', 'swe', 'swedish']];
    /**
     * @throws NotFoundException
     */
    public static function create(string $code) : Stemmer
    {
        $code = UTF8::strtolower($code);
        foreach (self::LANGS as $classname => $isoCodes) {
            if (\in_array($code, $isoCodes)) {
                return new $classname();
            }
        }
        throw new NotFoundException(\sprintf('Stemmer not found for %s', $code));
    }
}
