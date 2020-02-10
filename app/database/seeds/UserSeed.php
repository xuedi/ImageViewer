<?php

use Phinx\Seed\AbstractSeed;

class UserSeed extends AbstractSeed
{
    public function run()
    {
        $data = [
            [
                'id' => 1,
                'email' => 'admin@host.local',
                'password' => '123456',
            ],
        ];

        $thumbSite = $this->table('user');
        $thumbSite->insert($data)->save();
    }
}
