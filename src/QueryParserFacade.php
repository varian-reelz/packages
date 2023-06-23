<?php

namespace VarianReelz\QueryParser;

use Illuminate\Support\Facades\Facade;

class QueryParserFacades extends Facade {
    protected static function getFacadeAccessor() {
        return 'QueryParser';
    }
}