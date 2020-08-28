<?php

namespace SearchWP\Dependencies\Wamania\Snowball;

/**
 * UTF8 helper functions
 *
 * @license    LGPL (http://www.gnu.org/copyleft/lesser.html)
 * @author     Andreas Gohr <andi@splitbrain.org>
 * @package Stato
 * @subpackage view
 */
class Utf8
{
    /**
     * UTF-8 lookup table for lower case accented letters
     *
     * This lookuptable defines replacements for accented characters from the ASCII-7
     * range. This are lower case letters only.
     *
     * @author Andreas Gohr <andi@splitbrain.org>
     * @see    utf8_deaccent()
     */
    private static $utf8_lower_accents = array('à' => 'a', 'ô' => 'o', 'd' => 'd', '?' => 'f', 'ë' => 'e', 'š' => 's', 'o' => 'o', 'ß' => 'ss', 'a' => 'a', 'r' => 'r', '?' => 't', 'n' => 'n', 'a' => 'a', 'k' => 'k', 's' => 's', '?' => 'y', 'n' => 'n', 'l' => 'l', 'h' => 'h', '?' => 'p', 'ó' => 'o', 'ú' => 'u', 'e' => 'e', 'é' => 'e', 'ç' => 'c', '?' => 'w', 'c' => 'c', 'õ' => 'o', '?' => 's', 'ø' => 'o', 'g' => 'g', 't' => 't', '?' => 's', 'e' => 'e', 'c' => 'c', 's' => 's', 'î' => 'i', 'u' => 'u', 'c' => 'c', 'e' => 'e', 'w' => 'w', '?' => 't', 'u' => 'u', 'c' => 'c', 'ö' => 'oe', 'è' => 'e', 'y' => 'y', 'a' => 'a', 'l' => 'l', 'u' => 'u', 'u' => 'u', 's' => 's', 'g' => 'g', 'l' => 'l', 'ƒ' => 'f', 'ž' => 'z', '?' => 'w', '?' => 'b', 'å' => 'a', 'ì' => 'i', 'ï' => 'i', '?' => 'd', 't' => 't', 'r' => 'r', 'ä' => 'ae', 'í' => 'i', 'r' => 'r', 'ê' => 'e', 'ü' => 'ue', 'ò' => 'o', 'e' => 'e', 'ñ' => 'n', 'n' => 'n', 'h' => 'h', 'g' => 'g', 'd' => 'd', 'j' => 'j', 'ÿ' => 'y', 'u' => 'u', 'u' => 'u', 'u' => 'u', 't' => 't', 'ý' => 'y', 'o' => 'o', 'â' => 'a', 'l' => 'l', '?' => 'w', 'z' => 'z', 'i' => 'i', 'ã' => 'a', 'g' => 'g', '?' => 'm', 'o' => 'o', 'i' => 'i', 'ù' => 'u', 'i' => 'i', 'z' => 'z', 'á' => 'a', 'û' => 'u', 'þ' => 'th', 'ð' => 'dh', 'æ' => 'ae', 'µ' => 'u');
    /**
     * UTF-8 Case lookup table
     *
     * This lookuptable defines the upper case letters to their correspponding
     * lower case letter in UTF-8
     *
     * @author Andreas Gohr <andi@splitbrain.org>
     */
    private static $utf8_lower_to_upper = array(0x61 => 0x41, 0x3c6 => 0x3a6, 0x163 => 0x162, 0xe5 => 0xc5, 0x62 => 0x42, 0x13a => 0x139, 0xe1 => 0xc1, 0x142 => 0x141, 0x3cd => 0x38e, 0x101 => 0x100, 0x491 => 0x490, 0x3b4 => 0x394, 0x15b => 0x15a, 0x64 => 0x44, 0x3b3 => 0x393, 0xf4 => 0xd4, 0x44a => 0x42a, 0x439 => 0x419, 0x113 => 0x112, 0x43c => 0x41c, 0x15f => 0x15e, 0x144 => 0x143, 0xee => 0xce, 0x45e => 0x40e, 0x44f => 0x42f, 0x3ba => 0x39a, 0x155 => 0x154, 0x69 => 0x49, 0x73 => 0x53, 0x1e1f => 0x1e1e, 0x135 => 0x134, 0x447 => 0x427, 0x3c0 => 0x3a0, 0x438 => 0x418, 0xf3 => 0xd3, 0x440 => 0x420, 0x454 => 0x404, 0x435 => 0x415, 0x449 => 0x429, 0x14b => 0x14a, 0x431 => 0x411, 0x459 => 0x409, 0x1e03 => 0x1e02, 0xf6 => 0xd6, 0xf9 => 0xd9, 0x6e => 0x4e, 0x451 => 0x401, 0x3c4 => 0x3a4, 0x443 => 0x423, 0x15d => 0x15c, 0x453 => 0x403, 0x3c8 => 0x3a8, 0x159 => 0x158, 0x67 => 0x47, 0xe4 => 0xc4, 0x3ac => 0x386, 0x3ae => 0x389, 0x167 => 0x166, 0x3be => 0x39e, 0x165 => 0x164, 0x117 => 0x116, 0x109 => 0x108, 0x76 => 0x56, 0xfe => 0xde, 0x157 => 0x156, 0xfa => 0xda, 0x1e61 => 0x1e60, 0x1e83 => 0x1e82, 0xe2 => 0xc2, 0x119 => 0x118, 0x146 => 0x145, 0x70 => 0x50, 0x151 => 0x150, 0x44e => 0x42e, 0x129 => 0x128, 0x3c7 => 0x3a7, 0x13e => 0x13d, 0x442 => 0x422, 0x7a => 0x5a, 0x448 => 0x428, 0x3c1 => 0x3a1, 0x1e81 => 0x1e80, 0x16d => 0x16c, 0xf5 => 0xd5, 0x75 => 0x55, 0x177 => 0x176, 0xfc => 0xdc, 0x1e57 => 0x1e56, 0x3c3 => 0x3a3, 0x43a => 0x41a, 0x6d => 0x4d, 0x16b => 0x16a, 0x171 => 0x170, 0x444 => 0x424, 0xec => 0xcc, 0x169 => 0x168, 0x3bf => 0x39f, 0x6b => 0x4b, 0xf2 => 0xd2, 0xe0 => 0xc0, 0x434 => 0x414, 0x3c9 => 0x3a9, 0x1e6b => 0x1e6a, 0xe3 => 0xc3, 0x44d => 0x42d, 0x436 => 0x416, 0x1a1 => 0x1a0, 0x10d => 0x10c, 0x11d => 0x11c, 0xf0 => 0xd0, 0x13c => 0x13b, 0x45f => 0x40f, 0x45a => 0x40a, 0xe8 => 0xc8, 0x3c5 => 0x3a5, 0x66 => 0x46, 0xfd => 0xdd, 0x63 => 0x43, 0x21b => 0x21a, 0xea => 0xca, 0x3b9 => 0x399, 0x17a => 0x179, 0xef => 0xcf, 0x1b0 => 0x1af, 0x65 => 0x45, 0x3bb => 0x39b, 0x3b8 => 0x398, 0x3bc => 0x39c, 0x45c => 0x40c, 0x43f => 0x41f, 0x44c => 0x42c, 0xfe => 0xde, 0xf0 => 0xd0, 0x1ef3 => 0x1ef2, 0x68 => 0x48, 0xeb => 0xcb, 0x111 => 0x110, 0x433 => 0x413, 0x12f => 0x12e, 0xe6 => 0xc6, 0x78 => 0x58, 0x161 => 0x160, 0x16f => 0x16e, 0x3b1 => 0x391, 0x457 => 0x407, 0x173 => 0x172, 0xff => 0x178, 0x6f => 0x4f, 0x43b => 0x41b, 0x3b5 => 0x395, 0x445 => 0x425, 0x121 => 0x120, 0x17e => 0x17d, 0x17c => 0x17b, 0x3b6 => 0x396, 0x3b2 => 0x392, 0x3ad => 0x388, 0x1e85 => 0x1e84, 0x175 => 0x174, 0x71 => 0x51, 0x437 => 0x417, 0x1e0b => 0x1e0a, 0x148 => 0x147, 0x105 => 0x104, 0x458 => 0x408, 0x14d => 0x14c, 0xed => 0xcd, 0x79 => 0x59, 0x10b => 0x10a, 0x3ce => 0x38f, 0x72 => 0x52, 0x430 => 0x410, 0x455 => 0x405, 0x452 => 0x402, 0x127 => 0x126, 0x137 => 0x136, 0x12b => 0x12a, 0x3af => 0x38a, 0x44b => 0x42b, 0x6c => 0x4c, 0x3b7 => 0x397, 0x125 => 0x124, 0x219 => 0x218, 0xfb => 0xdb, 0x11f => 0x11e, 0x43e => 0x41e, 0x1e41 => 0x1e40, 0x3bd => 0x39d, 0x107 => 0x106, 0x3cb => 0x3ab, 0x446 => 0x426, 0xfe => 0xde, 0xe7 => 0xc7, 0x3ca => 0x3aa, 0x441 => 0x421, 0x432 => 0x412, 0x10f => 0x10e, 0xf8 => 0xd8, 0x77 => 0x57, 0x11b => 0x11a, 0x74 => 0x54, 0x6a => 0x4a, 0x45b => 0x40b, 0x456 => 0x406, 0x103 => 0x102, 0x3bb => 0x39b, 0xf1 => 0xd1, 0x43d => 0x41d, 0x3cc => 0x38c, 0xe9 => 0xc9, 0xf0 => 0xd0, 0x457 => 0x407, 0x123 => 0x122);
    /**
     * UTF-8 Case lookup table
     *
     * This lookuptable defines the lower case letters to their correspponding
     * upper case letter in UTF-8 (it does so by flipping $utf8_lower_to_upper)
     *
     * @author Andreas Gohr <andi@splitbrain.org>
     */
    //private static $utf8_upper_to_lower = array_flip(self::$utf8_lower_to_upper);
    /**
     * UTF-8 lookup table for upper case accented letters
     *
     * This lookuptable defines replacements for accented characters from the ASCII-7
     * range. This are upper case letters only.
     *
     * @author Andreas Gohr <andi@splitbrain.org>
     * @see    utf8_deaccent()
     */
    private static $utf8_upper_accents = array('À' => 'A', 'Ô' => 'O', 'D' => 'D', '?' => 'F', 'Ë' => 'E', 'Š' => 'S', 'O' => 'O', 'A' => 'A', 'R' => 'R', '?' => 'T', 'N' => 'N', 'A' => 'A', 'K' => 'K', 'S' => 'S', '?' => 'Y', 'N' => 'N', 'L' => 'L', 'H' => 'H', '?' => 'P', 'Ó' => 'O', 'Ú' => 'U', 'E' => 'E', 'É' => 'E', 'Ç' => 'C', '?' => 'W', 'C' => 'C', 'Õ' => 'O', '?' => 'S', 'Ø' => 'O', 'G' => 'G', 'T' => 'T', '?' => 'S', 'E' => 'E', 'C' => 'C', 'S' => 'S', 'Î' => 'I', 'U' => 'U', 'C' => 'C', 'E' => 'E', 'W' => 'W', '?' => 'T', 'U' => 'U', 'C' => 'C', 'Ö' => 'Oe', 'È' => 'E', 'Y' => 'Y', 'A' => 'A', 'L' => 'L', 'U' => 'U', 'U' => 'U', 'S' => 'S', 'G' => 'G', 'L' => 'L', 'ƒ' => 'F', 'Ž' => 'Z', '?' => 'W', '?' => 'B', 'Å' => 'A', 'Ì' => 'I', 'Ï' => 'I', '?' => 'D', 'T' => 'T', 'R' => 'R', 'Ä' => 'Ae', 'Í' => 'I', 'R' => 'R', 'Ê' => 'E', 'Ü' => 'Ue', 'Ò' => 'O', 'E' => 'E', 'Ñ' => 'N', 'N' => 'N', 'H' => 'H', 'G' => 'G', 'Ð' => 'D', 'J' => 'J', 'Ÿ' => 'Y', 'U' => 'U', 'U' => 'U', 'U' => 'U', 'T' => 'T', 'Ý' => 'Y', 'O' => 'O', 'Â' => 'A', 'L' => 'L', '?' => 'W', 'Z' => 'Z', 'I' => 'I', 'Ã' => 'A', 'G' => 'G', '?' => 'M', 'O' => 'O', 'I' => 'I', 'Ù' => 'U', 'I' => 'I', 'Z' => 'Z', 'Á' => 'A', 'Û' => 'U', 'Þ' => 'Th', 'Ð' => 'Dh', 'Æ' => 'Ae');
    /**
     * UTF-8 array of common special characters
     *
     * This array should contain all special characters (not a letter or digit)
     * defined in the various local charsets - it's not a complete list of non-alphanum
     * characters in UTF-8. It's not perfect but should match most cases of special
     * chars.
     *
     * The controlchars 0x00 to 0x19 are _not_ included in this array. The space 0x20 is!
     * These chars are _not_ in the array either:  _ (0x5f), : 0x3a, . 0x2e, - 0x2d, * 0x2a
     *
     * @author Andreas Gohr <andi@splitbrain.org>
     * @see    utf8_stripspecials()
     */
    private static $utf8_special_chars = array(0x1a, 0x1b, 0x1c, 0x1d, 0x1e, 0x1f, 0x20, 0x21, 0x22, 0x23, 0x24, 0x25, 0x26, 0x27, 0x28, 0x29, 0x2b, 0x2c, 0x2f, 0x3b, 0x3c, 0x3d, 0x3e, 0x3f, 0x40, 0x5b, 0x5c, 0x5d, 0x5e, 0x60, 0x7b, 0x7c, 0x7d, 0x7e, 0x7f, 0x80, 0x81, 0x82, 0x83, 0x84, 0x85, 0x86, 0x87, 0x88, 0x89, 0x8a, 0x8b, 0x8c, 0x8d, 0x8e, 0x8f, 0x90, 0x91, 0x92, 0x93, 0x94, 0x95, 0x96, 0x97, 0x98, 0x99, 0x9a, 0x9b, 0x9c, 0x9d, 0x9e, 0x9f, 0xa0, 0xa1, 0xa2, 0xa3, 0xa4, 0xa5, 0xa6, 0xa7, 0xa8, 0xa9, 0xaa, 0xab, 0xac, 0xad, 0xae, 0xaf, 0xb0, 0xb1, 0xb2, 0xb3, 0xb4, 0xb5, 0xb6, 0xb7, 0xb8, 0xb9, 0xba, 0xbb, 0xbc, 0xbd, 0xbe, 0xbf, 0xd7, 0xf7, 0x2c7, 0x2d8, 0x2d9, 0x2da, 0x2db, 0x2dc, 0x2dd, 0x300, 0x301, 0x303, 0x309, 0x323, 0x384, 0x385, 0x387, 0x3b2, 0x3c6, 0x3d1, 0x3d2, 0x3d5, 0x3d6, 0x5b0, 0x5b1, 0x5b2, 0x5b3, 0x5b4, 0x5b5, 0x5b6, 0x5b7, 0x5b8, 0x5b9, 0x5bb, 0x5bc, 0x5bd, 0x5be, 0x5bf, 0x5c0, 0x5c1, 0x5c2, 0x5c3, 0x5f3, 0x5f4, 0x60c, 0x61b, 0x61f, 0x640, 0x64b, 0x64c, 0x64d, 0x64e, 0x64f, 0x650, 0x651, 0x652, 0x66a, 0xe3f, 0x200c, 0x200d, 0x200e, 0x200f, 0x2013, 0x2014, 0x2015, 0x2017, 0x2018, 0x2019, 0x201a, 0x201c, 0x201d, 0x201e, 0x2020, 0x2021, 0x2022, 0x2026, 0x2030, 0x2032, 0x2033, 0x2039, 0x203a, 0x2044, 0x20a7, 0x20aa, 0x20ab, 0x20ac, 0x2116, 0x2118, 0x2122, 0x2126, 0x2135, 0x2190, 0x2191, 0x2192, 0x2193, 0x2194, 0x2195, 0x21b5, 0x21d0, 0x21d1, 0x21d2, 0x21d3, 0x21d4, 0x2200, 0x2202, 0x2203, 0x2205, 0x2206, 0x2207, 0x2208, 0x2209, 0x220b, 0x220f, 0x2211, 0x2212, 0x2215, 0x2217, 0x2219, 0x221a, 0x221d, 0x221e, 0x2220, 0x2227, 0x2228, 0x2229, 0x222a, 0x222b, 0x2234, 0x223c, 0x2245, 0x2248, 0x2260, 0x2261, 0x2264, 0x2265, 0x2282, 0x2283, 0x2284, 0x2286, 0x2287, 0x2295, 0x2297, 0x22a5, 0x22c5, 0x2310, 0x2320, 0x2321, 0x2329, 0x232a, 0x2469, 0x2500, 0x2502, 0x250c, 0x2510, 0x2514, 0x2518, 0x251c, 0x2524, 0x252c, 0x2534, 0x253c, 0x2550, 0x2551, 0x2552, 0x2553, 0x2554, 0x2555, 0x2556, 0x2557, 0x2558, 0x2559, 0x255a, 0x255b, 0x255c, 0x255d, 0x255e, 0x255f, 0x2560, 0x2561, 0x2562, 0x2563, 0x2564, 0x2565, 0x2566, 0x2567, 0x2568, 0x2569, 0x256a, 0x256b, 0x256c, 0x2580, 0x2584, 0x2588, 0x258c, 0x2590, 0x2591, 0x2592, 0x2593, 0x25a0, 0x25b2, 0x25bc, 0x25c6, 0x25ca, 0x25cf, 0x25d7, 0x2605, 0x260e, 0x261b, 0x261e, 0x2660, 0x2663, 0x2665, 0x2666, 0x2701, 0x2702, 0x2703, 0x2704, 0x2706, 0x2707, 0x2708, 0x2709, 0x270c, 0x270d, 0x270e, 0x270f, 0x2710, 0x2711, 0x2712, 0x2713, 0x2714, 0x2715, 0x2716, 0x2717, 0x2718, 0x2719, 0x271a, 0x271b, 0x271c, 0x271d, 0x271e, 0x271f, 0x2720, 0x2721, 0x2722, 0x2723, 0x2724, 0x2725, 0x2726, 0x2727, 0x2729, 0x272a, 0x272b, 0x272c, 0x272d, 0x272e, 0x272f, 0x2730, 0x2731, 0x2732, 0x2733, 0x2734, 0x2735, 0x2736, 0x2737, 0x2738, 0x2739, 0x273a, 0x273b, 0x273c, 0x273d, 0x273e, 0x273f, 0x2740, 0x2741, 0x2742, 0x2743, 0x2744, 0x2745, 0x2746, 0x2747, 0x2748, 0x2749, 0x274a, 0x274b, 0x274d, 0x274f, 0x2750, 0x2751, 0x2752, 0x2756, 0x2758, 0x2759, 0x275a, 0x275b, 0x275c, 0x275d, 0x275e, 0x2761, 0x2762, 0x2763, 0x2764, 0x2765, 0x2766, 0x2767, 0x277f, 0x2789, 0x2793, 0x2794, 0x2798, 0x2799, 0x279a, 0x279b, 0x279c, 0x279d, 0x279e, 0x279f, 0x27a0, 0x27a1, 0x27a2, 0x27a3, 0x27a4, 0x27a5, 0x27a6, 0x27a7, 0x27a8, 0x27a9, 0x27aa, 0x27ab, 0x27ac, 0x27ad, 0x27ae, 0x27af, 0x27b1, 0x27b2, 0x27b3, 0x27b4, 0x27b5, 0x27b6, 0x27b7, 0x27b8, 0x27b9, 0x27ba, 0x27bb, 0x27bc, 0x27bd, 0x27be, 0xf6d9, 0xf6da, 0xf6db, 0xf8d7, 0xf8d8, 0xf8d9, 0xf8da, 0xf8db, 0xf8dc, 0xf8dd, 0xf8de, 0xf8df, 0xf8e0, 0xf8e1, 0xf8e2, 0xf8e3, 0xf8e4, 0xf8e5, 0xf8e6, 0xf8e7, 0xf8e8, 0xf8e9, 0xf8ea, 0xf8eb, 0xf8ec, 0xf8ed, 0xf8ee, 0xf8ef, 0xf8f0, 0xf8f1, 0xf8f2, 0xf8f3, 0xf8f4, 0xf8f5, 0xf8f6, 0xf8f7, 0xf8f8, 0xf8f9, 0xf8fa, 0xf8fb, 0xf8fc, 0xf8fd, 0xf8fe, 0xfe7c, 0xfe7d);
    /**
     * URL-Encode a filename to allow unicodecharacters
     *
     * Slashes are not encoded
     *
     * When the second parameter is true the string will
     * be encoded only if non ASCII characters are detected -
     * This makes it safe to run it multiple times on the
     * same string (default is true)
     *
     * @author Andreas Gohr <andi@splitbrain.org>
     * @see    urlencode
     */
    public static function encode_fn($file, $safe = \true)
    {
        if ($safe && \preg_match('#^[a-zA-Z0-9/_\\-.%]+$#', $file)) {
            return $file;
        }
        $file = \urlencode($file);
        $file = \str_replace('%2F', '/', $file);
        return $file;
    }
    /**
     * URL-Decode a filename
     *
     * This is just a wrapper around urldecode
     *
     * @author Andreas Gohr <andi@splitbrain.org>
     * @see    urldecode
     */
    public static function decode_fn($file)
    {
        $file = \urldecode($file);
        return $file;
    }
    /**
     * Checks if a string contains 7bit ASCII only
     *
     * @author Andreas Gohr <andi@splitbrain.org>
     */
    public static function is_ascii($str)
    {
        for ($i = 0; $i < \strlen($str); $i++) {
            if (\ord($str[$i]) > 127) {
                return \false;
            }
        }
        return \true;
    }
    /**
     * Strips all highbyte chars
     *
     * Returns a pure ASCII7 string
     *
     * @author Andreas Gohr <andi@splitbrain.org>
     */
    public static function strip($str)
    {
        $ascii = '';
        for ($i = 0; $i < \strlen($str); $i++) {
            if (\ord($str[$i]) < 128) {
                $ascii .= $str[$i];
            }
        }
        return $ascii;
    }
    /**
     * Tries to detect if a string is in Unicode encoding
     *
     * @author <bmorel@ssi.fr>
     * @link   http://www.php.net/manual/en/function.utf8-encode.php
     */
    public static function check($str)
    {
        for ($i = 0; $i < \strlen($str); $i++) {
            if (\ord($str[$i]) < 0x80) {
                continue;
            } elseif ((\ord($str[$i]) & 0xe0) == 0xc0) {
                $n = 1;
            } elseif ((\ord($str[$i]) & 0xf0) == 0xe0) {
                $n = 2;
            } elseif ((\ord($str[$i]) & 0xf8) == 0xf0) {
                $n = 3;
            } elseif ((\ord($str[$i]) & 0xfc) == 0xf8) {
                $n = 4;
            } elseif ((\ord($str[$i]) & 0xfe) == 0xfc) {
                $n = 5;
            } else {
                return \false;
            }
            # Does not match any model
            for ($j = 0; $j < $n; $j++) {
                # n bytes matching 10bbbbbb follow ?
                if (++$i == \strlen($str) || (\ord($str[$i]) & 0xc0) != 0x80) {
                    return \false;
                }
            }
        }
        return \true;
    }
    /**
     * Unicode aware replacement for strlen()
     *
     * utf8_decode() converts characters that are not in ISO-8859-1
     * to '?', which, for the purpose of counting, is alright - It's
     * even faster than mb_strlen.
     *
     * @author <chernyshevsky at hotmail dot com>
     * @see    strlen()
     * @see    utf8_decode()
     */
    public static function strlen($string)
    {
        return \strlen(\utf8_decode($string));
    }
    /**
     * Unicode aware replacement for substr()
     *
     * @author lmak at NOSPAM dot iti dot gr
     * @link   http://www.php.net/manual/en/function.substr.php
     * @see    substr()
     */
    public static function substr($str, $start, $length = null)
    {
        $ar = array();
        \preg_match_all("/./u", $str, $ar);
        if ($length != null) {
            return \join("", \array_slice($ar[0], $start, $length));
        } else {
            return \join("", \array_slice($ar[0], $start));
        }
    }
    /**
     * Unicode aware replacement for substr_replace()
     *
     * @author Andreas Gohr <andi@splitbrain.org>
     * @see    substr_replace()
     */
    public static function substr_replace($string, $replacement, $start, $length = null)
    {
        $ret = '';
        if ($start > 0) {
            $ret .= self::substr($string, 0, $start);
        }
        $ret .= $replacement;
        if ($length != null) {
            $ret .= self::substr($string, $start + $length);
        }
        return $ret;
    }
    /**
     * Unicode aware replacement for explode
     *
     * @TODO   support third limit arg
     * @author Harry Fuecks <hfuecks@gmail.com>
     * @see    explode();
     */
    public static function explode($sep, $str)
    {
        if ($sep == '') {
            \trigger_error('Empty delimiter', \E_USER_WARNING);
            return FALSE;
        }
        return \preg_split('!' . \preg_quote($sep, '!') . '!u', $str);
    }
    /**
     * Unicode aware replacement for strrepalce()
     *
     * @todo   support PHP5 count (fourth arg)
     * @author Harry Fuecks <hfuecks@gmail.com>
     * @see    strreplace();
     */
    public static function str_replace($s, $r, $str)
    {
        if (!\is_array($s)) {
            $s = '!' . \preg_quote($s, '!') . '!u';
        } else {
            foreach ($s as $k => $v) {
                $s[$k] = '!' . \preg_quote($v) . '!u';
            }
        }
        return \preg_replace($s, $r, $str);
    }
    /**
     * Unicode aware replacement for ltrim()
     *
     * @author Andreas Gohr <andi@splitbrain.org>
     * @see    ltrim()
     * @return string
     */
    public static function ltrim($str, $charlist = '')
    {
        if ($charlist == '') {
            return \ltrim($str);
        }
        //quote charlist for use in a characterclass
        $charlist = \preg_replace('!([\\\\\\-\\]\\[/])!', '\\\\${1}', $charlist);
        return \preg_replace('/^[' . $charlist . ']+/u', '', $str);
    }
    /**
     * Unicode aware replacement for rtrim()
     *
     * @author Andreas Gohr <andi@splitbrain.org>
     * @see    rtrim()
     * @return string
     */
    public static function rtrim($str, $charlist = '')
    {
        if ($charlist == '') {
            return \rtrim($str);
        }
        //quote charlist for use in a characterclass
        $charlist = \preg_replace('!([\\\\\\-\\]\\[/])!', '\\\\${1}', $charlist);
        return \preg_replace('/[' . $charlist . ']+$/u', '', $str);
    }
    /**
     * Unicode aware replacement for trim()
     *
     * @author Andreas Gohr <andi@splitbrain.org>
     * @see    trim()
     * @return string
     */
    public static function trim($str, $charlist = '')
    {
        if ($charlist == '') {
            return \trim($str);
        }
        return self::ltrim(self::rtrim($str));
    }
    /**
     * This is a unicode aware replacement for strtolower()
     *
     * Uses mb_string extension if available
     *
     * @author Andreas Gohr <andi@splitbrain.org>
     * @see    strtolower()
     * @see    utf8_strtoupper()
     */
    public static function strtolower($string)
    {
        if (!\defined('UTF8_NOMBSTRING') && \function_exists('mb_strtolower')) {
            return \mb_strtolower($string, 'utf-8');
        }
        //global $utf8_upper_to_lower;
        $utf8_upper_to_lower = \array_flip(self::$utf8_lower_to_upper);
        $uni = self::utf8_to_unicode($string);
        $cnt = \count($uni);
        for ($i = 0; $i < $cnt; $i++) {
            if ($utf8_upper_to_lower[$uni[$i]]) {
                $uni[$i] = $utf8_upper_to_lower[$uni[$i]];
            }
        }
        return self::unicode_to_utf8($uni);
    }
    /**
     * This is a unicode aware replacement for strtoupper()
     *
     * Uses mb_string extension if available
     *
     * @author Andreas Gohr <andi@splitbrain.org>
     * @see    strtoupper()
     * @see    utf8_strtoupper()
     */
    public static function strtoupper($string)
    {
        if (!\defined('UTF8_NOMBSTRING') && \function_exists('mb_strtolower')) {
            return \mb_strtoupper($string, 'utf-8');
        }
        //global $utf8_lower_to_upper;
        $uni = self::utf8_to_unicode($string);
        $cnt = \count($uni);
        for ($i = 0; $i < $cnt; $i++) {
            if (self::$utf8_lower_to_upper[$uni[$i]]) {
                $uni[$i] = self::$utf8_lower_to_upper[$uni[$i]];
            }
        }
        return self::unicode_to_utf8($uni);
    }
    /**
     * Replace accented UTF-8 characters by unaccented ASCII-7 equivalents
     *
     * Use the optional parameter to just deaccent lower ($case = -1) or upper ($case = 1)
     * letters. Default is to deaccent both cases ($case = 0)
     *
     * @author Andreas Gohr <andi@splitbrain.org>
     */
    public static function deaccent($string, $case = 0)
    {
        if ($case <= 0) {
            //global $utf8_lower_accents;
            $string = \str_replace(\array_keys(self::$utf8_lower_accents), \array_values(self::$utf8_lower_accents), $string);
        }
        if ($case >= 0) {
            //global $utf8_upper_accents;
            $string = \str_replace(\array_keys(self::$utf8_upper_accents), \array_values(self::$utf8_upper_accents), $string);
        }
        return $string;
    }
    /**
     * Removes special characters (nonalphanumeric) from a UTF-8 string
     *
     * This function adds the controlchars 0x00 to 0x19 to the array of
     * stripped chars (they are not included in $utf8_special_chars)
     *
     * @author Andreas Gohr <andi@splitbrain.org>
     * @param  string $string     The UTF8 string to strip of special chars
     * @param  string $repl       Replace special with this string
     * @param  string $additional Additional chars to strip (used in regexp char class)
     */
    public static function stripspecials($string, $repl = '', $additional = '')
    {
        //global $utf8_special_chars;
        static $specials = null;
        if (\is_null($specials)) {
            $specials = \preg_quote(self::unicode_to_utf8(self::$utf8_special_chars), '/');
        }
        return \preg_replace('/[' . $additional . '\\x00-\\x19' . $specials . ']/u', $repl, $string);
    }
    /**
     * This is an Unicode aware replacement for strpos
     *
     * Uses mb_string extension if available
     *
     * @author Harry Fuecks <hfuecks@gmail.com>
     * @see    strpos()
     */
    public static function strpos($haystack, $needle, $offset = 0)
    {
        if (!\defined('UTF8_NOMBSTRING') && \function_exists('mb_strpos')) {
            return \mb_strpos($haystack, $needle, $offset, 'utf-8');
        }
        if (!$offset) {
            $ar = self::explode($needle, $haystack);
            if (\count($ar) > 1) {
                return self::strlen($ar[0]);
            }
            return \false;
        } else {
            if (!\is_int($offset)) {
                \trigger_error('Offset must be an integer', \E_USER_WARNING);
                return \false;
            }
            $str = self::substr($haystack, $offset);
            if (\false !== ($pos = self::strpos($str, $needle))) {
                return $pos + $offset;
            }
            return \false;
        }
    }
    /**
     * This is an Unicode aware replacement for strrpos
     *
     * Uses mb_string extension if available
     *
     * @author Harry Fuecks <hfuecks@gmail.com>
     * @see    strpos()
     */
    public static function strrpos($haystack, $needle, $offset = 0)
    {
        if (!\defined('UTF8_NOMBSTRING') && \function_exists('mb_strrpos')) {
            return \mb_strrpos($haystack, $needle, $offset, 'utf-8');
        }
        if (!$offset) {
            $ar = self::explode($needle, $haystack);
            $count = \count($ar);
            if ($count > 1) {
                return self::strlen($haystack) - self::strlen($ar[$count - 1]) - self::strlen($needle);
            }
            return \false;
        } else {
            if (!\is_int($offset)) {
                \trigger_error('Offset must be an integer', \E_USER_WARNING);
                return \false;
            }
            $str = self::substr($haystack, $offset);
            if (\false !== ($pos = self::strrpos($str, $needle))) {
                return $pos + $offset;
            }
            return \false;
        }
    }
    /**
     * Encodes UTF-8 characters to HTML entities
     *
     * @author <vpribish at shopping dot com>
     * @link   http://www.php.net/manual/en/function.utf8-decode.php
     */
    public static function tohtml($str)
    {
        $ret = '';
        $max = \strlen($str);
        $last = 0;
        // keeps the index of the last regular character
        for ($i = 0; $i < $max; $i++) {
            $c = $str[$i];
            $c1 = \ord($c);
            if ($c1 >> 5 == 6) {
                // 110x xxxx, 110 prefix for 2 bytes unicode
                $ret .= \substr($str, $last, $i - $last);
                // append all the regular characters we've passed
                $c1 &= 31;
                // remove the 3 bit two bytes prefix
                $c2 = \ord($str[++$i]);
                // the next byte
                $c2 &= 63;
                // remove the 2 bit trailing byte prefix
                $c2 |= ($c1 & 3) << 6;
                // last 2 bits of c1 become first 2 of c2
                $c1 >>= 2;
                // c1 shifts 2 to the right
                $ret .= '&#' . ($c1 * 100 + $c2) . ';';
                // this is the fastest string concatenation
                $last = $i + 1;
            }
        }
        return $ret . \substr($str, $last, $i);
        // append the last batch of regular characters
    }
    /**
     * This function returns any UTF-8 encoded text as a list of
     * Unicode values:
     *
     * @author Scott Michael Reynen <scott@randomchaos.com>
     * @link   http://www.randomchaos.com/document.php?source=php_and_unicode
     * @see    unicode_to_utf8()
     */
    public static function utf8_to_unicode(&$str)
    {
        $unicode = array();
        $values = array();
        $looking_for = 1;
        for ($i = 0; $i < \strlen($str); $i++) {
            $this_value = \ord($str[$i]);
            if ($this_value < 128) {
                $unicode[] = $this_value;
            } else {
                if (\count($values) == 0) {
                    $looking_for = $this_value < 224 ? 2 : 3;
                }
                $values[] = $this_value;
                if (\count($values) == $looking_for) {
                    $number = $looking_for == 3 ? $values[0] % 16 * 4096 + $values[1] % 64 * 64 + $values[2] % 64 : $values[0] % 32 * 64 + $values[1] % 64;
                    $unicode[] = $number;
                    $values = array();
                    $looking_for = 1;
                }
            }
        }
        return $unicode;
    }
    /**
     * This function converts a Unicode array back to its UTF-8 representation
     *
     * @author Scott Michael Reynen <scott@randomchaos.com>
     * @link   http://www.randomchaos.com/document.php?source=php_and_unicode
     * @see    utf8_to_unicode()
     */
    public static function unicode_to_utf8(&$str)
    {
        if (!\is_array($str)) {
            return '';
        }
        $utf8 = '';
        foreach ($str as $unicode) {
            if ($unicode < 128) {
                $utf8 .= \chr($unicode);
            } elseif ($unicode < 2048) {
                $utf8 .= \chr(192 + ($unicode - $unicode % 64) / 64);
                $utf8 .= \chr(128 + $unicode % 64);
            } else {
                $utf8 .= \chr(224 + ($unicode - $unicode % 4096) / 4096);
                $utf8 .= \chr(128 + ($unicode % 4096 - $unicode % 64) / 64);
                $utf8 .= \chr(128 + $unicode % 64);
            }
        }
        return $utf8;
    }
    /**
     * UTF-8 to UTF-16BE conversion.
     *
     * Maybe really UCS-2 without mb_string due to utf8_to_unicode limits
     */
    public static function utf8_to_utf16be(&$str, $bom = \false)
    {
        $out = $bom ? "��" : '';
        if (!\defined('UTF8_NOMBSTRING') && \function_exists('mb_convert_encoding')) {
            return $out . \mb_convert_encoding($str, 'UTF-16BE', 'UTF-8');
        }
        $uni = self::utf8_to_unicode($str);
        foreach ($uni as $cp) {
            $out .= \pack('n', $cp);
        }
        return $out;
    }
    /**
     * UTF-8 to UTF-16BE conversion.
     *
     * Maybe really UCS-2 without mb_string due to utf8_to_unicode limits
     */
    public static function utf16be_to_utf8(&$str)
    {
        $uni = \unpack('n*', $str);
        return self::unicode_to_utf8($uni);
    }
}
