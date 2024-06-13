<?php

declare(strict_types=1);

namespace Fred\FreezeHibernatesPM;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskHandler;

class Main extends PluginBase implements Listener {

    private ?TaskHandler $freezeTaskHandler = null;

    public function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onPlayerJoin(PlayerJoinEvent $event): void {
        if ($this->freezeTaskHandler !== null) {
            $this->freezeTaskHandler->cancel();
            $this->freezeTaskHandler = null;
            $this->getLogger()->info("Server resumed as a player joined.");
        }
    }

    public function onPlayerQuit(PlayerQuitEvent $event): void {
        if (count($this->getServer()->getOnlinePlayers()) === 1) {
            $this->freezeTaskHandler = $this->getScheduler()->scheduleDelayedTask(new ClosureTask(function (): void {
                if (count($this->getServer()->getOnlinePlayers()) === 0) {
                    $this->freezeServer();
                }
            }), 20);
        }
    }

    private function freezeServer(): void {
        $this->getLogger()->info("Freezing server activities as there are no players online...");

        $this->getScheduler()->cancelAllTasks();
    }
}
