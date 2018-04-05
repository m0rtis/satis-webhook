<?php
declare(strict_types=1);


namespace Composer\Satis\Webhook\Container;


use Psr\Container\NotFoundExceptionInterface;

final class NotFoundException extends \InvalidArgumentException implements NotFoundExceptionInterface
{

}