<?php

namespace SearchWP\Dependencies\Psr\Log;

/**
 * Basic Implementation of LoggerAwareInterface.
 */
trait LoggerAwareTrait
{
    /**
     * The logger instance.
     *
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * Sets a logger.
     *
     * @param LoggerInterface $logger
     */
    public function setLogger(\SearchWP\Dependencies\Psr\Log\LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}
