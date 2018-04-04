<?php
declare(strict_types=1);


namespace Composer\Satis\Webhook\Config;


final class AuthValidator extends BaseConfigValidator
{
    private $requiredKeys = [
        'secret' => BaseConfigValidator::STRING,
        'uri_key' => BaseConfigValidator::STRING,
    ];
    /**
     * @param iterable $config
     * @return iterable
     */
    protected function validate(iterable $config): iterable
    {
        $this->check($config, $this->requiredKeys);
        return $config;
    }
}