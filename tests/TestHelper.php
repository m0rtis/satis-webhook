<?php
declare(strict_types=1);


namespace Composer\Satis\Webhook\Test;


final class TestHelper
{
    public static $config;

    public static function getConfig(): array
    {
        if (!static::$config) {
            static::$config = require __DIR__.'/../config/example.config.php';
        }
        return static::$config;

    }

    /**
     * @return array
     */
    public static function getGlobals(): array
    {
        return [
            'REQUEST_METHOD' => 'POST',
            'HTTPS' => 'on',
            'HTTP_HOST' => 'localhost',
            'SERVER_PORT' => 443,
            'REQUEST_URI' =>'/command/gitlab/update/SomeSecretKeyAsPartOfURI',
            'HTTP_X-Gitlab-Token' => 'someRandomLongString'
        ];
    }

    public static function getGitlabAnswer(): array
    {
        return [
            'project' => [
                'path_with_namespaces' => 'vendor\package',
                'url' => 'git@gitlab.com:m0rtis/satis-webhook'
            ]
        ];
    }
}