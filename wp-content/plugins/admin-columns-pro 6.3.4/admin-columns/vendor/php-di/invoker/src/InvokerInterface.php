<?php

namespace AC\Vendor\Invoker;

use AC\Vendor\Invoker\Exception\InvocationException;
use AC\Vendor\Invoker\Exception\NotCallableException;
use AC\Vendor\Invoker\Exception\NotEnoughParametersException;
/**
 * Invoke a callable.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
interface InvokerInterface
{
    /**
     * Call the given function using the given parameters.
     *
     * @param callable $callable   Function to call.
     * @param array    $parameters Parameters to use.
     *
     * @return mixed Result of the function.
     *
     * @throws InvocationException Base exception class for all the sub-exceptions below.
     * @throws NotCallableException
     * @throws NotEnoughParametersException
     */
    public function call($callable, array $parameters = array());
}
