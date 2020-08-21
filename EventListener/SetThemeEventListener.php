<?php

/*
 * This file is part of Sulu.
 *
 * (c) Sulu GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\ThemeBundle\EventListener;

use Sulu\Bundle\PreviewBundle\Preview\Events\PreRenderEvent;
use Sylius\Bundle\ThemeBundle\Context\SettableThemeContext;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

/**
 * Listener which applies the configured theme.
 */
class SetThemeEventListener
{
    /** @var ThemeRepositoryInterface */
    private $themeRepository;

    /** @var SettableThemeContext */
    private $themeContext;

    public function __construct(ThemeRepositoryInterface $themeRepository, SettableThemeContext $themeContext)
    {
        $this->themeRepository = $themeRepository;
        $this->themeContext = $themeContext;
    }

    /**
     * Set the active theme if there is a portal.
     */
    public function setActiveThemeOnRequest(RequestEvent $event): void
    {
        if (null === ($attributes = $event->getRequest()->get('_sulu'))
            || null === ($webspace = $attributes->getAttribute('webspace'))
            || null === ($theme = $webspace->getTheme())
        ) {
            return;
        }

        $this->themeContext->setTheme(
            $this->themeRepository->findOneByName($theme)
        );
    }

    /**
     * Set the active theme for a preview rendering.
     */
    public function setActiveThemeOnPreviewPreRender(PreRenderEvent $event): void
    {

        $this->themeContext->setTheme(
            $this->themeRepository->findOneByName($event->getAttribute('webspace')->getTheme())
        );
    }
}
