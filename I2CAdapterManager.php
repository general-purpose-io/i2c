<?php

namespace GeneralPurposeIO\I2C;

use InvalidArgumentException;
use Fabricate\NutsAndBolts\Manager;
use GeneralPurposeIO\Contracts\Common\GPIOException;
use Fabricate\Contracts\Chassis\CircularDependencyException;
use GeneralPurposeIO\Contracts\I2C\I2CCommunicationAdapter as AdapterInterface;
use GeneralPurposeIO\Contracts\Common\GPIOCommunicationAdapterManager as AdapterManager;

class I2CAdapterManager extends Manager implements AdapterManager
{
    /**
     * @throws GPIOException
     */
    public function adapter(?string $adapter = null): AdapterInterface
    {
        try {
            return $this->driver($adapter);
        }
        catch (InvalidArgumentException)
        {
            throw new GPIOException("I2C Adapter [$adapter] not registered. Is it defined in config(gpio.protocols.i2c.adapters)?");
        }
    }

    /**
     * @throws CircularDependencyException
     */
    public function getDefaultDriver(): ?string
    {
        return config('gpio.protocols.i2c.default');
    }
}