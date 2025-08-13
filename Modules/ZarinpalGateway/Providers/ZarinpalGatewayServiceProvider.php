<?php

namespace Modules\ZarinpalGateway\Providers;

use Illuminate\Support\ServiceProvider;
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
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
