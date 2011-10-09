<?php

namespace Photobooth\Command;

use Symfony\Component\Console\Command\Command;

abstract class BaseCommand extends Command
{
    public function getConfig()
    {
        return $this->getApplication()->getConfig();
    }
}