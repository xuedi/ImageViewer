<?php

use Phinx\Seed\AbstractSeed;

/**
 * Class SizeSeed
 */
class SizeSeed extends AbstractSeed
{
    public function run()
    {
        $data = [
            [
                'id' => 1,
                'size' => 200,
            ],
            [
                'id' => 2,
                'size' => 400,
            ],
            [
                'id' => 3,
                'size' => 1000,
            ],
        ];

        $thumbSite = $this->table('thumb_size');
        $thumbSite->insert($data)->save();
    }
}
