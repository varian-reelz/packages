<?php

namespace VarianReelz\QueryParser\Providers;

use Illuminate\Support\Facades\Facade;

class QueryParserFacades extends Facade {
    protected static function getFacadeAccessor() {
        return 'QueryParser';
    }
}