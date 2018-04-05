<?php
declare(strict_types=1);


namespace Composer\Satis\Webhook\Config;


final class RoutesValidator extends BaseConfigValidator
{
    private $reqiredGroupKeys = [
        'pattern' => BaseConfigValidator::STRING,
        'routes' => BaseConfigValidator::ARRAY
    ];
    private $reqiredRouteKeys = [
        'pattern' => BaseConfigValidator::STRING,
        'type' => BaseConfigValidator::STRING,
        'methods' => BaseConfigValidator::ARRAY,
        'handler' => BaseConfigValidator::STRING
    ];

    /**
     * @param iterable $config
     * @return iterable
     * @throws \InvalidArgumentException
     * @throws \ReflectionException
     */
    protected function validate(iterable $config): iterable
    {
        foreach ($config as $name => $item) {
            $this->check($item, ['type' => BaseConfigValidator::STRING]);
            if ('group' === $item['type']) {
                $this->check($item, $this->reqiredGroupKeys);
                foreach ($item['routes'] as $route) {
                    $this->check($route, $this->reqiredRouteKeys);
                }
            } else {
                $this->check($item, $this->reqiredRouteKeys);
            }
        }
        return $config;
    }
}