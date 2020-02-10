<?php

use Phinx\Seed\AbstractSeed;

class LocationsSeed extends AbstractSeed
{
    const UNKNOWN_ID = 1;

    public function run()
    {
        $data = [
            [
                'id' => self::UNKNOWN_ID,
                'name' => 'unknown',
            ],
        ];

        $location = $this->table('locations');
        $location->insert($data)->save();
    }
}
