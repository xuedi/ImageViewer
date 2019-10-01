<?php declare(strict_types=1);

namespace ImageViewer\Configuration;

class TagGroupConfig
{
    /** @var array */
    private $groups = [];

    public function __construct(array $data)
    {
        foreach ($data as $group => $values) {
            $this->groups[$group] = explode(',', $values);
        }
    }

    public function getGroup(): array
    {
        return $this->groups;
    }
}
