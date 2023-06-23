<?php
/**
 * @license MIT
 *
 * Modified by woocommerce on 14-June-2023 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */ declare(strict_types=1);

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Automattic\WooCommerce\Bookings\Vendor\Monolog\Processor;

/**
 * Injects memory_get_usage in all records
 *
 * @see Automattic\WooCommerce\Bookings\Vendor\Monolog\Processor\MemoryProcessor::__construct() for options
 * @author Rob Jensen
 */
class MemoryUsageProcessor extends MemoryProcessor
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(array $record): array
    {
        $usage = memory_get_usage($this->realUsage);

        if ($this->useFormatting) {
            $usage = $this->formatBytes($usage);
        }

        $record['extra']['memory_usage'] = $usage;

        return $record;
    }
}
