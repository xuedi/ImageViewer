<?php

namespace Database\Seeds;

use Phinx\Seed\AbstractSeed;

class EventsSeed extends AbstractSeed
{
    const UNKNOWN_ID = 1;
    const GERMAN_PICNIC_ID = 2;
    const CHINESE_PICNIC_ID = 3;
    const GERMAN_PARTY_ID = 4;
    const CHINESE_PARTY_ID = 5;
    const GERMAN_BBQ_ID = 6;

    public function run()
    {
        $data = [
            [
                'id' => self::UNKNOWN_ID,
                'locationId' => LocationsSeed::UNKNOWN_ID,
                'date' => '0000-00-00',
                'name' => 'unknown',
            ],
            [
                'id' => self::GERMAN_PICNIC_ID,
                'locationId' => LocationsSeed::GERMANY_ID,
                'date' => '2020-04-04',
                'name' => 'Picnic in Germany',
            ],
            [
                'id' => self::CHINESE_PICNIC_ID,
                'locationId' => LocationsSeed::CHINA_ID,
                'date' => '2020-05-05',
                'name' => 'Picnic in China',
            ],
            [
                'id' => self::GERMAN_PARTY_ID,
                'locationId' => LocationsSeed::GERMANY_ID,
                'date' => '2020-12-22',
                'name' => 'Party in Germany',
            ],
            [
                'id' => self::CHINESE_PARTY_ID,
                'locationId' => LocationsSeed::CHINA_ID,
                'date' => '2020-02-14',
                'name' => 'Party in China',
            ],
            [
                'id' => self::GERMAN_BBQ_ID,
                'locationId' => LocationsSeed::GERMANY_ID,
                'date' => '2020-06-01',
                'name' => 'Picnic in Germany',
            ],
        ];

        $events = $this->table('events');
        $events->insert($data)->save();
    }
}
