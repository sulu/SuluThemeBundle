<?php

/*
 * This file is part of Sulu.
 *
 * (c) Sulu GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\ThemeBundle\StructureProvider;

use Doctrine\Common\Cache\Cache;
use Sulu\Component\Content\Compat\Structure\PageBridge;
use Sulu\Component\Content\Compat\StructureInterface;
use Sulu\Component\Content\Compat\StructureManagerInterface;
use Sulu\Component\Webspace\Manager\WebspaceManagerInterface;
use Sulu\Component\Webspace\StructureProvider\WebspaceStructureProvider as BaseWebspaceStructureProvider;
use Sylius\Bundle\ThemeBundle\Context\SettableThemeContext;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;

/**
 * Provide templates which are implemented in a single webspace.
 */
class WebspaceStructureProvider extends BaseWebspaceStructureProvider
{
    /**
     * @var WebspaceManagerInterface
     */
    protected $webspaceManager;

    /** @var ThemeRepositoryInterface */
    private $themeRepository;

    /** @var SettableThemeContext */
    private $themeContext;

    public function __construct(
        \Twig_Environment $twig,
        StructureManagerInterface $structureManager,
        Cache $cache,
        WebspaceManagerInterface $webspaceManager,
        ThemeRepositoryInterface $themeRepository,
        SettableThemeContext $themeContext
    ) {
        parent::__construct($twig, $structureManager, $cache);

        $this->webspaceManager = $webspaceManager;
        $this->themeRepository = $themeRepository;
        $this->themeContext = $themeContext;
    }

    /**
     * {@inheritdoc}
     *
     * @return StructureInterface[]
     */
    protected function loadStructures($webspaceKey): array
    {
        $before = $this->themeContext->getTheme()->getName();
        $webspace = $this->webspaceManager->findWebspaceByKey($webspaceKey);

        if (!$webspace) {
            return [];
        }

        if (null !== $webspace->getTheme()) {
            $this->themeContext->setTheme(
                $this->themeRepository->findOneByName($webspace->getTheme())
            );
        }

        $structures = [];
        $keys = [];
        /** @var PageBridge $page */
        foreach ($this->structureManager->getStructures() as $page) {
            $template = sprintf('%s.html.twig', $page->getView());
            if ($this->templateExists($template)) {
                $keys[] = $page->getKey();
                $structures[] = $page;
            }
        }
        $this->themeContext->setTheme(
            $this->themeRepository->findOneByName($before)
        );
        $this->cache->save($webspaceKey, $keys);

        return $structures;
    }
}
