<?php

/**
 * Dutch Stemmer
 *
 * based on the stemming algorithm (http://snowball.tartarus.org/algorithms/dutch/stemmer.html )
 *
 * Copyright (c) 2010 Glenn De Backer < glenn at simplicity dot be >
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * If you have any questions or comments, please email:
 * Glenn at simplicity dot be
 *
 * @author Glenn De Backer (glenn at  simplicity  dot be)
 *
 * @version 2.0
 */

// namespace Simplicitylab\Stemmer;

class DutchStemmer
{

    private $R1;
    private $R2;
    private $removedESuffix;

    public function stemWord($term)
    {
        // remove whitespace
        $term = rtrim($term);

        // convert to lowercase
        $term = strtolower($term);

        // be sure that word is stemmable
        if ($this->isStemmable($term)) {
            // replace special characters
            $term = $this->replaceSpecialCharacters($term);

            // Substitute i and y
            $term = $this->substituteIAndY($term);

            // Get R1 & R2 region
            $this->R1 = $this->getRIndex($term, 0);
            $this->R2 = $this->getRIndex($term, $this->R1);

            // do step 1
            $term = $this->step1($term);

            // do step 2
            $term = $this->step2($term);

            // do step 3a
            $term = $this->step3a($term);

            // do step 3b
            $term = $this->step3b($term);

            // do step 4
            $term = $this->step4($term);

            // lower cases to restory I and Y
            $term = strtolower($term);
        }

        return $term;
    }


    private function step1($term)
    {

        /**
         * Define a valid s-ending as a non-vowel other than j.
         * Define a valid en-ending as a non-vowel, and not gem.
         * Define undoubling the ending as removing the last letter if the word ends kk, dd or tt.
         **/

        if ($this->R1 != -1) {
            $numBerOfletters = strlen($term);

            if ($numBerOfletters > 2) {

                if ($this->R1 >= $numBerOfletters) {
                    return $term;
                }

                /**
                 * (a) heden
                 * replace with heid if in R1
                 **/
                if ($this->endsWith($term, "heden")) {
                    $term = $this->replace($term, '/heden$/', 'heid', $this->R1);
                    return $term;
                }

                /**
                 * (b) en   ene
                 * delete if in R1 and preceded by a valid en-ending, and then undouble the ending
                 **/
                if (preg_match("/(?<![aeiouyè]|gem)(ene?)$/", $term, $matches, 0, $this->R1)) {
                    $term = $this->undouble($this->replace($term, '/(?<![aeiouyè]|gem)(ene?)$/', '', $this->R1));
                    //$term = $this->undouble(preg_replace('/ene?/', '', $term, -1));
                    return $term;
                }

                /**
                 * (c) s   se
                 * delete if in R1 and preceded by a valid s-ending
                 **/
                if (preg_match("/(?<![aeiouyèj])(se?)$/", $term, $matches, 0, $this->R1)) {
                    $term = $this->replace($term, '/(?<![aeiouyèj])(se?)$/', '', $this->R1);
                    return $term;
                }
            }
        }

        return $term;
    }

    /**
     * Step 2 : Delete suffix e if in R1 and preceded by a non-vowel, and then undouble the ending
     * @param string $term
     * @return string
     */
    private function step2($term)
    {

        // is suffix the letter e?
        if ($this->endsWith($term, "e")) {
            $letters = str_split($term);
            $numBerOfLetters = count($letters);

            if ($numBerOfLetters > 2) {
                // if preceding letter isn't a vowel
                if (!$this->isVowel($letters[$numBerOfLetters - 2])) {
                    // remove last letter
                    $letters = array_slice($letters, 0, ($numBerOfLetters - 1));

                    // implode array
                    $term = implode('', $letters);

                    // undouble ending
                    $term = $this->undouble($term);

                    // set removed e flag to true
                    $this->removedESuffix = true;
                }
            }
        }

        return $term;
    }

    /**
     * Step 3a: delete heid if in R2 and not preceded by c, and treat a preceding en as in step 1(b)
     * @param string $term
     * @return string
     */
    private function step3a($term)
    {
        if ($this->R2 != -1) {

            // if you found heid not preceded by c
            if (preg_match("/(?<![c])(heid)/", $term, $matches, 0, $this->R2)) {

                // delete if not preceded by c
                $term = $this->replace($term, '/(?<![c])(heid)/', '', $this->R2);

                // do like step 1b
                if (preg_match("/(?<![aeiouyè]|gem)(ene?)/", $term, $matches, 0, $this->R2)) {
                    $term = $this->undouble($this->replace($term, '/(?<![aeiouyè]|gem)(ene?)/', '', $this->R2));
                }
            }
        }

        return $term;
    }


