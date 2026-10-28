<?php

declare(strict_types=1);

namespace WonderWp\Component\PluginSkeleton {

    if (!interface_exists(ManagerInterface::class, false)) {
        interface ManagerInterface
        {
        }
    }

    if (!interface_exists(ManagerAwareInterface::class, false)) {
        interface ManagerAwareInterface
        {
            public function getManager(): ManagerInterface;

            public function setManager(ManagerInterface $manager): static;
        }
    }

    if (!class_exists(AbstractManager::class, false)) {
        abstract class AbstractManager implements ManagerInterface
        {
            protected array $config = [];

            public function __construct($name = '', $version = '')
            {
            }

            public function getConfig($key = null, $default = null)
            {
                if ($key === null) {
                    return $this->config;
                }

                return $this->config[$key] ?? $default;
            }

            public function setConfig($key, $value = null)
            {
                $this->config[$key] = $value;

                return $this;
            }
        }
    }

    if (!class_exists(AbstractPluginManager::class, false)) {
        abstract class AbstractPluginManager extends AbstractManager
        {
        }
    }

    if (!trait_exists(ManagerAwareTrait::class, false)) {
        trait ManagerAwareTrait
        {
            /** @var ManagerInterface|null */
            protected $manager;

            public function getManager(): ManagerInterface
            {
                return $this->manager;
            }

            public function setManager(ManagerInterface $manager): static
            {
                $this->manager = $manager;

                return $this;
            }
        }
    }
}
