<?php

namespace MvcLTE\Hashids;

use \Hashids\Hashids;

use MvcLTE\Helpers\ArrayData;

class HashidsFactory
{
    /**
     * Make hashids instance.
     * 
     * @param array $Config
     * @return \Hashids\Hashids
     */
    public function make(array $Config): Hashids
    {
        $Config = $this->getConfig($Config);

        return $this->getClient($Config);
    }

    /**
     * Get configuration for the hashids
     * 
     * @param array $Config
     * @return array
     */
    protected function getConfig(array $Config): array
    {
        return [
            'salt' => ArrayData::get($Config, 'salt', ''),
            'length' => ArrayData::get($Config, 'length', 0),
            'alphabet' => ArrayData::get($Config, 'alphabet', 
                'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'
            ),
        ];
    }

    /**
     * Create new hashids client
     * 
     * @param array $Config
     * @return \Hashids\Hashids
     */
    protected function getClient(array $Config): Hashids
    {
        return new Hashids($Config['salt'], $Config['length'], $Config['alphabet']);
    }
}
