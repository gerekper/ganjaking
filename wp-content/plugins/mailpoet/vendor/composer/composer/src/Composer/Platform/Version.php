<?php
namespace Composer\Platform;
if (!defined('ABSPATH')) exit;
use Composer\Pcre\Preg;
class Version
{
 public static function parseOpenssl($opensslVersion, &$isFips)
 {
 $isFips = false;
 if (!Preg::isMatch('/^(?<version>[0-9.]+)(?<patch>[a-z]{0,2})?(?<suffix>(?:-?(?:dev|pre|alpha|beta|rc|fips)[\d]*)*)?(?<garbage>-\w+)?$/', $opensslVersion, $matches)) {
 return null;
 }
 // OpenSSL 1 used 1.2.3a style versioning, 3+ uses semver
 $patch = '';
 if (version_compare($matches['version'], '3.0.0', '<')) {
 $patch = '.'.self::convertAlphaVersionToIntVersion($matches['patch']);
 }
 $isFips = strpos($matches['suffix'], 'fips') !== false;
 $suffix = strtr('-'.ltrim($matches['suffix'], '-'), array('-fips' => '', '-pre' => '-alpha'));
 return rtrim($matches['version'].$patch.$suffix, '-');
 }
 public static function parseLibjpeg($libjpegVersion)
 {
 if (!Preg::isMatch('/^(?<major>\d+)(?<minor>[a-z]*)$/', $libjpegVersion, $matches)) {
 return null;
 }
 return $matches['major'].'.'.self::convertAlphaVersionToIntVersion($matches['minor']);
 }
 public static function parseZoneinfoVersion($zoneinfoVersion)
 {
 if (!Preg::isMatch('/^(?<year>\d{4})(?<revision>[a-z]*)$/', $zoneinfoVersion, $matches)) {
 return null;
 }
 return $matches['year'].'.'.self::convertAlphaVersionToIntVersion($matches['revision']);
 }
 private static function convertAlphaVersionToIntVersion($alpha)
 {
 return strlen($alpha) * (-ord('a') + 1) + array_sum(array_map('ord', str_split($alpha)));
 }
 public static function convertLibxpmVersionId($versionId)
 {
 return self::convertVersionId($versionId, 100);
 }
 public static function convertOpenldapVersionId($versionId)
 {
 return self::convertVersionId($versionId, 100);
 }
 private static function convertVersionId($versionId, $base)
 {
 return sprintf(
 '%d.%d.%d',
 $versionId / ($base * $base),
 (int) ($versionId / $base) % $base,
 $versionId % $base
 );
 }
}
