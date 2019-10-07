<?php

/*
 * This file is part of Sulu.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\ThemeBundle\StructureProvider;

use Doctrine\Common\Cache\Cache;
use Liip\ThemeBundle\ActiveTheme;
use Sulu\Component\Content\Compat\Structure\PageBridge;
use Sulu\Component\Content\Compat\StructureManagerInterface;
use Sulu\Component\Webspace\Manager\WebspaceManagerInterface;
use Sulu\Component\Webspace\StructureProvider\WebspaceStructureProvider as BaseWebspaceStructureProvider;

/**
 * Provide templates which are implemented in a single webspace.
 */
class WebspaceStructureProvider extends BaseWebspaceStructureProvider
{
    /**
     * @var WebspaceManagerInterface
     */
    protected $webspaceManager;

    /**
     * @var ActiveTheme
     */
    protected $activeTheme;

    /**
     * @param \Twig_Environment $twig
     * @param StructureManagerInterface $structureManager
     * @param Cache $cache
     * @param WebspaceManagerInterface $webspaceManager
     * @param ActiveTheme $activeTheme
     */
    public function __construct(
        \Twig_Environment $twig,
        StructureManagerInterface $structureManager,
        Cache $cache,
        WebspaceManagerInterface $webspaceManager,
        ActiveTheme $activeTheme
    ) {
        parent::__construct($twig, $structureManager, $cache);

        $this->webspaceManager = $webspaceManager;
        $this->activeTheme = $activeTheme;
    }

    /**
     * {@inheritdoc}
     */
    protected function loadStructures($webspaceKey)
    {
        $before = $this->activeTheme->getName();
        $webspace = $this->webspaceManager->findWebspaceByKey($webspaceKey);

        if (!$webspace) {
            return [];
        }


        if (null !== $webspace->getTheme()) {
            $this->activeTheme->setName($webspace->getTheme());
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
        $this->activeTheme->setName($before);
        $this->cache->save($webspaceKey, $keys);

        return $structures;
    }
}
