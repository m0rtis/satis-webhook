<?php
declare(strict_types=1);


namespace Composer\Satis\Webhook\Config;


final class CommandValidator extends BaseConfigValidator
{
    private $requiredKeys = [
        'uri_key' => BaseConfigValidator::STRING,
        'satis_config' => BaseConfigValidator::STRING,
        'output_dir' => BaseConfigValidator::STRING
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