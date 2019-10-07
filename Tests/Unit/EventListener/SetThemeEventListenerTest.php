<?php

/*
 * This file is part of Sulu.
 *
 * (c) Sulu GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\ThemeBundle\Tests\Unit\EventListener;

use Liip\ThemeBundle\ActiveTheme;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Sulu\Bundle\PreviewBundle\Preview\Events\PreRenderEvent;
use Sulu\Bundle\ThemeBundle\EventListener\SetThemeEventListener;
use Sulu\Component\Webspace\Analyzer\Attributes\RequestAttributes;
use Sulu\Component\Webspace\Webspace;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class SetThemeEventListenerTest extends TestCase
{
    /**
     * @var ObjectProphecy>
     */
    private $activeTheme;

    /**
     * @var string
     */
    private $theme = 'test';

    /**
     * @var ObjectProphecy>
     */
    private $webspace;

    /**
     * @var SetThemeEventListener
     */
    private $listener;

    public function setUp(): void
    {
        $this->activeTheme = $this->prophesize(ActiveTheme::class);
        $this->webspace = $this->prophesize(Webspace::class);
        $this->webspace->getTheme()->willReturn($this->theme);

        $this->listener = new SetThemeEventListener($this->activeTheme->reveal());
    }

    public function testEventListener(): void
    {
        $request = $this->prophesize(Request::class);
        $attributes = $this->prophesize(RequestAttributes::class);
        $attributes->getAttribute('webspace')->willReturn($this->webspace->reveal());
        $request->get('_sulu')->willReturn($attributes->reveal());

        $event = $this->prophesize(GetResponseEvent::class);
        $event->getRequest()->willReturn($request->reveal());
        $event->isMasterRequest()->willReturn(true);

        $this->activeTheme->setName($this->theme)->shouldBeCalled();

        $this->listener->setActiveThemeOnRequest($event->reveal());
    }

    public function testEventListenerNotMaster(): void
    {
        $request = $this->prophesize(Request::class);
        $attributes = $this->prophesize(RequestAttributes::class);
        $attributes->getAttribute('webspace')->willReturn($this->webspace->reveal());
        $request->get('_sulu')->willReturn($attributes->reveal());

        $event = $this->prophesize(GetResponseEvent::class);
        $event->getRequest()->willReturn($request->reveal());
        $event->isMasterRequest()->willReturn(false);

        $this->activeTheme->setName($this->theme)->shouldBeCalled();

        $this->listener->setActiveThemeOnRequest($event->reveal());
    }

    public function testEventListenerNoWebspace(): void
    {
        $request = $this->prophesize(Request::class);
        $attributes = $this->prophesize(RequestAttributes::class);
        $attributes->getAttribute('webspace')->willReturn(null);
        $request->get('_sulu')->willReturn($attributes->reveal());

        $event = $this->prophesize(GetResponseEvent::class);
        $event->getRequest()->willReturn($request->reveal());
        $event->isMasterRequest()->willReturn(true);

        $this->activeTheme->setName($this->theme)->shouldNotBeCalled();

        $this->listener->setActiveThemeOnRequest($event->reveal());
    }

    public function testEventListenerNoAttributes(): void
    {
        $request = $this->prophesize(Request::class);
        $request->get('_sulu')->willReturn(null);

        $event = $this->prophesize(GetResponseEvent::class);
        $event->getRequest()->willReturn($request->reveal());
        $event->isMasterRequest()->willReturn(true);

        $this->activeTheme->setName($this->theme)->shouldNotBeCalled();

        $this->listener->setActiveThemeOnRequest($event->reveal());
    }

    public function testEventListenerOnPreview(): void
    {
        $attributes = $this->prophesize(RequestAttributes::class);
        $attributes->getAttribute('webspace', null)->willReturn($this->webspace->reveal());

        $this->activeTheme->setName($this->theme)->shouldBeCalled();

        $this->listener->setActiveThemeOnPreviewPreRender(
            new PreRenderEvent($attributes->reveal())
        );
    }
}
