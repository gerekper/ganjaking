<?php
namespace Composer\Util;
if (!defined('ABSPATH')) exit;
use Composer\IO\IOInterface;
use Composer\Config;
use Composer\Factory;
use Composer\Downloader\TransportException;
use Composer\Pcre\Preg;
class GitLab
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
 // before composer 1.9, origin URLs had no port number in them
 $bcOriginUrl = Preg::replace('{:\d+}', '', $originUrl);
 if (!in_array($originUrl, $this->config->get('gitlab-domains'), true) && !in_array($bcOriginUrl, $this->config->get('gitlab-domains'), true)) {
 return false;
 }
 // if available use token from git config
 if (0 === $this->process->execute('git config gitlab.accesstoken', $output)) {
 $this->io->setAuthentication($originUrl, trim($output), 'oauth2');
 return true;
 }
 // if available use deploy token from git config
 if (0 === $this->process->execute('git config gitlab.deploytoken.user', $tokenUser) && 0 === $this->process->execute('git config gitlab.deploytoken.token', $tokenPassword)) {
 $this->io->setAuthentication($originUrl, trim($tokenUser), trim($tokenPassword));
 return true;
 }
 // if available use token from composer config
 $authTokens = $this->config->get('gitlab-token');
 if (isset($authTokens[$originUrl])) {
 $token = $authTokens[$originUrl];
 }
 if (isset($authTokens[$bcOriginUrl])) {
 $token = $authTokens[$bcOriginUrl];
 }
 if (isset($token)) {
 $username = is_array($token) && array_key_exists("username", $token) ? $token["username"] : $token;
 $password = is_array($token) && array_key_exists("token", $token) ? $token["token"] : 'private-token';
 // Composer expects the GitLab token to be stored as username and 'private-token' or 'gitlab-ci-token' to be stored as password
 // Detect cases where this is reversed and resolve automatically resolve it
 if (in_array($username, array('private-token', 'gitlab-ci-token', 'oauth2'), true)) {
 $this->io->setAuthentication($originUrl, $password, $username);
 } else {
 $this->io->setAuthentication($originUrl, $username, $password);
 }
 return true;
 }
 return false;
 }
 public function authorizeOAuthInteractively($scheme, $originUrl, $message = null)
 {
 if ($message) {
 $this->io->writeError($message);
 }
 $this->io->writeError(sprintf('A token will be created and stored in "%s", your password will never be stored', $this->config->getAuthConfigSource()->getName()));
 $this->io->writeError('To revoke access to this token you can visit '.$scheme.'://'.$originUrl.'/-/profile/personal_access_tokens');
 $attemptCounter = 0;
 while ($attemptCounter++ < 5) {
 try {
 $response = $this->createToken($scheme, $originUrl);
 } catch (TransportException $e) {
 // 401 is bad credentials,
 // 403 is max login attempts exceeded
 if (in_array($e->getCode(), array(403, 401))) {
 if (401 === $e->getCode()) {
 $response = json_decode($e->getResponse(), true);
 if (isset($response['error']) && $response['error'] === 'invalid_grant') {
 $this->io->writeError('Bad credentials. If you have two factor authentication enabled you will have to manually create a personal access token');
 } else {
 $this->io->writeError('Bad credentials.');
 }
 } else {
 $this->io->writeError('Maximum number of login attempts exceeded. Please try again later.');
 }
 $this->io->writeError('You can also manually create a personal access token enabling the "read_api" scope at '.$scheme.'://'.$originUrl.'/profile/personal_access_tokens');
 $this->io->writeError('Add it using "composer config --global --auth gitlab-token.'.$originUrl.' <token>"');
 continue;
 }
 throw $e;
 }
 $this->io->setAuthentication($originUrl, $response['access_token'], 'oauth2');
 // store value in user config in auth file
 $this->config->getAuthConfigSource()->addConfigSetting('gitlab-oauth.'.$originUrl, $response['access_token']);
 return true;
 }
 throw new \RuntimeException('Invalid GitLab credentials 5 times in a row, aborting.');
 }
 private function createToken($scheme, $originUrl)
 {
 $username = $this->io->ask('Username: ');
 $password = $this->io->askAndHideAnswer('Password: ');
 $headers = array('Content-Type: application/x-www-form-urlencoded');
 $apiUrl = $originUrl;
 $data = http_build_query(array(
 'username' => $username,
 'password' => $password,
 'grant_type' => 'password',
 ), '', '&');
 $options = array(
 'retry-auth-failure' => false,
 'http' => array(
 'method' => 'POST',
 'header' => $headers,
 'content' => $data,
 ),
 );
 $token = $this->httpDownloader->get($scheme.'://'.$apiUrl.'/oauth/token', $options)->decodeJson();
 $this->io->writeError('Token successfully created');
 return $token;
 }
}
