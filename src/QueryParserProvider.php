<?php

namespace VarianReelz\QueryParser\Providers;

use Illuminate\Support\ServiceProvider;

class QueryParserProvider extends ServiceProvider {
    public function boot() {
        
    }

    public function register() {
        $this->app->make('VarianReelz\QueryParser\QueryParser');
    }
}