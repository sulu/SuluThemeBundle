<?php

/*
 * This file is part of Sulu.
 *
 * (c) Sulu GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\ThemeBundle\Tests\Application;

use Liip\ThemeBundle\LiipThemeBundle;
use Sulu\Bundle\TestBundle\Kernel\SuluTestKernel;
use Sulu\Bundle\ThemeBundle\SuluThemeBundle;
use Symfony\Component\Config\Loader\LoaderInterface;

class Kernel extends SuluTestKernel
{
    /**
     * {@inheritdoc}
     */
    public function __construct($environment, $debug, $suluContext = self::CONTEXT_ADMIN)
    {
        parent::__construct($environment, $debug, $suluContext);
    }

    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        return array_merge(
            parent::registerBundles(),
            [
                new LiipThemeBundle(),
                new SuluThemeBundle(),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        parent::registerContainerConfiguration($loader);

        $loader->load(__DIR__ . '/config/config_' . $this->getContext() . '.yaml');
    }
}
