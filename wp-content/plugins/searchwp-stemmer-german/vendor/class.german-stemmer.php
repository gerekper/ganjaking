<?php
/**

 * Copyright (c) 2013 Aris Buzachis (buzachis.aris@gmail.com)
 *
 * All rights reserved.
 *
 * This script is free software.
 *
 * DISCLAIMER:
 *
 * IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

/**
 * Takes a word and reduces it to its German stem using the Porter stemmer algorithm.
 *
 * References:
 *  - http://snowball.tartarus.org/algorithms/porter/stemmer.html
 *  - http://snowball.tartarus.org/algorithms/german/stemmer.html
 *
 * Usage:
 *  $stem = SearchWP_GermanStemmer::stem($word);
 *
 * NOTE: You must open this document as a UTF-8 file, or you'll override the
 * accented forms.
 *
 *  @author Aris Buzachis <buzachis.aris@gmail.com>
 *  @version 1.1
 */

// namespaced to SearchWP_GermanStemmer from GermanStemmer
class SearchWP_GermanStemmer
{
	/**
	 *  R1 and R2 regions (see the Porter algorithm)
	 */
	private static $R1;
	private static $R1Pos;
	private static $R2;
	private static $R2Pos;

	private static $cache = array();

	private static $vowels = array('a','e','i','o','u','y','A','O','U');
	private static $s_ending = array('b','d','f','g','h','k','l','m','n','r','t');
	private static $st_ending = array('b','d','f','g','h','k','l','m','n','t');


	public static function stem($word) {

		$word = function_exists( 'mb_strtolower' ) ? mb_strtolower( $word ) : strtolower( $word );

		if (!isset(self::$cache[$word])) {
			$result = self::getStem($word);
			self::$cache[$word] = $result;
		}

		return self::$cache[$word];
	}

	private static function getStem($word) {
		$word = self::step0a($word);
		$word = self::step1($word);
		$word = self::step2($word);
		$word = self::step3($word);
		$word = self::step0b($word);

		return $word;
	}

	/**
	 *  replaces to protect some characters
	 */
	private static function step0a($word) {
		$word = str_replace(array('ä','ö','ü'),array('A','O','U'),$word);
		$vstr = implode('',self::$vowels);
		$word = preg_replace('#(['.$vstr.'])u(['.$vstr.'])#', '$1Z$2',$word);
		$word = preg_replace('#(['.$vstr.'])y(['.$vstr.'])#', '$1Y$2',$word);

		return $word;
	}

	/**
	 *   Undo the initial replaces
	 */
	private static function step0b($word) {
		$word = str_replace(array('A','O','U','Y','Z'),array('ä','ö','ü','y','u'),$word);

		return $word;
	}

	private static function step1($word) {
		$word = str_replace('ß','ss',$word);

		self::getR($word);

		$replaceCount = 0;

		$arr = array('em','ern','er');
		foreach ($arr as $s) {
			self::$R1 = preg_replace('#'.$s.'$#','',self::$R1,-1,$replaceCount);
			if ($replaceCount > 0) {
				$word = preg_replace('#'.$s.'$#','',$word);
			}
		}

		$arr = array('en','es','e');
		foreach ($arr as $s) {
			self::$R1 = preg_replace('#'.$s.'$#','',self::$R1,-1,$replaceCount);
			if ($replaceCount > 0) {
				$word = preg_replace('#'.$s.'$#','',$word);
				$word = preg_replace('#niss$#', 'nis', $word);
			}
		}

		$word = preg_replace('/(['.implode('',self::$s_ending).'])s$/','$1',$word);

		return $word;
	}

	private static function step2($word) {
		self::getR($word);

		$replaceCount = 0;

		$arr = array('est','er','en');
		foreach ($arr as $s) {
			self::$R1 = preg_replace('#'.$s.'$#','',self::$R1,-1,$replaceCount);
			if ($replaceCount > 0) {
				$word = preg_replace('#'.$s.'$#','',$word);
			}
		}

		if (strpos(self::$R1,'st') !== FALSE) {
			self::$R1 = preg_replace('#st$#','',self::$R1);
			$word = preg_replace('#(...['.implode('',self::$st_ending).'])st$#','$1',$word);
		}

		return $word;
	}

	private static function step3($word) {
		self::getR($word);

		$replaceCount = 0;

		$arr = array('end', 'ung');
		foreach ($arr as $s) {
			if (preg_match('#'.$s.'$#',self::$R2)) {
				$word = preg_replace('#([^e])'.$s.'$#','$1',$word, -1, $replaceCount);
				if ($replaceCount > 0) {
					self::$R2 = preg_replace('#'.$s.'$#','',self::$R2,-1,$replaceCount);
				}
			}
		}

		$arr = array('isch', 'ik', 'ig');
		foreach ($arr as $s) {
			if (preg_match('#'.$s.'$#',self::$R2)) {
				$word = preg_replace('#([^e])'.$s.'$#','$1',$word, -1, $replaceCount);
				if ($replaceCount > 0) {
					self::$R2 = preg_replace('#'.$s.'$#','',self::$R2);
				}
			}
		}

		$arr = array('lich', 'heit');
		foreach ($arr as $s) {
			self::$R2 = preg_replace('#'.$s.'$#','',self::$R2,-1,$replaceCount);
			if ($replaceCount > 0) {
				$word = preg_replace('#'.$s.'$#','',$word);
			} else {
				if (preg_match('#'.$s.'$#',self::$R1)) {
					$word = preg_replace('#(er|en)'.$s.'$#','$1',$word, -1, $replaceCount);
					if ($replaceCount > 0) {
						self::$R1 = preg_replace('#'.$s.'$#','',self::$R1);
					}
				}
			}
		}

		$arr = array('keit');
		foreach ($arr as $s) {
			self::$R2 = preg_replace('#'.$s.'$#','',self::$R2,-1,$replaceCount);
			if ($replaceCount > 0) {
				$word = preg_replace('#'.$s.'$#','',$word);
			}
		}

		return $word;
	}

	/**
	 * Find R1 and R2
	 */
	private static function getR($word) {
		$string = str_split($word);
		$arrV = array_intersect($string, self::$vowels);

		self::$R1Pos = NULL;
		self::$R2Pos = NULL;

		// find R1/R2 positions
		for ($i=0; $i<count($string)-1; $i++) {
			if(isset($arrV[$i]) && !isset($arrV[$i+1]) && self::$R1Pos === NULL) {
				self::$R1Pos = $i+2;
			} elseif (isset($arrV[$i]) && !isset($arrV[$i+1])  && self::$R1Pos) {
				self::$R2Pos = $i+2;
				break;
			}
		}

		if(self::$R1Pos!=NULL) {
			self::$R1 = substr($word, self::$R1Pos);
		}
		if(self::$R2Pos!=NULL) {
			self::$R2 = substr($word, self::$R2Pos);
		}
	}
}
