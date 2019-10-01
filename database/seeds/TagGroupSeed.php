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
                'name' => 'country',
            ],
            [
                'id' => 2,
                'name' => 'city',
            ],
            [
                'id' => 3,
                'name' => 'people',
            ],
            [
                'id' => 4,
                'name' => 'madeBy',
            ],
            [
                'id' => 5,
                'name' => 'misc',
            ],
            [
                'id' => 6,
                'name' => 'year',
            ],
        ];

        $tagGroup = $this->table('tag_group');
        $tagGroup->insert($data)->save();
    }
}
