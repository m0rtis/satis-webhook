<?php
declare(strict_types=1);


namespace Composer\Satis\Webhook\Command;


use Composer\Json\JsonFile;
use Composer\Satis\Console\Command\AddCommand;
use Composer\Satis\Console\Command\BuildCommand;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Console\Exception\CommandNotFoundException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class UpdateRepositoryCommand extends BaseCommand
{
    /**
     * @param ServerRequestInterface $request
     * @return string
     * @throws \RuntimeException
     * @throws CommandNotFoundException
     * @throws \InvalidArgumentException
     * @throws \ReflectionException
     * @throws \Exception
     */
    protected function runSatisCommands(ServerRequestInterface $request): string
    {
        $output = new BufferedOutput();
        $packageName = $request->getAttribute('package_name');
        $packageUrl = $request->getAttribute('package_url');
        $satisJson = $this->config['satis_config'];

        if ($this->packageDoesNotExists($packageUrl)) {
            $inputAdd = new ArrayInput([
                'file' => $satisJson,
                'url' => $packageUrl
            ]);
            /** @var AddCommand $add */
            $add = $this->getCommand('add');
            $add->run($inputAdd, $output);
        }
        $inputBuild = new ArrayInput([
            'file' => $satisJson,
            'output-dir' => $this->config['output_dir'],
            'packages' => [$packageName]
        ]);
        /** @var BuildCommand $build */
        $build = $this->getCommand('build');
        $build->run($inputBuild, $output);

        return $output->fetch();
    }

    /**
     * @param string $packageUrl
     * @return bool
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    private function packageDoesNotExists(string $packageUrl): bool
    {
        $file = new JsonFile($this->config['satis_config']);
        $config = $file->read();
        if (isset($config['repositories']) && \is_array($config['repositories'])) {
            foreach ($config['repositories'] as $repository) {
                if ($repository['url'] === $packageUrl) {
                    return false;
                }
            }
        }
        return true;
    }
}