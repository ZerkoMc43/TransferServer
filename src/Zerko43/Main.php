<?php

namespace Zerko43;

use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use Zerko43\Api\TransferServerApi;
use Zerko43\Events\PlayerJoin;

class Main extends PluginBase implements Listener{

    /**
     * @return void
     */
    public function onEnable(): void{
        $this->getServer()->getPluginManager()->registerEvents(new PlayerJoin($this), $this);
        $this->getServer()->getCommandMap()->unregister($this->getServer()->getCommandMap()->getCommand("transferserver"));
        $this->getServer()->getCommandMap()->register("transferserver", new TransferCmd($this));
    }

    /**
     * @return TransferServerApi
     */
    public function openTransferApi() : TransferServerApi{
        return new TransferServerApi($this);
    }

}
