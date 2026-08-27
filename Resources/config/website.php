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

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sulu\Bundle\ThemeBundle\EventListener\SetThemeEventListener;
use Symfony\Component\DependencyInjection\Reference;

return static function(ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sulu_theme.event_listener.set_theme', SetThemeEventListener::class)
        ->args([
            new Reference('sylius.repository.theme'),
            new Reference('sylius.theme.context.settable'),
        ])
        ->tag('kernel.event_listener', [
            'event' => 'kernel.request',
            'method' => 'setActiveThemeOnRequest',
        ]);
};
