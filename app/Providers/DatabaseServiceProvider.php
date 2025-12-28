<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use PDO;

class DatabaseServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::beforeExecuting(function ($query) {
                if (str_contains($query, 'select * from "users"')) {
                    DB::statement('SET SESSION CHARACTERISTICS AS TRANSACTION ISOLATION LEVEL READ COMMITTED');
                }
            });
        }
    }
}