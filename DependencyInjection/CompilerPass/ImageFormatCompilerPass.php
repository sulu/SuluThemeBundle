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

use Sulu\Bundle\MediaBundle\Media\FormatLoader\XmlFormatLoader10;
use Sulu\Bundle\MediaBundle\Media\FormatLoader\XmlFormatLoader11;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class ImageFormatCompilerPass.
 */
class ImageFormatCompilerPass implements CompilerPassInterface
{
    /**
     * @var ContainerBuilder
     */
    protected $container;

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $this->container = $container;

        $formats = $this->loadThemeFormats(
            $container->getParameter('sulu_media.format_manager.default_imagine_options')
        );
        if ($container->hasParameter('sulu_media.image.formats')) {
            $formats = array_merge($container->getParameter('sulu_media.image.formats'), $formats);
        }

        $container->setParameter('sulu_media.image.formats', $formats);
    }

    /**
     * @param array $globalOptions
     *
     * @return array
     */
    private function loadThemeFormats($globalOptions)
    {
        $activeFormats = [];
        $activeTheme = $this->container->get('liip_theme.active_theme');
        $bundles = $this->container->getParameter('kernel.bundles');
        $configPaths = $this->container->getParameter('sulu_media.format_manager.config_paths');
        $defaultConfigPath = 'config/image-formats.xml';

        foreach ($activeTheme->getThemes() as $theme) {
            foreach ($bundles as $bundleName => $bundle) {
                $reflector = new \ReflectionClass($bundle);
                $configPath = $defaultConfigPath;
                if (isset($configPaths[$theme])) {
                    $configPath = $configPaths[$theme];
                }
                $fullPath = sprintf(
                    '%s/Resources/themes/%s/%s',
                    dirname($reflector->getFileName()),
                    $theme,
                    $configPath
                );

                if (file_exists($fullPath)) {
                    $this->setFormatsFromFile($fullPath, $activeFormats, $globalOptions);
                }
            }
        }

        return $activeFormats;
    }

    /**
     * @param $fullPath
     * @param $activeFormats
     * @param $globalOptions
     */
    private function setFormatsFromFile($fullPath, &$activeFormats, $globalOptions)
    {
        $folder = dirname($fullPath);
        $fileName = basename($fullPath);

        $locator = new FileLocator($folder);

        $xmlLoader10 = new XmlFormatLoader10($locator);
        $xmlLoader11 = new XmlFormatLoader11($locator);
        $xmlLoader10->setGlobalOptions($globalOptions);
        $xmlLoader11->setGlobalOptions($globalOptions);

        $resolver = new LoaderResolver([$xmlLoader10, $xmlLoader11]);
        $loader = new DelegatingLoader($resolver);

        $themeFormats = $loader->load($fileName);
        foreach ($themeFormats as $format) {
            $activeFormats[$format['key']] = $format;
        }
    }
}
