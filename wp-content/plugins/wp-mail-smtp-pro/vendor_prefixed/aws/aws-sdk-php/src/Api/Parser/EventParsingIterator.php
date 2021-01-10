<?php

namespace WPMailSMTP\Vendor\Aws\Api\Parser;

use Iterator;
use WPMailSMTP\Vendor\Aws\Exception\EventStreamDataException;
use WPMailSMTP\Vendor\Aws\Api\Parser\Exception\ParserException;
use WPMailSMTP\Vendor\Aws\Api\StructureShape;
use WPMailSMTP\Vendor\Psr\Http\Message\StreamInterface;
/**
 * @internal Implements a decoder for a binary encoded event stream that will
 * decode, validate, and provide individual events from the stream.
 */
class EventParsingIterator implements \Iterator
{
    /** @var StreamInterface */
    private $decodingIterator;
    /** @var StructureShape */
    private $shape;
    /** @var AbstractParser */
    private $parser;
    public function __construct(\WPMailSMTP\Vendor\Psr\Http\Message\StreamInterface $stream, \WPMailSMTP\Vendor\Aws\Api\StructureShape $shape, \WPMailSMTP\Vendor\Aws\Api\Parser\AbstractParser $parser)
    {
        $this->decodingIterator = new \WPMailSMTP\Vendor\Aws\Api\Parser\DecodingEventStreamIterator($stream);
        $this->shape = $shape;
        $this->parser = $parser;
    }
    public function current()
    {
        return $this->parseEvent($this->decodingIterator->current());
    }
    public function key()
    {
        return $this->decodingIterator->key();
    }
    public function next()
    {
        $this->decodingIterator->next();
    }
    public function rewind()
    {
        $this->decodingIterator->rewind();
    }
    public function valid()
    {
        return $this->decodingIterator->valid();
    }
    private function parseEvent(array $event)
    {
        if (!empty($event['headers'][':message-type'])) {
            if ($event['headers'][':message-type'] === 'error') {
                return $this->parseError($event);
            }
            if ($event['headers'][':message-type'] !== 'event') {
                throw new \WPMailSMTP\Vendor\Aws\Api\Parser\Exception\ParserException('Failed to parse unknown message type.');
            }
        }
        if (empty($event['headers'][':event-type'])) {
            throw new \WPMailSMTP\Vendor\Aws\Api\Parser\Exception\ParserException('Failed to parse without event type.');
        }
        $eventShape = $this->shape->getMember($event['headers'][':event-type']);
        $parsedEvent = [];
        foreach ($eventShape['members'] as $shape => $details) {
            if (!empty($details['eventpayload'])) {
                $payloadShape = $eventShape->getMember($shape);
                if ($payloadShape['type'] === 'blob') {
                    $parsedEvent[$shape] = $event['payload'];
                } else {
                    $parsedEvent[$shape] = $this->parser->parseMemberFromStream($event['payload'], $payloadShape, null);
                }
            } else {
                $parsedEvent[$shape] = $event['headers'][$shape];
            }
        }
        return [$event['headers'][':event-type'] => $parsedEvent];
    }
    private function parseError(array $event)
    {
        throw new \WPMailSMTP\Vendor\Aws\Exception\EventStreamDataException($event['headers'][':error-code'], $event['headers'][':error-message']);
    }
}
