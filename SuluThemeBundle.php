<?php

/*
 * This file is part of Sulu.
 *
 * (c) Sulu GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\ThemeBundle;

use Sulu\Bundle\ThemeBundle\DependencyInjection\CompilerPass\ImageFormatCompilerPass;
use Sulu\Bundle\ThemeBundle\DependencyInjection\CompilerPass\WebspaceStructureProviderCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SuluThemeBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new WebspaceStructureProviderCompilerPass());
        $container->addCompilerPass(new ImageFormatCompilerPass());
    }
}
