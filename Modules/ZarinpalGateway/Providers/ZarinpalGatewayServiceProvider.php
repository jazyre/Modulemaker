<?php

namespace Modules\ZarinpalGateway\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Modules\ZarinpalGateway\Entities\ZarinpalGateway;
use App\Interfaces\PaymentInterface;

class ZarinpalGatewayServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'ZarinpalGateway';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'zarinpalgateway';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerConfig();
        $this->registerDynamicEvents();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(PaymentInterface::class, ZarinpalGateway::class);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__.'/../Config/config.php' => config_path($this->moduleNameLower . '.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', $this->moduleNameLower
        );
    }

    /**
     * Register events and listeners dynamically from the database.
     *
     * @return void
     */
    protected function registerDynamicEvents()
    {
        // Ensure the table exists to prevent errors during initial migration.
        if (Schema::hasTable('module_listeners')) {
            $listeners = DB::table('module_listeners')->get();

            foreach ($listeners as $listener) {
                // Check if the listener class and event class exist before registering.
                if (class_exists($listener->event) && class_exists($listener->listener)) {
                    Event::listen(
                        $listener->event,
                        $listener->listener
                    );
                }
            }
        }
    }


    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
