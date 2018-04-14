<?php
declare(strict_types=1);


namespace Composer\Satis\Webhook\Test\Mock;


use Composer\Satis\Webhook\Command\UpdateRepositoryCommand;
use Symfony\Component\Console\Command\Command;

final class UpdateRepositoryCommandMock extends UpdateRepositoryCommand
{
    protected function getCommand(string $commandName): Command
    {
        return new SatisCommandMock($commandName);
    }

}