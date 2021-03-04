<?php

/*
 * This file is part of Sulu.
 *
 * (c) Sulu GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\ThemeBundle\DependencyInjection\CompilerPass;

use Sulu\Bundle\MediaBundle\DependencyInjection\AbstractImageFormatCompilerPass;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * This compiler pass loads all image formats defined in the configuration files in all the themes.
 */
class ImageFormatCompilerPass extends AbstractImageFormatCompilerPass
{
    /**
     * {@inheritdoc}
     */
    protected function getFiles(ContainerBuilder $container)
    {
        /** @var ThemeRepositoryInterface $themeRepository */
        $themeRepository = $container->get('sylius.repository.theme');

        /** @var array<class-string> $bundles */
        $bundles = $container->getParameter('kernel.bundles');

        $configPath = 'config/image-formats.xml';

        $files = [];
        foreach ($themeRepository->findAll() as $theme) {
            foreach ($bundles as $bundleName => $bundle) {
                $bundleReflection = new \ReflectionClass($bundle);
                $fileName = $bundleReflection->getFileName();

                if (!$fileName) {
                    continue;
                }

                $path = sprintf(
                    '%s/Resources/themes/%s/%s',
                    dirname($fileName),
                    $theme->getName(),
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
