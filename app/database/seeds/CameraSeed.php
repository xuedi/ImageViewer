<?php

use Phinx\Seed\AbstractSeed;

class CameraSeed extends AbstractSeed
{
    public function run()
    {
        $data = [
            [
                'id' => 1,
                'ident' => 'ed90a62d55a1834c9785b8e0f78785f4',
                'model' => 'unknown',
                'manufacturer' => 'unknown',
            ],
        ];

        $location = $this->table('camera');
        $location->insert($data)->save();
    }
}
