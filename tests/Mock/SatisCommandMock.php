<?php
declare(strict_types=1);


namespace Composer\Satis\Webhook\Test\Mock;


use Composer\Command\BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SatisCommandMock
 * @package Composer\Satis\Webhook\Test
 */
final class SatisCommandMock extends BaseCommand
{
    /**
     * @var null|string
     */
    private $name;

    /**
     * SatisCommandMock constructor.
     * @param null|string $name
     */
    public function __construct(?string $name = null)
    {
        if ($name) {
            $this->name = $name;
        }
        parent::__construct($name);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = ucfirst($this->name);
        $output->writeln("$name command executed successfully");
        return 0;
    }

    protected function configure(): void
    {
        $definitions = [];
        switch ($this->name) {
            case 'add':
                $definitions = [
                    new InputArgument('url'),
                    new InputArgument('file')
                ];
                break;

            case 'build':
                $definitions = [
                    new InputArgument('file'),
                    new InputArgument('output-dir'),
                    new InputArgument('packages', InputArgument::IS_ARRAY | InputArgument::OPTIONAL)
                ];
                break;
        }
        $this->setDefinition($definitions);
    }


}