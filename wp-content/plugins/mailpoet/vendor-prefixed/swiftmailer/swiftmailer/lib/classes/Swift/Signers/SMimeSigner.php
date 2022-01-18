<?php
namespace MailPoetVendor;
if (!defined('ABSPATH')) exit;
class Swift_Signers_SMimeSigner implements Swift_Signers_BodySigner
{
 protected $signCertificate;
 protected $signPrivateKey;
 protected $encryptCert;
 protected $signThenEncrypt = \true;
 protected $signLevel;
 protected $encryptLevel;
 protected $signOptions;
 protected $encryptOptions;
 protected $encryptCipher;
 protected $extraCerts = null;
 protected $wrapFullMessage = \false;
 protected $replacementFactory;
 protected $headerFactory;
 public function __construct($signCertificate = null, $signPrivateKey = null, $encryptCertificate = null)
 {
 if (null !== $signPrivateKey) {
 $this->setSignCertificate($signCertificate, $signPrivateKey);
 }
 if (null !== $encryptCertificate) {
 $this->setEncryptCertificate($encryptCertificate);
 }
 $this->replacementFactory = Swift_DependencyContainer::getInstance()->lookup('transport.replacementfactory');
 $this->signOptions = \PKCS7_DETACHED;
 $this->encryptCipher = \OPENSSL_CIPHER_AES_128_CBC;
 }
 public function setSignCertificate($certificate, $privateKey = null, $signOptions = \PKCS7_DETACHED, $extraCerts = null)
 {
 $this->signCertificate = 'file://' . \str_replace('\\', '/', \realpath($certificate));
 if (null !== $privateKey) {
 if (\is_array($privateKey)) {
 $this->signPrivateKey = $privateKey;
 $this->signPrivateKey[0] = 'file://' . \str_replace('\\', '/', \realpath($privateKey[0]));
 } else {
 $this->signPrivateKey = 'file://' . \str_replace('\\', '/', \realpath($privateKey));
 }
 }
 $this->signOptions = $signOptions;
 $this->extraCerts = $extraCerts ? \realpath($extraCerts) : null;
 return $this;
 }
 public function setEncryptCertificate($recipientCerts, $cipher = null)
 {
 if (\is_array($recipientCerts)) {
 $this->encryptCert = [];
 foreach ($recipientCerts as $cert) {
 $this->encryptCert[] = 'file://' . \str_replace('\\', '/', \realpath($cert));
 }
 } else {
 $this->encryptCert = 'file://' . \str_replace('\\', '/', \realpath($recipientCerts));
 }
 if (null !== $cipher) {
 $this->encryptCipher = $cipher;
 }
 return $this;
 }
 public function getSignCertificate()
 {
 return $this->signCertificate;
 }
 public function getSignPrivateKey()
 {
 return $this->signPrivateKey;
 }
 public function setSignThenEncrypt($signThenEncrypt = \true)
 {
 $this->signThenEncrypt = $signThenEncrypt;
 return $this;
 }
 public function isSignThenEncrypt()
 {
 return $this->signThenEncrypt;
 }
 public function reset()
 {
 return $this;
 }
 public function setWrapFullMessage($wrap)
 {
 $this->wrapFullMessage = $wrap;
 }
 public function signMessage(Swift_Message $message)
 {
 if (null === $this->signCertificate && null === $this->encryptCert) {
 return $this;
 }
 if ($this->signThenEncrypt) {
 $this->smimeSignMessage($message);
 $this->smimeEncryptMessage($message);
 } else {
 $this->smimeEncryptMessage($message);
 $this->smimeSignMessage($message);
 }
 }
 public function getAlteredHeaders()
 {
 return ['Content-Type', 'Content-Transfer-Encoding', 'Content-Disposition'];
 }
 protected function smimeSignMessage(Swift_Message $message)
 {
 // If we don't have a certificate we can't sign the message
 if (null === $this->signCertificate) {
 return;
 }
 // Work on a clone of the original message
 $signMessage = clone $message;
 $signMessage->clearSigners();
 if ($this->wrapFullMessage) {
 // The original message essentially becomes the body of the new
 // wrapped message
 $signMessage = $this->wrapMimeMessage($signMessage);
 } else {
 // Only keep header needed to parse the body correctly
 $this->clearAllHeaders($signMessage);
 $this->copyHeaders($message, $signMessage, ['Content-Type', 'Content-Transfer-Encoding', 'Content-Disposition']);
 }
 // Copy the cloned message into a temporary file stream
 $messageStream = new Swift_ByteStream_TemporaryFileByteStream();
 $signMessage->toByteStream($messageStream);
 $messageStream->commit();
 $signedMessageStream = new Swift_ByteStream_TemporaryFileByteStream();
 // Sign the message using openssl
 if (!\openssl_pkcs7_sign($messageStream->getPath(), $signedMessageStream->getPath(), $this->signCertificate, $this->signPrivateKey, [], $this->signOptions, $this->extraCerts)) {
 throw new Swift_IoException(\sprintf('Failed to sign S/Mime message. Error: "%s".', \openssl_error_string()));
 }
 // Parse the resulting signed message content back into the Swift message
 // preserving the original headers
 $this->parseSSLOutput($signedMessageStream, $message);
 }
 protected function smimeEncryptMessage(Swift_Message $message)
 {
 // If we don't have a certificate we can't encrypt the message
 if (null === $this->encryptCert) {
 return;
 }
 // Work on a clone of the original message
 $encryptMessage = clone $message;
 $encryptMessage->clearSigners();
 if ($this->wrapFullMessage) {
 // The original message essentially becomes the body of the new
 // wrapped message
 $encryptMessage = $this->wrapMimeMessage($encryptMessage);
 } else {
 // Only keep header needed to parse the body correctly
 $this->clearAllHeaders($encryptMessage);
 $this->copyHeaders($message, $encryptMessage, ['Content-Type', 'Content-Transfer-Encoding', 'Content-Disposition']);
 }
 // Convert the message content (including headers) to a string
 // and place it in a temporary file
 $messageStream = new Swift_ByteStream_TemporaryFileByteStream();
 $encryptMessage->toByteStream($messageStream);
 $messageStream->commit();
 $encryptedMessageStream = new Swift_ByteStream_TemporaryFileByteStream();
 // Encrypt the message
 if (!\openssl_pkcs7_encrypt($messageStream->getPath(), $encryptedMessageStream->getPath(), $this->encryptCert, [], 0, $this->encryptCipher)) {
 throw new Swift_IoException(\sprintf('Failed to encrypt S/Mime message. Error: "%s".', \openssl_error_string()));
 }
 // Parse the resulting signed message content back into the Swift message
 // preserving the original headers
 $this->parseSSLOutput($encryptedMessageStream, $message);
 }
 protected function copyHeaders(Swift_Message $fromMessage, Swift_Message $toMessage, array $headers = [])
 {
 foreach ($headers as $header) {
 $this->copyHeader($fromMessage, $toMessage, $header);
 }
 }
 protected function copyHeader(Swift_Message $fromMessage, Swift_Message $toMessage, $headerName)
 {
 $header = $fromMessage->getHeaders()->get($headerName);
 if (!$header) {
 return;
 }
 $headers = $toMessage->getHeaders();
 switch ($header->getFieldType()) {
 case Swift_Mime_Header::TYPE_TEXT:
 $headers->addTextHeader($header->getFieldName(), $header->getValue());
 break;
 case Swift_Mime_Header::TYPE_PARAMETERIZED:
 $headers->addParameterizedHeader($header->getFieldName(), $header->getValue(), $header->getParameters());
 break;
 }
 }
 protected function clearAllHeaders(Swift_Message $message)
 {
 $headers = $message->getHeaders();
 foreach ($headers->listAll() as $header) {
 $headers->removeAll($header);
 }
 }
 protected function wrapMimeMessage(Swift_Message $message)
 {
 // Start by copying the original message into a message stream
 $messageStream = new Swift_ByteStream_TemporaryFileByteStream();
 $message->toByteStream($messageStream);
 $messageStream->commit();
 // Create a new MIME part that wraps the original stream
 $wrappedMessage = new Swift_MimePart($messageStream, 'message/rfc822');
 $wrappedMessage->setEncoder(new Swift_Mime_ContentEncoder_PlainContentEncoder('7bit'));
 return $wrappedMessage;
 }
 protected function parseSSLOutput(Swift_InputByteStream $inputStream, Swift_Message $message)
 {
 $messageStream = new Swift_ByteStream_TemporaryFileByteStream();
 $this->copyFromOpenSSLOutput($inputStream, $messageStream);
 $this->streamToMime($messageStream, $message);
 }
 protected function streamToMime(Swift_OutputByteStream $fromStream, Swift_Message $message)
 {
 // Parse the stream into headers and body
 list($headers, $messageStream) = $this->parseStream($fromStream);
 // Get the original message headers
 $messageHeaders = $message->getHeaders();
 // Let the stream determine the headers describing the body content,
 // since the body of the original message is overwritten by the body
 // coming from the stream.
 // These are all content-* headers.
 // Default transfer encoding is 7bit if not set
 $encoding = '';
 // Remove all existing transfer encoding headers
 $messageHeaders->removeAll('Content-Transfer-Encoding');
 // See whether the stream sets the transfer encoding
 if (isset($headers['content-transfer-encoding'])) {
 $encoding = $headers['content-transfer-encoding'];
 }
 // We use the null content encoder, since the body is already encoded
 // according to the transfer encoding specified in the stream
 $message->setEncoder(new Swift_Mime_ContentEncoder_NullContentEncoder($encoding));
 // Set the disposition, if present
 if (isset($headers['content-disposition'])) {
 $messageHeaders->addTextHeader('Content-Disposition', $headers['content-disposition']);
 }
 // Copy over the body from the stream using the content type dictated
 // by the stream content
 $message->setChildren([]);
 $message->setBody($messageStream, $headers['content-type']);
 }
 protected function parseStream(Swift_OutputByteStream $emailStream)
 {
 $bufferLength = 78;
 $headerData = '';
 $headerBodySeparator = "\r\n\r\n";
 $emailStream->setReadPointer(0);
 // Read out the headers section from the stream to a string
 while (\false !== ($buffer = $emailStream->read($bufferLength))) {
 $headerData .= $buffer;
 $headersPosEnd = \strpos($headerData, $headerBodySeparator);
 // Stop reading if we found the end of the headers
 if (\false !== $headersPosEnd) {
 break;
 }
 }
 // Split the header data into lines
 $headerData = \trim(\substr($headerData, 0, $headersPosEnd));
 $headerLines = \explode("\r\n", $headerData);
 unset($headerData);
 $headers = [];
 $currentHeaderName = '';
 // Transform header lines into an associative array
 foreach ($headerLines as $headerLine) {
 // Handle headers that span multiple lines
 if (\false === \strpos($headerLine, ':')) {
 $headers[$currentHeaderName] .= ' ' . \trim($headerLine ?? '');
 continue;
 }
 $header = \explode(':', $headerLine, 2);
 $currentHeaderName = \strtolower($header[0] ?? '');
 $headers[$currentHeaderName] = \trim($header[1] ?? '');
 }
 // Read the entire email body into a byte stream
 $bodyStream = new Swift_ByteStream_TemporaryFileByteStream();
 // Skip the header and separator and point to the body
 $emailStream->setReadPointer($headersPosEnd + \strlen($headerBodySeparator));
 while (\false !== ($buffer = $emailStream->read($bufferLength))) {
 $bodyStream->write($buffer);
 }
 $bodyStream->commit();
 return [$headers, $bodyStream];
 }
 protected function copyFromOpenSSLOutput(Swift_OutputByteStream $fromStream, Swift_InputByteStream $toStream)
 {
 $bufferLength = 4096;
 $filteredStream = new Swift_ByteStream_TemporaryFileByteStream();
 $filteredStream->addFilter($this->replacementFactory->createFilter("\r\n", "\n"), 'CRLF to LF');
 $filteredStream->addFilter($this->replacementFactory->createFilter("\n", "\r\n"), 'LF to CRLF');
 while (\false !== ($buffer = $fromStream->read($bufferLength))) {
 $filteredStream->write($buffer);
 }
 $filteredStream->flushBuffers();
 while (\false !== ($buffer = $filteredStream->read($bufferLength))) {
 $toStream->write($buffer);
 }
 $toStream->commit();
 }
}
