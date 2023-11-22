<?php

declare (strict_types=1);
namespace DynamicOOOS\PHPHtmlParser\Contracts;

use DynamicOOOS\PHPHtmlParser\Dom;
use DynamicOOOS\PHPHtmlParser\Options;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
interface DomInterface
{
    public function loadFromFile(string $file, ?Options $options = null) : Dom;
    public function loadFromUrl(string $url, ?Options $options, ?ClientInterface $client = null, ?RequestInterface $request = null) : Dom;
    public function loadStr(string $str, ?Options $options = null) : Dom;
    public function setOptions(Options $options) : Dom;
    public function find(string $selector, int $nth = null);
}
