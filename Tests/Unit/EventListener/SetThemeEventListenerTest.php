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

namespace Sulu\Bundle\ThemeBundle\Tests\Unit\EventListener;

use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Sulu\Bundle\PreviewBundle\Preview\Events\PreRenderEvent;
use Sulu\Bundle\ThemeBundle\EventListener\SetThemeEventListener;
use Sulu\Component\Webspace\Analyzer\Attributes\RequestAttributes;
use Sulu\Component\Webspace\Webspace;
use Sylius\Bundle\ThemeBundle\Context\SettableThemeContext;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class SetThemeEventListenerTest extends TestCase
{
    /**
     * @var ThemeRepositoryInterface|ObjectProphecy
     */
    private $themeRepository;

    /**
     * @var SettableThemeContext
     */
    private $themeContext;

    /**
     * @var SetThemeEventListener
     */
    private $listener;

    protected function setUp(): void
    {
        $this->themeRepository = $this->prophesize(ThemeRepositoryInterface::class);
        $this->themeContext = new SettableThemeContext();

        $this->listener = new SetThemeEventListener($this->themeRepository->reveal(), $this->themeContext);
    }

    public function testEventListener(): void
    {
        /** @var Webspace|ObjectProphecy webspace */
        $webspace = $this->prophesize(Webspace::class);
        $webspace->getTheme()->willReturn('theme/name');

        /** @var ThemeInterface|ObjectProphecy $theme */
        $theme = $this->prophesize(ThemeInterface::class);

        /** @var Request|ObjectProphecy $request */
        $request = $this->prophesize(Request::class);
        /** @var RequestAttributes|ObjectProphecy $attributes */
        $attributes = $this->prophesize(RequestAttributes::class);
        $attributes->getAttribute('webspace')->willReturn($webspace->reveal());
        $request->get('_sulu')->willReturn($attributes->reveal());

        /** @var RequestEvent|ObjectProphecy $event */
        $event = $this->prophesize(RequestEvent::class);
        $event->getRequest()->willReturn($request->reveal());
        $event->isMasterRequest()->willReturn(true);

        $this->themeRepository->findOneByName('theme/name')
            ->shouldBeCalled()
            ->willReturn($theme->reveal());

        $this->listener->setActiveThemeOnRequest($event->reveal());

        $this->assertSame($theme->reveal(), $this->themeContext->getTheme());
    }

    public function testEventListenerNotMaster(): void
    {
        /** @var Webspace|ObjectProphecy webspace */
        $webspace = $this->prophesize(Webspace::class);
        $webspace->getTheme()->willReturn('theme/name');

        /** @var ThemeInterface|ObjectProphecy $theme */
        $theme = $this->prophesize(ThemeInterface::class);

        /** @var Request|ObjectProphecy $request */
        $request = $this->prophesize(Request::class);
        /** @var RequestAttributes|ObjectProphecy $attributes */
        $attributes = $this->prophesize(RequestAttributes::class);
        $attributes->getAttribute('webspace')->willReturn($webspace->reveal());
        $request->get('_sulu')->willReturn($attributes->reveal());

        /** @var RequestEvent|ObjectProphecy $event */
        $event = $this->prophesize(RequestEvent::class);
        $event->getRequest()->willReturn($request->reveal());
        $event->isMasterRequest()->willReturn(false);

        $this->themeRepository->findOneByName('theme/name')
            ->shouldBeCalled()
            ->willReturn($theme->reveal());

        $this->listener->setActiveThemeOnRequest($event->reveal());

        $this->assertSame($theme->reveal(), $this->themeContext->getTheme());
    }

    public function testEventListenerNoWebspace(): void
    {
        /** @var Request|ObjectProphecy $request */
        $request = $this->prophesize(Request::class);
        /** @var RequestAttributes|ObjectProphecy $attributes */
        $attributes = $this->prophesize(RequestAttributes::class);
        $attributes->getAttribute('webspace')->willReturn(null);
        $request->get('_sulu')->willReturn($attributes->reveal());

        /** @var RequestEvent|ObjectProphecy $event */
        $event = $this->prophesize(RequestEvent::class);
        $event->getRequest()->willReturn($request->reveal());
        $event->isMasterRequest()->willReturn(true);

        $this->themeRepository->findOneByName('theme/name')
            ->shouldNotBeCalled();

        $this->listener->setActiveThemeOnRequest($event->reveal());

        $this->assertNull($this->themeContext->getTheme());
    }

    public function testEventListenerNoAttributes(): void
    {
        /** @var Request|ObjectProphecy $request */
        $request = $this->prophesize(Request::class);
        /* @var RequestAttributes|ObjectProphecy $attributes */
        $request->get('_sulu')->willReturn(null);

        /** @var RequestEvent|ObjectProphecy $event */
        $event = $this->prophesize(RequestEvent::class);
        $event->getRequest()->willReturn($request->reveal());
        $event->isMasterRequest()->willReturn(true);

        $this->themeRepository->findOneByName('theme/name')
            ->shouldNotBeCalled();

        $this->listener->setActiveThemeOnRequest($event->reveal());

        $this->assertNull($this->themeContext->getTheme());
    }

    public function testEventListenerOnPreview(): void
    {
        /** @var ThemeInterface|ObjectProphecy $theme */
        $theme = $this->prophesize(ThemeInterface::class);

        /** @var Webspace|ObjectProphecy webspace */
        $webspace = $this->prophesize(Webspace::class);
        $webspace->getTheme()->willReturn('theme/name');

        /** @var RequestAttributes|ObjectProphecy $attributes */
        $attributes = $this->prophesize(RequestAttributes::class);
        $attributes->getAttribute('webspace', null)->willReturn($webspace->reveal());

        $this->themeRepository->findOneByName('theme/name')
            ->shouldBeCalled()
            ->willReturn($theme->reveal());

        $this->listener->setActiveThemeOnPreviewPreRender(
            new PreRenderEvent($attributes->reveal())
        );

        $this->assertSame($theme->reveal(), $this->themeContext->getTheme());
    }

    public function testEventListenerOnPreviewNoTheme(): void
    {
        /** @var Webspace|ObjectProphecy webspace */
        $webspace = $this->prophesize(Webspace::class);
        $webspace->getTheme()->willReturn(null);

        /** @var RequestAttributes|ObjectProphecy $attributes */
        $attributes = $this->prophesize(RequestAttributes::class);
        $attributes->getAttribute('webspace', null)->willReturn($webspace->reveal());

        $this->themeRepository->findOneByName('theme/name')
            ->shouldNotBeCalled();

        $this->listener->setActiveThemeOnPreviewPreRender(
            new PreRenderEvent($attributes->reveal())
        );

        $this->assertNull($this->themeContext->getTheme());
    }
}
