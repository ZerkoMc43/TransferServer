<?php

namespace Zerko43\TransferServer;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class TransferCmd extends Command {

    private Main $main;

    public function __construct(Main $main){
        parent::__construct("transferserver", "Allows to open a interface with you'r saved servers");
        $this->main = $main;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool{
        if($sender instanceof Player){
            $this->main->openTransferApi()->openTransferForm($sender);
            return true;
        }else{
            return false;
        }
    }

}