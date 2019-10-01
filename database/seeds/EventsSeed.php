<?php

use Phinx\Seed\AbstractSeed;

/**
 * Class EventsSeed
 */
class EventsSeed extends AbstractSeed
{
    const UNKNOWN_ID = 1;

    public function run()
    {
        $data = [
            [
                'id' => self::UNKNOWN_ID,
                'locationId' => LocationsSeed::UNKNOWN_ID,
                'date' => '000-00-00',
                'name' => 'Unkonown',
            ],
        ];

        $events = $this->table('events');
        $events->insert($data)->save();
    }
}
