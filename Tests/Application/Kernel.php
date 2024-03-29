<?php

declare(strict_types=1);

/*
 * This file is part of Sulu.
 *
 * (c) Sulu GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\ThemeBundle\Tests\Application;

use Sulu\Bundle\TestBundle\Kernel\SuluTestKernel;
use Sulu\Bundle\ThemeBundle\SuluThemeBundle;
use Sylius\Bundle\ThemeBundle\SyliusThemeBundle;
use Symfony\Component\Config\Loader\LoaderInterface;

class Kernel extends SuluTestKernel
{
    public function __construct($environment, $debug, $suluContext = self::CONTEXT_ADMIN)
    {
        parent::__construct($environment, $debug, $suluContext);
    }

    public function registerBundles(): iterable
    {
        return \array_merge(
            parent::registerBundles(),
            [
                new SyliusThemeBundle(),
                new SuluThemeBundle(),
            ]
        );
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        parent::registerContainerConfiguration($loader);

        $loader->load(__DIR__ . '/config/config_' . $this->getContext() . '.yaml');
    }

    protected function getKernelParameters(): array
    {
        $parameters = parent::getKernelParameters();

        $reflection = new \ReflectionClass(\Gedmo\Exception::class);
        $gedmoDirectory = \dirname($reflection->getFileName());

        $parameters['gedmo_directory'] = $gedmoDirectory;

        return $parameters;
    }
}
