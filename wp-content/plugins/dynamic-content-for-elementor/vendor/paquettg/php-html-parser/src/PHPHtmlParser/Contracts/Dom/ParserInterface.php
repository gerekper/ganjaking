<?php

namespace DynamicOOOS\PHPHtmlParser\Contracts\Dom;

use DynamicOOOS\PHPHtmlParser\Content;
use DynamicOOOS\PHPHtmlParser\Dom\Node\AbstractNode;
use DynamicOOOS\PHPHtmlParser\Exceptions\ChildNotFoundException;
use DynamicOOOS\PHPHtmlParser\Exceptions\CircularException;
use DynamicOOOS\PHPHtmlParser\Exceptions\ContentLengthException;
use DynamicOOOS\PHPHtmlParser\Exceptions\LogicalException;
use DynamicOOOS\PHPHtmlParser\Exceptions\StrictException;
use DynamicOOOS\PHPHtmlParser\Options;
interface ParserInterface
{
    /**
     * Attempts to parse the html in content.
     *
     * @throws ChildNotFoundException
     * @throws CircularException
     * @throws ContentLengthException
     * @throws LogicalException
     * @throws StrictException
     */
    public function parse(Options $options, Content $content, int $size) : AbstractNode;
    /**
     * Attempts to detect the charset that the html was sent in.
     *
     * @throws ChildNotFoundException
     */
    public function detectCharset(Options $options, string $defaultCharset, AbstractNode $root) : bool;
}
