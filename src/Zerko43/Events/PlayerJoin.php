<?php

namespace Zerko43\Events;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use Zerko43\Main;

class PlayerJoin implements Listener {

    private Main $main;

    public function __construct(Main $main){
        $this->main = $main;
    }

    public function onJoin(PlayerJoinEvent $event) : void{
        $player = $event->getPlayer();
        $this->main->openTransferApi()->openTransferForm($player);
    }

}