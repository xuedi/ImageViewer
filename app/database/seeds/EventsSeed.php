<?php

use Phinx\Seed\AbstractSeed;

class EventsSeed extends AbstractSeed
{
    const UNKNOWN_ID = 1;

    public function run()
    {
        $data = [
            [
                'id' => self::UNKNOWN_ID,
                'locationId' => LocationsSeed::UNKNOWN_ID,
                'date' => '0000-00-00',
                'name' => 'unknown',
            ],
        ];

        $events = $this->table('events');
        $events->insert($data)->save();
    }
}
