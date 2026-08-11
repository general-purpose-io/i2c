<?php

namespace GeneralPurposeIO\I2C;

use Fabricate\Chassis\Exceptions\CircularDependencyException;
use Fabricate\Contracts\Core\Program;
use Fabricate\NutsAndBolts\Contracts\DeferrableProvider;
use Fabricate\NutsAndBolts\ServiceProvider;
use GeneralPurposeIO\Core\MagicAliases\GPIO;
use GeneralPurposeIO\I2C\Console\I2CDetectCommand;

class I2CServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function register(): void
    {
        $this->container->singleton('gpio.i2c', fn (Program $program) => new I2CAdapterManager($program));
        $this->container->alias('gpio.i2c', I2CAdapterManager::class);

        $this->container->singleton(I2CDetectCommand::class);

        $this->commands([
            I2CDetectCommand::class,
        ]);
    }

    /**
     * @throws CircularDependencyException
     */
    public function boot(): void
    {
        $adapters = config('gpio.protocols.i2c.adapters');
        foreach ($adapters as $adapter => $adapter_class) {
            I2C::extend($adapter, fn () => new $adapter_class);
        }

        GPIO::extend('i2c', fn () => app('gpio.i2c'));
    }

    public function provides(): array
    {
        return [
            'gpio.i2c',
            I2CDetectCommand::class,
        ];
    }
}