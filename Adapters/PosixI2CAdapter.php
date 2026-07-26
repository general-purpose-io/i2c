<?php

namespace GeneralPurposeIO\I2C\Adapters;

use GeneralPurposeIO\Contracts\Common\GPIOException;
use GeneralPurposeIO\I2C\I2CCommunicationAdapter;
use GeneralPurposeIO\Common\ConfirmPOSIXDependencies;

class PosixI2CAdapter extends I2CCommunicationAdapter
{

    protected function confirmDependencies(): void
    {
        ConfirmPOSIXDependencies::run('I2C');

        if (!function_exists('i2c_open')) {
            throw new GPIOException('The POSIX I2C adapter requires the I2C package. Require it with composer require microscrap/i2c');
        }
    }
}