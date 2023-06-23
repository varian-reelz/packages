<?php

namespace VarianReelz\QueryParser\Providers;

use Illuminate\Support\ServiceProvider;
use VarianReelz\QueryParser\QueryParser;

class QueryParserProvider extends ServiceProvider {
    public function boot() {
        
    }

    public function register() {
        $this->app->bind('QueryParser', function ($app) {
            return new QueryParser();
        });
        // $this->app->make('VarianReelz\QueryParser\QueryParser');
    }
}