<?php

namespace DynamicOOOS\PayPalCheckoutSdk\Core;

use DynamicOOOS\PayPalHttp\Injector;
class GzipInjector implements Injector
{
    public function inject($httpRequest)
    {
        $httpRequest->headers["Accept-Encoding"] = "gzip";
    }
}
