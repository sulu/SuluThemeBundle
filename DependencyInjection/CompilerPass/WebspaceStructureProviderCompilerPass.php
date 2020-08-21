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

use Sulu\Bundle\ThemeBundle\StructureProvider\WebspaceStructureProvider;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class WebspaceStructureProviderCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('sulu.content.webspace_structure_provider')) {
            return;
        }

        $definition = $container->getDefinition('sulu.content.webspace_structure_provider');
        $definition->setClass(WebspaceStructureProvider::class);
        $definition->addArgument(new Reference('sulu_core.webspace.webspace_manager'));
        $definition->addArgument(new Reference('sylius.repository.theme'));
        $definition->addArgument(new Reference('sylius.theme.context.settable'));
    }
}
