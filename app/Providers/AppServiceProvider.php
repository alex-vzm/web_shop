<?php

namespace App\Providers;

use App\Http\Kernel;
use Carbon\CarbonInterval;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Model::shouldBeStrict(!app()->isProduction());


        if (app()->isProduction()) {
            DB::whenQueryingForLongerThan(CarbonInterval::seconds(5), function (Connection $connection) {
                // Notify development team...
                //  Отправить уведомление если запрос выполняется больше чем указано милисекунд (ЕСЛИ ДОЛГО)

                logger()
                    ->channel('telegram')
                    ->debug('whenQueryingForLongerThan: ' . $connection->totalQueryDuration());
            });


            DB::listen(function ($query) {
                if ($query->time > 100) {
                    logger()
                        ->channel('telegram')
                        ->debug(
                            'whenQueryingForLongerThan (запрос долго выполняется): ' . $query()->Sql,
                            $query->bindings
                        );
                }
                //  dump($query->sql);
            });


            $kernel = app(kernel::class);
            $kernel->whenRequestLifecycleIsLongerThan(
                CarbonInterval::seconds(4),
                function () {
                    logger()
                        ->channel('telegram')
                        ->debug('whenRequestLifecycleIsLongerThan: ' . request()->url());
                }
            );
        }
    }
}