    /**
     * Step 3b: d-suffixes   Search for the longest among the following suffixes, and perform the action indicated.
     * @param string $term
     * @return string
     */
    public function step3b($term)
    {
        $letters = str_split($term);
        $numBerOfLetters = count($letters);

        if ($numBerOfLetters > 2) {
            if ($this->R2 != -1) {

                /**
                 * end   ing
                 * delete if in R2
                 * if preceded by ig, delete if in R2 and not preceded by e, otherwise undouble the ending
                 **/

                // check if you find end or ing preceded by eig
                if (preg_match('/eig(end|ing)$/', $term, $matches, 0, $this->R2)) {
                    $term = $this->replace($term, '/(eig)end|ing$/', '', $this->R2);
                    $term = $this->undouble($term);
                    return $term;

                    // check if you find end or ing preceded by ig then delete it
                } elseif (preg_match('/ig(end|ing)$/', $term, $matches, 0, $this->R2)) {
                    $term = $this->replace($term, '/(igend|iging)$/', '', $this->R2);
                    return $term;

                    // check if you find end or ing within R2 then delete it
                } elseif (preg_match('/end|ing/', $term, $matches, 0, $this->R2)) {
                    $term = $this->replace($term, '/(end|ing)$/', '', $this->R2);
                    return $term;
                }

                /*
                 * ig
                * delete if in R2 and not preceded by e
                */
                if (preg_match("/(?<![e])ig$/", $term, $matches, 0, $this->R2)) {
                    $term = $this->replace($term, '/(?<![e])ig$/', '', $this->R2);

                    return $term;
                }

                /*
                 * lijk
                * delete if in R2, and then repeat step 2
                */
                if (preg_match("/lijk$/", $term, $matches, 0, $this->R2)) {
                    $term = $this->replace($term, '/lijk$/', '', $this->R2);
                    $term = $this->step2($term);
                    return $term;
                }


                /*
                 * baar
                * delete if in R2
                */
                if (preg_match("/baar$/", $term, $matches, 0, $this->R2)) {
                    $term = $this->replace($term, '/baar$/', '', $this->R2);
                    return $term;
                }


               /*
                * bar
                * delete if in R2 and if step 2 removed an e
                */
                if (preg_match("/bar$/", $term, $matches, 0, $this->R2)) {

                    // if step 2 removed E
                    if ($this->removedESuffix) {
                        $term = $this->replace($term, '/bar$/', '', $this->R2);
                    }

                    return $term;
                }
            }
        }

        return $term;
    }


    /**
     * If the words ends CVD, where C is a non-vowel, D is a non-vowel other than I,
     * and V is double a, e, o or u, remove one of the vowels from V (for example, maan -> man, brood -> brod).
     * @param string $term
     * @return string
     */
    private function step4($term)
    {
        $letters = str_split($term);
        $numberOfLetters = count($letters);

        if ($numberOfLetters > 4) {
            $c = $letters[$numberOfLetters - 4];
            $v1 = $letters[$numberOfLetters - 3];
            $v2 = $letters[$numberOfLetters - 2];
            $d = $letters[$numberOfLetters - 1];

            if (!$this->isVowel($c) &&
                $this->isVowel($v1) &&
                $this->isVowel($v2) &&
                !$this->isVowel($d) &&
                $v1 == $v2 &&
                $d != 'I' &&
                $v1 != 'i') {
                unset($letters[$numberOfLetters - 2]);

                $term = implode('', $letters);
            }
        }

        return $term;
    }


