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
use Symfony\Component\Security\Bundle\SecurityBundle;

class Kernel extends SuluTestKernel
{
    public function __construct($environment, $debug, $suluContext = self::CONTEXT_ADMIN)
    {
        parent::__construct($environment, $debug, $suluContext);
    }

    public function registerBundles(): iterable
    {
        $bundles = [
            new SyliusThemeBundle(),
            new SuluThemeBundle(),
        ];

        // Register SecurityBundle for website context (already registered for admin in parent)
        if (self::CONTEXT_WEBSITE === $this->getContext()) {
            $bundles[] = new SecurityBundle();
        }

        return \array_merge(
            parent::registerBundles(),
            $bundles
        );

        return \array_merge(
            parent::registerBundles(),
        );
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        parent::registerContainerConfiguration($loader);

        $loader->load(__DIR__ . '/config/config_' . $this->getContext() . '.yaml');

        $bundles = $this->registerBundles();
        $hasMassiveSearchBundle = false;
        foreach ($bundles as $bundle) {
            if ($bundle instanceof \Massive\Bundle\SearchBundle\MassiveSearchBundle) {
                $hasMassiveSearchBundle = true;
                break;
            }
        }

        if ($hasMassiveSearchBundle) {
            $loader->load(__DIR__ . '/config/config_massive_search.yaml');
        }
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
