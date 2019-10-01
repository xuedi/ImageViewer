<?php

use Phinx\Seed\AbstractSeed;

/**
 * Class LocationsSeed
 */
class LocationsSeed extends AbstractSeed
{
    const UNKNOWN_ID = 1;

    public function run()
    {
        $data = [
            [
                'id' => self::UNKNOWN_ID,
                'name' => 'Unknown',
            ],
        ];

        $location = $this->table('locations');
        $location->insert($data)->save();
    }
}
