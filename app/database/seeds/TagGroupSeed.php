<?php

use Phinx\Seed\AbstractSeed;

/**
 * Class EventsSeed
 */
class TagGroupSeed extends AbstractSeed
{
    public function run()
    {
        $data = [
            [
                'id' => 1,
                'name' => 'unknown',
            ],
            [
                'id' => 2,
                'name' => 'country',
            ],
            [
                'id' => 3,
                'name' => 'city',
            ],
            [
                'id' => 4,
                'name' => 'people',
            ],
            [
                'id' => 5,
                'name' => 'madeBy',
            ],
            [
                'id' => 6,
                'name' => 'misc',
            ],
            [
                'id' => 7,
                'name' => 'year',
            ],
            [
                'id' => 8,
                'name' => 'event',
            ],
        ];

        $tagGroup = $this->table('tag_group');
        $tagGroup->insert($data)->save();
    }
}
