<?php

namespace Database\Seeds;

use Phinx\Seed\AbstractSeed;

class LocationsSeed extends AbstractSeed
{
    const UNKNOWN_ID = 1;
    const CHINA_ID = 2;
    const GERMANY_ID = 3;

    public function run()
    {
        $data = [
            [
                'id' => self::UNKNOWN_ID,
                'name' => 'unknown',
            ],
            [
                'id' => self::CHINA_ID,
                'name' => 'China',
            ],
            [
                'id' => self::GERMANY_ID,
                'name' => 'Germany',
            ],
        ];

        $location = $this->table('locations');
        $location->insert($data)->save();
    }
}
