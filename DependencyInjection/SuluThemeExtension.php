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

namespace Sulu\Bundle\ThemeBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @internal this class is not part of the public API and should only be called by the Symfony framework classes
 *
 * @final
 */
class SuluThemeExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        /** @var string $context */
        $context = $container->getParameter('sulu.context');

        $loader->load(\sprintf('%s.xml', $context));
    }
}
