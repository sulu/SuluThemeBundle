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
use Prophecy\Argument;
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
use Symfony\Component\HttpKernel\HttpKernelInterface;

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
        $webspace = new Webspace();
        $webspace->setTheme('theme/name');

        /** @var ThemeInterface|ObjectProphecy $theme */
        $theme = $this->prophesize(ThemeInterface::class);

        $request = new Request();
        $requestAttributes = new RequestAttributes([
            'webspace' => $webspace,
        ]);
        $request->attributes->set('_sulu', $requestAttributes);

        $event = $this->createRequestEvent($request);

        $this->themeRepository->findOneByName('theme/name')
            ->shouldBeCalled()
            ->willReturn($theme->reveal());

        $this->listener->setActiveThemeOnRequest($event);

        $this->assertSame($theme->reveal(), $this->themeContext->getTheme());
    }

    public function testEventListenerNotMaster(): void
    {
        $webspace = new Webspace();
        $webspace->setTheme('theme/name');

        /** @var ThemeInterface|ObjectProphecy $theme */
        $theme = $this->prophesize(ThemeInterface::class);

        $request = new Request();
        $requestAttributes = new RequestAttributes([
            'webspace' => $webspace,
        ]);
        $request->attributes->set('_sulu', $requestAttributes);

        $event = $this->createRequestEvent($request);

        $this->themeRepository->findOneByName('theme/name')
            ->shouldBeCalled()
            ->willReturn($theme->reveal());

        $this->listener->setActiveThemeOnRequest($event);

        $this->assertSame($theme->reveal(), $this->themeContext->getTheme());
    }

    public function testEventListenerNoWebspace(): void
    {
        $request = new Request();
        $requestAttributes = new RequestAttributes([
            'webspace' => null,
        ]);
        $request->attributes->set('_sulu', $requestAttributes);

        $event = $this->createRequestEvent($request);

        $this->themeRepository->findOneByName(Argument::cetera())
            ->shouldNotBeCalled();

        $this->listener->setActiveThemeOnRequest($event);

        $this->assertNull($this->themeContext->getTheme());
    }

    public function testEventListenerNoAttributes(): void
    {
        $request = new Request();

        $event = $this->createRequestEvent($request);

        $this->themeRepository->findOneByName(Argument::cetera())
            ->shouldNotBeCalled();

        $this->listener->setActiveThemeOnRequest($event);

        $this->assertNull($this->themeContext->getTheme());
    }

    public function testEventListenerOnPreview(): void
    {
        /** @var ThemeInterface|ObjectProphecy $theme */
        $theme = $this->prophesize(ThemeInterface::class);

        /** @var Webspace|ObjectProphecy webspace */
        $webspace = $this->prophesize(Webspace::class);
        $webspace->getTheme()->willReturn('theme/name');

        $requestAttributes = new RequestAttributes([
            'webspace' => $webspace->reveal(),
        ]);

        $this->themeRepository->findOneByName('theme/name')
            ->shouldBeCalled()
            ->willReturn($theme->reveal());

        $this->listener->setActiveThemeOnPreviewPreRender(
            new PreRenderEvent($requestAttributes)
        );

        $this->assertSame($theme->reveal(), $this->themeContext->getTheme());
    }

    public function testEventListenerOnPreviewNoTheme(): void
    {
        $webspace = new Webspace();
        $webspace->setTheme(null);

        $requestAttributes = new RequestAttributes([
            'webspace' => $webspace,
        ]);

        $this->themeRepository->findOneByName(Argument::cetera())
            ->shouldNotBeCalled();

        $this->listener->setActiveThemeOnPreviewPreRender(
            new PreRenderEvent($requestAttributes)
        );

        $this->assertNull($this->themeContext->getTheme());
    }

    private function createRequestEvent(Request $request): RequestEvent
    {
        $kernel = $this->prophesize(HttpKernelInterface::class);

        return new RequestEvent(
            $kernel->reveal(),
            $request,
            HttpKernelInterface::MAIN_REQUEST
        );
    }
}
