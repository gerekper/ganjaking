<?php
declare(strict_types=1);

namespace ACP\ListScreenRepository;

interface SourceAware
{

    public function get_sources() : SourceCollection;

}