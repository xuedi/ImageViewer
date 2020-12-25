<?php

namespace Database\Seeds;

use Phinx\Seed\AbstractSeed;

class StatusSeed extends AbstractSeed
{
    const UNKNOWN_ID = 1;

    public function run()
    {
        $data = [
            ['id' => 1, 'name' => 'NEW'],
            ['id' => 2, 'name' => 'PARSED'],
            ['id' => 3, 'name' => 'THUMBED'],
            ['id' => 4, 'name' => 'ORPHANED'],
        ];

        $location = $this->table('status');
        $location->insert($data)->save();
    }
}
