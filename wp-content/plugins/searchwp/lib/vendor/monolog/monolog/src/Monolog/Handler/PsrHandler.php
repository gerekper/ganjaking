<?php

declare (strict_types=1);
/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SearchWP\Dependencies\Monolog\Handler;

use SearchWP\Dependencies\Monolog\Logger;
use SearchWP\Dependencies\Psr\Log\LoggerInterface;
use SearchWP\Dependencies\Monolog\Formatter\FormatterInterface;
/**
 * Proxies log messages to an existing PSR-3 compliant logger.
 *
 * If a formatter is configured, the formatter's output MUST be a string and the
 * formatted message will be fed to the wrapped PSR logger instead of the original
 * log record's message.
 *
 * @author Michael Moussa <michael.moussa@gmail.com>
 */
class PsrHandler extends \SearchWP\Dependencies\Monolog\Handler\AbstractHandler implements \SearchWP\Dependencies\Monolog\Handler\FormattableHandlerInterface
{
    /**
     * PSR-3 compliant logger
     *
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var FormatterInterface|null
     */
    protected $formatter;
    /**
     * @param LoggerInterface $logger The underlying PSR-3 compliant logger to which messages will be proxied
     * @param string|int      $level  The minimum logging level at which this handler will be triggered
     * @param bool            $bubble Whether the messages that are handled can bubble up the stack or not
     */
    public function __construct(\SearchWP\Dependencies\Psr\Log\LoggerInterface $logger, $level = \SearchWP\Dependencies\Monolog\Logger::DEBUG, bool $bubble = \true)
    {
        parent::__construct($level, $bubble);
        $this->logger = $logger;
    }
    /**
     * {@inheritDoc}
     */
    public function handle(array $record) : bool
    {
        if (!$this->isHandling($record)) {
            return \false;
        }
        if ($this->formatter) {
            $formatted = $this->formatter->format($record);
            $this->logger->log(\strtolower($record['level_name']), (string) $formatted, $record['context']);
        } else {
            $this->logger->log(\strtolower($record['level_name']), $record['message'], $record['context']);
        }
        return \false === $this->bubble;
    }
    /**
     * Sets the formatter.
     *
     * @param FormatterInterface $formatter
     */
    public function setFormatter(\SearchWP\Dependencies\Monolog\Formatter\FormatterInterface $formatter) : \SearchWP\Dependencies\Monolog\Handler\HandlerInterface
    {
        $this->formatter = $formatter;
        return $this;
    }
    /**
     * Gets the formatter.
     *
     * @return FormatterInterface
     */
    public function getFormatter() : \SearchWP\Dependencies\Monolog\Formatter\FormatterInterface
    {
        if (!$this->formatter) {
            throw new \LogicException('No formatter has been set and this handler does not have a default formatter');
        }
        return $this->formatter;
    }
}
