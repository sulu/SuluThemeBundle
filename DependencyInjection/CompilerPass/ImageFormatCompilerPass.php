<?php

/*
 * This file is part of Sulu.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\ThemeBundle\DependencyInjection\CompilerPass;

use Sulu\Bundle\MediaBundle\DependencyInjection\AbstractImageFormatCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class ImageFormatCompilerPass.
 */
class ImageFormatCompilerPass extends AbstractImageFormatCompilerPass
{
    /**
     * {@inheritdoc}
     */
    protected function getFiles(ContainerBuilder $container)
    {
        $files = [];

        $activeTheme = $container->get('liip_theme.active_theme');
        $bundles = $container->getParameter('kernel.bundles');
        $configPath = 'config/image-formats.xml';

        foreach ($activeTheme->getThemes() as $theme) {
            foreach ($bundles as $bundleName => $bundle) {
                $bundleReflection = new \ReflectionClass($bundle);
                $path = sprintf(
                    '%s/Resources/themes/%s/%s',
                    dirname($bundleReflection->getFileName()),
                    $theme,
                    $configPath
                );

                if (file_exists($path)) {
                    $files[] = $path;
                }
            }
        }

        return $files;
    }
}
