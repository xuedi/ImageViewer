<?php

namespace ImageViewer\Commands;

use ImageViewer\Factory;
use Symfony\Component\Console\Command\Command;


abstract class FactoryCommand extends Command
{
    protected Factory $factory;

    public function __construct(Factory $factory)
    {
        parent::__construct();
        $this->factory = $factory;
    }
}