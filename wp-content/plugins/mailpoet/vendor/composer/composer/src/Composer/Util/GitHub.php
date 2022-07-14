<?php
namespace Composer\Util;
if (!defined('ABSPATH')) exit;
use Composer\Factory;
use Composer\IO\IOInterface;
use Composer\Config;
use Composer\Downloader\TransportException;
use Composer\Pcre\Preg;
class GitHub
{
 protected $io;
 protected $config;
 protected $process;
 protected $httpDownloader;
 public function __construct(IOInterface $io, Config $config, ProcessExecutor $process = null, HttpDownloader $httpDownloader = null)
 {
 $this->io = $io;
 $this->config = $config;
 $this->process = $process ?: new ProcessExecutor($io);
 $this->httpDownloader = $httpDownloader ?: Factory::createHttpDownloader($this->io, $config);
 }
 public function authorizeOAuth($originUrl)
 {
 if (!in_array($originUrl, $this->config->get('github-domains'))) {
 return false;
 }
 // if available use token from git config
 if (0 === $this->process->execute('git config github.accesstoken', $output)) {
 $this->io->setAuthentication($originUrl, trim($output), 'x-oauth-basic');
 return true;
 }
 return false;
 }
 public function authorizeOAuthInteractively($originUrl, $message = null)
 {
 if ($message) {
 $this->io->writeError($message);
 }
 $note = 'Composer';
 if ($this->config->get('github-expose-hostname') === true && 0 === $this->process->execute('hostname', $output)) {
 $note .= ' on ' . trim($output);
 }
 $note .= ' ' . date('Y-m-d Hi');
 $url = 'https://'.$originUrl.'/settings/tokens/new?scopes=&description=' . str_replace('%20', '+', rawurlencode($note));
 $this->io->writeError(sprintf('When working with _public_ GitHub repositories only, head to %s to retrieve a token.', $url));
 $this->io->writeError('This token will have read-only permission for public information only.');
 $url = 'https://'.$originUrl.'/settings/tokens/new?scopes=repo&description=' . str_replace('%20', '+', rawurlencode($note));
 $this->io->writeError(sprintf('When you need to access _private_ GitHub repositories as well, go to %s', $url));
 $this->io->writeError('Note that such tokens have broad read/write permissions on your behalf, even if not needed by Composer.');
 $this->io->writeError(sprintf('Tokens will be stored in plain text in "%s" for future use by Composer.', $this->config->getAuthConfigSource()->getName()));
 $this->io->writeError('For additional information, check https://getcomposer.org/doc/articles/authentication-for-private-packages.md#github-oauth');
 $token = trim($this->io->askAndHideAnswer('Token (hidden): '));
 if (!$token) {
 $this->io->writeError('<warning>No token given, aborting.</warning>');
 $this->io->writeError('You can also add it manually later by using "composer config --global --auth github-oauth.github.com <token>"');
 return false;
 }
 $this->io->setAuthentication($originUrl, $token, 'x-oauth-basic');
 try {
 $apiUrl = ('github.com' === $originUrl) ? 'api.github.com/' : $originUrl . '/api/v3/';
 $this->httpDownloader->get('https://'. $apiUrl, array(
 'retry-auth-failure' => false,
 ));
 } catch (TransportException $e) {
 if (in_array($e->getCode(), array(403, 401))) {
 $this->io->writeError('<error>Invalid token provided.</error>');
 $this->io->writeError('You can also add it manually later by using "composer config --global --auth github-oauth.github.com <token>"');
 return false;
 }
 throw $e;
 }
 // store value in user config
 $this->config->getConfigSource()->removeConfigSetting('github-oauth.'.$originUrl);
 $this->config->getAuthConfigSource()->addConfigSetting('github-oauth.'.$originUrl, $token);
 $this->io->writeError('<info>Token stored successfully.</info>');
 return true;
 }
 public function getRateLimit(array $headers)
 {
 $rateLimit = array(
 'limit' => '?',
 'reset' => '?',
 );
 foreach ($headers as $header) {
 $header = trim($header);
 if (false === strpos($header, 'X-RateLimit-')) {
 continue;
 }
 list($type, $value) = explode(':', $header, 2);
 switch ($type) {
 case 'X-RateLimit-Limit':
 $rateLimit['limit'] = (int) trim($value);
 break;
 case 'X-RateLimit-Reset':
 $rateLimit['reset'] = date('Y-m-d H:i:s', (int) trim($value));
 break;
 }
 }
 return $rateLimit;
 }
 public function getSsoUrl(array $headers)
 {
 foreach ($headers as $header) {
 $header = trim($header);
 if (false === stripos($header, 'x-github-sso: required')) {
 continue;
 }
 if (Preg::isMatch('{\burl=(?P<url>[^\s;]+)}', $header, $match)) {
 return $match['url'];
 }
 }
 return null;
 }
 public function isRateLimited(array $headers)
 {
 foreach ($headers as $header) {
 if (Preg::isMatch('{^X-RateLimit-Remaining: *0$}i', trim($header))) {
 return true;
 }
 }
 return false;
 }
 public function requiresSso(array $headers)
 {
 foreach ($headers as $header) {
 if (Preg::isMatch('{^X-GitHub-SSO: required}i', trim($header))) {
 return true;
 }
 }
 return false;
 }
}
