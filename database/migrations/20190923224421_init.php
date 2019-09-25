<?php

use Phinx\Migration\AbstractMigration;

class Init extends AbstractMigration
{
    public function change()
    {

        $user = $this->table('files', ['primary_key' => 'uuid']);
        $user->addColumn('nameHash', 'string', ['limit' => 40]);
        $user->addColumn('fileHash', 'string', ['limit' => 40]);
        $user->addColumn('fileName', 'string');
        $user->addColumn('createdAt', 'datetime', ['default' => 'CURRENT_TIMESTAMP']);
        $user->create();
    }
}
