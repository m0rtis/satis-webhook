<?php
declare(strict_types=1);


namespace Composer\Satis\Webhook\Test\Command;


use Composer\Satis\Console\Application;
use Composer\Satis\Console\Command\BuildCommand;
use Composer\Satis\Webhook\Test\Mock\BaseCommandMock;
use Composer\Satis\Webhook\Test\TestHelper;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Symfony\Component\Console\Command\Command;

final class BaseCommandTest extends TestCase
{
    private $baseCommandMock;

    protected function setUp()/* The :void return type declaration that should be here would cause a BC issue */
    {
        $this->baseCommandMock = new BaseCommandMock([
                'satis_config' => '',
                'output_dir' => ''
            ]
        );
    }

    public function testGetCommand(): void
    {
        /** @var Command $command */
        $command = $this->baseCommandMock->getCommand('build');

        $this->assertInstanceOf(BuildCommand::class, $command);
        $this->assertInstanceOf(Application::class, $command->getApplication());
    }

    public function testHandleUnauthorizedException(): void
    {
        $globals = TestHelper::getGlobals();
        $globals['HTTP_X-Gitlab-Token'] = 'Invalid Token';
        $request = Request::createFromGlobals($globals)->withParsedBody(TestHelper::getGitlabAnswer());
        $baseCommandMock = new BaseCommandMock([
            'satis_config' => '',
            'output_dir' => '',
            'secret' => 'super-secret'
        ]);
        /** @var ResponseInterface $response */
        $response = $baseCommandMock->__invoke($request, new Response(), ['provider' => 'gitlab']);

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testHandleAnyOtherExceptions(): void
    {
        $request = Request::createFromGlobals(TestHelper::getGlobals());
        /** @var ResponseInterface $response */
        $response = $this->baseCommandMock->__invoke($request, new Response(), ['provider' => 'gitlab']);

        $this->assertEquals(500, $response->getStatusCode());
    }
}