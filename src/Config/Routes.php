<?php
declare(strict_types=1);


namespace Composer\Satis\Webhook\Config;


final class Routes extends BaseConfig
{
    private $reqiredGroupKeys = [
        'pattern' => BaseConfig::STRING,
        'routes' => BaseConfig::ARRAY
    ];
    private $reqiredRouteKeys = [
        'pattern' => BaseConfig::STRING,
        'type' => BaseConfig::STRING,
        'methods' => BaseConfig::ARRAY,
        'handler' => BaseConfig::STRING
    ];

    /**
     * @param iterable $config
     * @return iterable
     * @throws \InvalidArgumentException
     */
    protected function validate(iterable $config): iterable
    {
        foreach ($config as $name => $item) {
            $this->check($item, ['type' => BaseConfig::STRING]);
            if ('group' === $item['type']) {
                $this->check($item, $this->reqiredGroupKeys);
                foreach ($item['routes'] as $route) {
                    $this->check($route, $this->reqiredRouteKeys);
                }
            }
        }
        return $config;
    }
}