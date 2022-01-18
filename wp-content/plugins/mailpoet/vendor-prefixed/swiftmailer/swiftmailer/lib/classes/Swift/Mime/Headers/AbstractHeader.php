<?php
namespace MailPoetVendor;
if (!defined('ABSPATH')) exit;
abstract class Swift_Mime_Headers_AbstractHeader implements Swift_Mime_Header
{
 const PHRASE_PATTERN = '(?:(?:(?:(?:(?:(?:(?:[ \\t]*(?:\\r\\n))?[ \\t])?(\\((?:(?:(?:[ \\t]*(?:\\r\\n))?[ \\t])|(?:(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x19\\x7F]|[\\x21-\\x27\\x2A-\\x5B\\x5D-\\x7E])|(?:\\[\\x00-\\x08\\x0B\\x0C\\x0E-\\x7F])|(?1)))*(?:(?:[ \\t]*(?:\\r\\n))?[ \\t])?\\)))*(?:(?:(?:(?:[ \\t]*(?:\\r\\n))?[ \\t])?(\\((?:(?:(?:[ \\t]*(?:\\r\\n))?[ \\t])|(?:(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x19\\x7F]|[\\x21-\\x27\\x2A-\\x5B\\x5D-\\x7E])|(?:\\[\\x00-\\x08\\x0B\\x0C\\x0E-\\x7F])|(?1)))*(?:(?:[ \\t]*(?:\\r\\n))?[ \\t])?\\)))|(?:(?:[ \\t]*(?:\\r\\n))?[ \\t])))?[a-zA-Z0-9!#\\$%&\'\\*\\+\\-\\/=\\?\\^_`\\{\\}\\|~]+(?:(?:(?:(?:[ \\t]*(?:\\r\\n))?[ \\t])?(\\((?:(?:(?:[ \\t]*(?:\\r\\n))?[ \\t])|(?:(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x19\\x7F]|[\\x21-\\x27\\x2A-\\x5B\\x5D-\\x7E])|(?:\\[\\x00-\\x08\\x0B\\x0C\\x0E-\\x7F])|(?1)))*(?:(?:[ \\t]*(?:\\r\\n))?[ \\t])?\\)))*(?:(?:(?:(?:[ \\t]*(?:\\r\\n))?[ \\t])?(\\((?:(?:(?:[ \\t]*(?:\\r\\n))?[ \\t])|(?:(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x19\\x7F]|[\\x21-\\x27\\x2A-\\x5B\\x5D-\\x7E])|(?:\\[\\x00-\\x08\\x0B\\x0C\\x0E-\\x7F])|(?1)))*(?:(?:[ \\t]*(?:\\r\\n))?[ \\t])?\\)))|(?:(?:[ \\t]*(?:\\r\\n))?[ \\t])))?)|(?:(?:(?:(?:(?:[ \\t]*(?:\\r\\n))?[ \\t])?(\\((?:(?:(?:[ \\t]*(?:\\r\\n))?[ \\t])|(?:(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x19\\x7F]|[\\x21-\\x27\\x2A-\\x5B\\x5D-\\x7E])|(?:\\[\\x00-\\x08\\x0B\\x0C\\x0E-\\x7F])|(?1)))*(?:(?:[ \\t]*(?:\\r\\n))?[ \\t])?\\)))*(?:(?:(?:(?:[ \\t]*(?:\\r\\n))?[ \\t])?(\\((?:(?:(?:[ \\t]*(?:\\r\\n))?[ \\t])|(?:(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x19\\x7F]|[\\x21-\\x27\\x2A-\\x5B\\x5D-\\x7E])|(?:\\[\\x00-\\x08\\x0B\\x0C\\x0E-\\x7F])|(?1)))*(?:(?:[ \\t]*(?:\\r\\n))?[ \\t])?\\)))|(?:(?:[ \\t]*(?:\\r\\n))?[ \\t])))?"((?:(?:[ \\t]*(?:\\r\\n))?[ \\t])?(?:(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x19\\x7F]|[\\x21\\x23-\\x5B\\x5D-\\x7E])|(?:\\[\\x00-\\x08\\x0B\\x0C\\x0E-\\x7F])))*(?:(?:[ \\t]*(?:\\r\\n))?[ \\t])?"(?:(?:(?:(?:[ \\t]*(?:\\r\\n))?[ \\t])?(\\((?:(?:(?:[ \\t]*(?:\\r\\n))?[ \\t])|(?:(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x19\\x7F]|[\\x21-\\x27\\x2A-\\x5B\\x5D-\\x7E])|(?:\\[\\x00-\\x08\\x0B\\x0C\\x0E-\\x7F])|(?1)))*(?:(?:[ \\t]*(?:\\r\\n))?[ \\t])?\\)))*(?:(?:(?:(?:[ \\t]*(?:\\r\\n))?[ \\t])?(\\((?:(?:(?:[ \\t]*(?:\\r\\n))?[ \\t])|(?:(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x19\\x7F]|[\\x21-\\x27\\x2A-\\x5B\\x5D-\\x7E])|(?:\\[\\x00-\\x08\\x0B\\x0C\\x0E-\\x7F])|(?1)))*(?:(?:[ \\t]*(?:\\r\\n))?[ \\t])?\\)))|(?:(?:[ \\t]*(?:\\r\\n))?[ \\t])))?))+?)';
 private $name;
 private $encoder;
 private $lineLength = 78;
 private $lang;
 private $charset = 'utf-8';
 private $cachedValue = null;
 public function setCharset($charset)
 {
 $this->clearCachedValueIf($charset != $this->charset);
 $this->charset = $charset;
 if (isset($this->encoder)) {
 $this->encoder->charsetChanged($charset);
 }
 }
 public function getCharset()
 {
 return $this->charset;
 }
 public function setLanguage($lang)
 {
 $this->clearCachedValueIf($this->lang != $lang);
 $this->lang = $lang;
 }
 public function getLanguage()
 {
 return $this->lang;
 }
 public function setEncoder(Swift_Mime_HeaderEncoder $encoder)
 {
 $this->encoder = $encoder;
 $this->setCachedValue(null);
 }
 public function getEncoder()
 {
 return $this->encoder;
 }
 public function getFieldName()
 {
 return $this->name;
 }
 public function setMaxLineLength($lineLength)
 {
 $this->clearCachedValueIf($this->lineLength != $lineLength);
 $this->lineLength = $lineLength;
 }
 public function getMaxLineLength()
 {
 return $this->lineLength;
 }
 public function toString()
 {
 return $this->tokensToString($this->toTokens());
 }
 public function __toString()
 {
 return $this->toString();
 }
 protected function setFieldName($name)
 {
 $this->name = $name;
 }
 protected function createPhrase(Swift_Mime_Header $header, $string, $charset, Swift_Mime_HeaderEncoder $encoder = null, $shorten = \false)
 {
 // Treat token as exactly what was given
 $phraseStr = $string;
 // If it's not valid
 if (!\preg_match('/^' . self::PHRASE_PATTERN . '$/D', $phraseStr)) {
 // .. but it is just ascii text, try escaping some characters
 // and make it a quoted-string
 if (\preg_match('/^[\\x00-\\x08\\x0B\\x0C\\x0E-\\x7F]*$/D', $phraseStr)) {
 $phraseStr = $this->escapeSpecials($phraseStr, ['"']);
 $phraseStr = '"' . $phraseStr . '"';
 } else {
 // ... otherwise it needs encoding
 // Determine space remaining on line if first line
 if ($shorten) {
 $usedLength = \strlen($header->getFieldName() . ': ');
 } else {
 $usedLength = 0;
 }
 $phraseStr = $this->encodeWords($header, $string, $usedLength);
 }
 }
 return $phraseStr;
 }
 private function escapeSpecials($token, $include = [])
 {
 foreach (\array_merge(['\\'], $include) as $char) {
 $token = \str_replace($char, '\\' . $char, $token);
 }
 return $token;
 }
 protected function encodeWords(Swift_Mime_Header $header, $input, $usedLength = -1)
 {
 $value = '';
 $tokens = $this->getEncodableWordTokens($input);
 foreach ($tokens as $token) {
 // See RFC 2822, Sect 2.2 (really 2.2 ??)
 if ($this->tokenNeedsEncoding($token)) {
 // Don't encode starting WSP
 $firstChar = \substr($token, 0, 1);
 switch ($firstChar) {
 case ' ':
 case "\t":
 $value .= $firstChar;
 $token = \substr($token, 1);
 }
 if (-1 == $usedLength) {
 $usedLength = \strlen($header->getFieldName() . ': ') + \strlen($value);
 }
 $value .= $this->getTokenAsEncodedWord($token, $usedLength);
 $header->setMaxLineLength(76);
 // Forcefully override
 } else {
 $value .= $token;
 }
 }
 return $value;
 }
 protected function tokenNeedsEncoding($token)
 {
 return \preg_match('~[\\x00-\\x08\\x10-\\x19\\x7F-\\xFF\\r\\n]~', $token);
 }
 protected function getEncodableWordTokens($string)
 {
 $tokens = [];
 $encodedToken = '';
 // Split at all whitespace boundaries
 foreach (\preg_split('~(?=[\\t ])~', $string ?? '') as $token) {
 if ($this->tokenNeedsEncoding($token)) {
 $encodedToken .= $token;
 } else {
 if (\strlen($encodedToken) > 0) {
 $tokens[] = $encodedToken;
 $encodedToken = '';
 }
 $tokens[] = $token;
 }
 }
 if (\strlen($encodedToken)) {
 $tokens[] = $encodedToken;
 }
 return $tokens;
 }
 protected function getTokenAsEncodedWord($token, $firstLineOffset = 0)
 {
 // Adjust $firstLineOffset to account for space needed for syntax
 $charsetDecl = $this->charset;
 if (isset($this->lang)) {
 $charsetDecl .= '*' . $this->lang;
 }
 $encodingWrapperLength = \strlen('=?' . $charsetDecl . '?' . $this->encoder->getName() . '??=');
 if ($firstLineOffset >= 75) {
 //Does this logic need to be here?
 $firstLineOffset = 0;
 }
 $encodedTextLines = \explode("\r\n", $this->encoder->encodeString($token, $firstLineOffset, 75 - $encodingWrapperLength, $this->charset) ?? '');
 if ('iso-2022-jp' !== \strtolower($this->charset ?? '')) {
 // special encoding for iso-2022-jp using mb_encode_mimeheader
 foreach ($encodedTextLines as $lineNum => $line) {
 $encodedTextLines[$lineNum] = '=?' . $charsetDecl . '?' . $this->encoder->getName() . '?' . $line . '?=';
 }
 }
 return \implode("\r\n ", $encodedTextLines);
 }
 protected function generateTokenLines($token)
 {
 return \preg_split('~(\\r\\n)~', $token ?? '', -1, \PREG_SPLIT_DELIM_CAPTURE);
 }
 protected function setCachedValue($value)
 {
 $this->cachedValue = $value;
 }
 protected function getCachedValue()
 {
 return $this->cachedValue;
 }
 protected function clearCachedValueIf($condition)
 {
 if ($condition) {
 $this->setCachedValue(null);
 }
 }
 protected function toTokens($string = null)
 {
 if (null === $string) {
 $string = $this->getFieldBody();
 }
 $tokens = [];
 // Generate atoms; split at all invisible boundaries followed by WSP
 foreach (\preg_split('~(?=[ \\t])~', $string ?? '') as $token) {
 $newTokens = $this->generateTokenLines($token);
 foreach ($newTokens as $newToken) {
 $tokens[] = $newToken;
 }
 }
 return $tokens;
 }
 private function tokensToString(array $tokens)
 {
 $lineCount = 0;
 $headerLines = [];
 $headerLines[] = $this->name . ': ';
 $currentLine =& $headerLines[$lineCount++];
 // Build all tokens back into compliant header
 foreach ($tokens as $i => $token) {
 // Line longer than specified maximum or token was just a new line
 if ("\r\n" == $token || $i > 0 && \strlen($currentLine . $token) > $this->lineLength && 0 < \strlen($currentLine)) {
 $headerLines[] = '';
 $currentLine =& $headerLines[$lineCount++];
 }
 // Append token to the line
 if ("\r\n" != $token) {
 $currentLine .= $token;
 }
 }
 // Implode with FWS (RFC 2822, 2.2.3)
 return \implode("\r\n", $headerLines) . "\r\n";
 }
 public function __clone()
 {
 if ($this->encoder) {
 $this->encoder = clone $this->encoder;
 }
 }
}
