<?php

namespace GeneralPurposeIO\I2C\Drivers;

use Microscrap\Bindings\I2C\DataObjects\I2CBus;
use Microscrap\Bindings\I2C\Enums\I2CMsgFlag;

class PosixI2CDriver extends I2CDriver
{
    public function __construct(
        public readonly int $fd
    ) {}

    public function writeRead(int $address, array|string $bytes_to_write, int $bytes_to_read): array|false
    {
        $bus = new I2CBus($this->fd, "", $address);
        $write_bytes = is_array($bytes_to_write) ? array2bytes($bytes_to_write) : $bytes_to_write;

        $result = i2c_rdwr($bus, [
            ['flags' => 0, 'data' => $write_bytes],
            ['flags' => I2CMsgFlag::M_RD->value, 'len' => $bytes_to_read],
        ]);

        if ($result === false) {
            return false;
        }

        return bytes2array($result);

    }

    public function bulkWrite(int $address, array|string $messages): array|false
    {
        $bus = new I2CBus($this->fd, "", $address);
        $chunks = static::normalizeBulkMessages($messages);
        if (count($chunks) === 0) {
            return [];
        }

        $result = i2c_rdwr($bus, array_map(
            static fn (string $chunk): array => ['flags' => 0, 'data' => $chunk],
            $chunks,
        ));

        if ($result === false) {
            return false;
        }

        return array_map('strlen', $chunks);
    }

    public function read(int $address, int $len): array|false
    {
        $bus = new I2CBus($this->fd, "", $address);
        $bytes = i2c_read($bus, $len);

        if ($bytes === false) {
            return false;
        }

        return bytes2array($bytes);
    }

    public function write(int $address, array|string $data): int
    {
        $bus = new I2CBus($this->fd, "", $address);
        if (is_array($data)) {
            $data = array2bytes($data);
        }

        return i2c_write($bus, $data);

    }

    public function close(): void
    {
        $bus = new I2CBus($this->fd, "", 0x00);
        i2c_close($bus);
    }
}