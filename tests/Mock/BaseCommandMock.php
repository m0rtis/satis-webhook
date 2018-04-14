<?php
declare(strict_types=1);


namespace Composer\Satis\Webhook\Test\Mock;


use Composer\Satis\Webhook\Command\BaseCommand;
use Psr\Http\Message\ServerRequestInterface;

final class BaseCommandMock extends BaseCommand
{
    public function __call($name, $arguments)
    {
        if (\method_exists($this, $name)) {
            return $this->$name(...$arguments);
        }
        throw new \InvalidArgumentException('Called method '.$name.' does not exist');
    }

    protected function runSatisCommands(ServerRequestInterface $request): string
    {
        return 'passed';
    }
}