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

namespace Sulu\Bundle\ThemeBundle\Tests\Unit\DependencyInjection\CompilerPass;

use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Sulu\Bundle\ThemeBundle\DependencyInjection\CompilerPass\ImageFormatCompilerPass;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ImageFormatCompilerPassTest extends TestCase
{
    /**
     * @var ThemeRepositoryInterface|ObjectProphecy
     */
    private $themeRepository;

    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * @var ImageFormatCompilerPass
     */
    private $compilerPass;

    protected function setUp(): void
    {
        $this->themeRepository = $this->prophesize(ThemeRepositoryInterface::class);

        $this->container = new ContainerBuilder();
        $this->container->setParameter('sulu_media.format_manager.default_imagine_options', []);
        $this->container->setParameter('kernel.bundles', []);
        $this->container->set('sylius.repository.theme', $this->themeRepository->reveal());

        $this->compilerPass = new ImageFormatCompilerPass();
    }

    public function testGetFiles(): void
    {
        /** @var ThemeInterface|ObjectProphecy $theme */
        $theme = $this->prophesize(ThemeInterface::class);
        $theme->getPath()
            ->willReturn('Tests/Application/theme')
            ->shouldBeCalled();

        $this->themeRepository
            ->findAll()
            ->willReturn([$theme->reveal()])
            ->shouldBeCalled();

        $this->compilerPass->process($this->container);

        $formats = $this->container->getParameter('sulu_media.image.formats');

        $this->assertCount(1, $formats);
        // @phpstan-ignore-next-line
        $this->assertArrayHasKey('600x', $formats);
    }
}
