<?php declare(strict_types=1);

namespace ImageViewer\Configuration;

// TODO: move to database via phinx seed
class TagGroupConfig
{
    private array $groups = [];

    public function __construct(array $data)
    {
        foreach ($data as $group => $values) {
            $this->groups[(string)$group] = explode(',', (string)$values);
        }
    }

    public function getGroup(): array
    {
        return $this->groups;
    }
}
