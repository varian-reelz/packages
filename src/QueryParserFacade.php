<?php

namespace VarianReelz\QueryParser;

use Illuminate\Support\Facades\Facade;

class QueryParserFacade extends Facade {
    protected static function getFacadeAccessor() {
        return 'QueryParser';
    }
}