<?php

namespace NinjaTablesPro\App\Hooks\Handlers;

use NinjaTablesPro\App\Modules\DataProviders\CsvProvider;
use NinjaTablesPro\App\Modules\DataProviders\RawSqlProvider;
use NinjaTablesPro\App\Modules\DataProviders\WoocommercePostsProvider;
use NinjaTablesPro\App\Modules\DataProviders\WPPostsProvider;


class DataProviderHandler
{
    public function handle()
    {
        (new WPPostsProvider())->boot();
        (new WoocommercePostsProvider())->boot();
        (new CsvProvider())->boot();
        (new RawSqlProvider())->boot();
    }
}
