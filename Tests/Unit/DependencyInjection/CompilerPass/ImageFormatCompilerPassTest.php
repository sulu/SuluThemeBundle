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
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Sulu\Bundle\ThemeBundle\DependencyInjection\CompilerPass\ImageFormatCompilerPass;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ImageFormatCompilerPassTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var ObjectProphecy<ThemeRepositoryInterface>
     */
    private $themeRepository;

    /**
     * @var ContainerBuilder
     */
    private $container;

    protected function setUp(): void
    {
        $this->themeRepository = $this->prophesize(ThemeRepositoryInterface::class);

        $this->container = new ContainerBuilder();
        $this->container->setParameter('sulu_media.format_manager.default_imagine_options', []);
        $this->container->setParameter('kernel.bundles', []);
        $this->container->set('sylius.repository.theme', $this->themeRepository->reveal());
    }

    public function testGetFiles(): void
    {
        $theme = $this->prophesize(ThemeInterface::class);
        $theme->getPath()
            ->willReturn('Tests/Application/theme')
            ->shouldBeCalled();

        $this->themeRepository
            ->findAll()
            ->willReturn([$theme->reveal()])
            ->shouldBeCalled();

        $compilerPass = new ImageFormatCompilerPass();
        $reflectionMethod = new \ReflectionMethod(ImageFormatCompilerPass::class, 'getFiles');
        $reflectionMethod->setAccessible(true);

        $this->assertSame(
            ['Tests/Application/theme/config/image-formats.xml'],
            $reflectionMethod->invoke($compilerPass, $this->container)
        );
    }
}
