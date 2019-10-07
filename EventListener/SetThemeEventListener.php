<?php

/*
 * This file is part of Sulu.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\ThemeBundle\EventListener;

use Liip\ThemeBundle\ActiveTheme;
use Sulu\Bundle\PreviewBundle\Preview\Events\PreRenderEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Listener which applies the configured theme.
 */
class SetThemeEventListener
{
    /**
     * @var ActiveTheme
     */
    private $activeTheme;

    /**
     * @param ActiveTheme $activeTheme
     */
    public function __construct(ActiveTheme $activeTheme)
    {
        $this->activeTheme = $activeTheme;
    }

    /**
     * Set the active theme if there is a portal.
     *
     * @param GetResponseEvent $event
     */
    public function setActiveThemeOnRequest(GetResponseEvent $event): void
    {
        if (null === ($attributes = $event->getRequest()->get('_sulu'))
            || null === ($webspace = $attributes->getAttribute('webspace'))
            || null === ($theme = $webspace->getTheme())
        ) {
            return;
        }

        $this->activeTheme->setName($theme);
    }

    /**
     * Set the active theme for a preview rendering.
     *
     * @param PreRenderEvent $event
     */
    public function setActiveThemeOnPreviewPreRender(PreRenderEvent $event): void
    {
        $this->activeTheme->setName($event->getAttribute('webspace')->getTheme());
    }
}
