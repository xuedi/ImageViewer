<?php

use Phinx\Migration\AbstractMigration;

class Init extends AbstractMigration
{
    public function change()
    {
        $files = $this->table('files');
        $files->addColumn('event_id', 'integer');
        $files->addColumn('nameHash', 'string', ['limit' => 40]);
        $files->addColumn('fileHash', 'string', ['limit' => 40]);
        $files->addColumn('fileName', 'string');
        $files->addColumn('width', 'integer');
        $files->addColumn('height', 'integer');
        $files->addColumn('pixel', 'integer');
        $files->addColumn('size', 'integer');
        $files->addColumn('createdAt', 'datetime', ['default' => 'CURRENT_TIMESTAMP']);
        $files->create();

        $locations = $this->table('locations');
        $locations->addColumn('name', 'string', ['limit' => 128]);
        $locations->create();

        $tags = $this->table('tags');
        $tags->addColumn('name', 'string', ['limit' => 128]);
        $tags->addColumn('tag_group_id', 'integer');
        $tags->create();

        $user = $this->table('user');
        $user->addColumn('email', 'string', ['limit' => 128]);
        $user->addColumn('password', 'string', ['limit' => 128]);
        $user->create();

        $tags = $this->table('tag_group');
        $tags->addColumn('name', 'string', ['limit' => 128]);
        $tags->create();

        $tags = $this->table('file_tags', ['id' => false]);
        $tags->addColumn('file_id', 'integer');
        $tags->addColumn('tag_id', 'integer');
        $tags->create();

        $events = $this->table('events');
        $events->addColumn('locationId', 'integer');
        $events->addColumn('date', 'string', ['limit' => 10]);
        $events->addColumn('name', 'string', ['limit' => 128]);
        $events->create();

        $events = $this->table('thumbs');
        $events->addColumn('file_id', 'integer');
        $events->addColumn('size_id', 'integer');
        $events->create();

        $events = $this->table('thumb_size');
        $events->addColumn('size', 'integer');
        $events->create();
    }
}
