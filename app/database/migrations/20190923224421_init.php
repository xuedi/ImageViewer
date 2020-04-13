<?php

use Phinx\Migration\AbstractMigration;

class Init extends AbstractMigration
{
    public function change()
    {
        $files = $this->table('files');
        $files->addColumn('event_id', 'integer');
        $files->addColumn('status_id', 'integer');
        $files->addColumn('camera_id', 'integer');
        $files->addColumn('fileName', 'string');
        $files->addColumn('fileHash', 'string', ['limit' => 40]);
        $files->addColumn('fileType', 'string', ['null' => true, 'limit' => 40]);
        $files->addColumn('fileSize', 'integer', ['null' => true]);
        $files->addColumn('width', 'integer', ['null' => true]);
        $files->addColumn('height', 'integer', ['null' => true]);
        $files->addColumn('pixel', 'integer', ['null' => true]);
        $files->addColumn('iso', 'integer', ['null' => true]);
        $files->addColumn('exposure', 'string', ['null' => true, 'limit' => 40]);
        $files->addColumn('aperture', 'string', ['null' => true, 'limit' => 40]);
        $files->addColumn('createdAt', 'datetime', ['default' => 'CURRENT_TIMESTAMP']);
        $files->create();

        $files = $this->table('camera');
        $files->addColumn('ident', 'string');
        $files->addColumn('model', 'string', ['null' => true]);
        $files->addColumn('manufacturer', 'string', ['null' => true]);
        $files->create();

        $files = $this->table('status');
        $files->addColumn('name', 'string', ['limit' => 10]);
        $files->create();

        $locations = $this->table('locations');
        $locations->addColumn('name', 'string', ['limit' => 128]);
        $locations->create();

        $tags = $this->table('tags');
        $tags->addColumn('name', 'string', ['limit' => 128]);
        $tags->create();

        $user = $this->table('user');
        $user->addColumn('email', 'string', ['limit' => 128]);
        $user->addColumn('password', 'string', ['limit' => 128]);
        $user->create();

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