    /**
     * Substitute I and Y ( Put initial y, y after a vowel, and i between vowels into upper case. )
     * @param string $term
     * @return string
     */
    private function substituteIAndY($term)
    {

        /* Put initial y, y after a vowel, and i between vowels into upper case */
        $letters = str_split($term);
        $num_letters = count($letters);

        // check initial y
        if ($letters[0] == 'y') {
            $letters[0] = 'Y';
        }

        // loop through letters
        for ($i = 1; $i < $num_letters; $i++) {
            if ($letters[$i] == 'i') {
                // check if i is between two vowels
                if (isset($letters[$i - 1]) && isset($letters[$i + 1]) && $this->isVowel($letters[$i - 1]) && $this->isVowel($letters[$i + 1])) {
                    $letters[$i] = 'I';
                }
            } elseif ($letters[$i] == 'y') {
                if ($this->isVowel($letters[$i - 1])) {
                    $letters[$i] = 'Y';
                }
            }
        }

        if ($num_letters > 1) {
            $num_letters--;
            if ($letters[$num_letters] == 'y' && $this->isVowel($letters[$num_letters - 1])) {
                $letters[$num_letters] = 'Y';
            }
        }

        // implode array
        $term = implode('', $letters);

        return $term;
    }



    /**
     * Checks if a strings ends with string
     * source http://snipplr.com/view/13213/check-if-a-string-ends-with-another-string/
     * @param string $haystack
     * @param string $needle
     * @param bool $case
     * @return bool
     */
    private function endsWith($haystack, $needle, $case = true)
    {
        if ($case) {
            return (strcmp(substr($haystack, strlen($haystack) - strlen($needle)), $needle) === 0);
        }

        return (strcasecmp(substr($haystack, strlen($haystack) - strlen($needle)), $needle) === 0);
    }


    /**
     * Undoubles a word (Define undoubling the ending as removing the last letter if the word ends kk, dd or tt.)
     * @param string $term
     * @return string
     */
    private function undouble($term)
    {
        if ($this->endsWith($term, "kk") ||
            $this->endsWith($term, "tt") ||
            $this->endsWith($term, "dd")
        ) {
            $term = substr($term, 0, strlen($term) - 1);
        }

        return $term;
    }


    /**
     * Returns if letter is a vowel
     * @param string $letter
     * @return bool
     */
    private function isVowel($letter)
    {
        switch ($letter) {
            case 'e':
            case 'a':
            case 'o':
            case 'i':
            case 'u':
            case 'y':
            case 'è':
                return true;
                break;
        }

        return false;
    }

    /**
     * Returns R index
     * @param string $term
     * @param integer $start
     * @return integer index
     */
    private function getRIndex($term, $start)
    {
        if ($start == 0) {
            $start = 1;
        }

        $letters = str_split($term);

        $numBerOfletters = count($letters);

        for ($i = $start; $i < $numBerOfletters; $i++) {

            if ( empty( $letters[ $i ] ) || empty( $letters[ $i - 1 ] ) ) {
                continue;
            }

            //first non-vowel preceded by a vowel
            if (!$this->isVowel($letters[$i]) && $this->isVowel($letters[$i - 1])) {
                return $i + 1;
            }
        }

        return -1;
    }


    /**
     * Replaces with a certain part of a string
     * @param string $word
     * @param string $regex
     * @param string $replace
     * @param string $offset
     * @return string
     */
    private function replace($word, $regex, $replace, $offset)
    {
        if ($offset > 0) {

            // split up string in 2 parts
            $part1 = substr($word, 0, $offset);
            $part2 = substr($word, $offset, strlen($word));

            // do replace in part2 of the word
            $part2 = preg_replace($regex, $replace, $part2);

            // concat parts
            return $part1 . "" . $part2;
        } else {
            return preg_replace($regex, $replace, $word);
        }
    }


    /**
     * Returns if a term is stemmable
     * @param string $term
     * @return bool
     */
    private function isStemmable($term)
    {
        /* Checks if all of the characters in the provided string, text, are alphabetic. */
        return ctype_alpha($term);
    }

    /**
     * Replaces special characters
     * @param string $term
     * @return string
     */
    private function replaceSpecialCharacters($term)
    {
        $term = preg_replace("/\é|\ë|\ê/", "e", $term);
        $term = preg_replace("/\á|\à|ä/", "a", $term);
        $term = preg_replace("/\ó|\ò|ö/", "o", $term);
        $term = preg_replace("/\ç/", "c", $term);
        $term = preg_replace("/\ï/", "i", $term);
        $term = preg_replace("/\ü/", "u", $term);
        $term = preg_replace("/\û/", "u", $term);
        $term = preg_replace("/\î/", "i", $term);

        return $term;
    }
}
