<?php

declare(strict_types=1);

namespace TweakFlux;

use Symfony\Component\Console\Application as SymfonyApplication;
use TweakFlux\Commands\ApplyCommand;
use TweakFlux\Commands\BoostCommand;
use TweakFlux\Commands\CreateCommand;
use TweakFlux\Commands\ListCommand;

final class Application extends SymfonyApplication
{
    public function __construct()
    {
        parent::__construct('TweakFlux', '1.0.0');

        $this->add(new ListCommand());
        $this->add(new ApplyCommand());
        $this->add(new CreateCommand());
        $this->add(new BoostCommand());
    }
}
