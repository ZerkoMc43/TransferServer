<?php

namespace Zerko43;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class TransferCmd extends Command {

    private Main $main;

    public function __construct(Main $main){
        parent::__construct("transferserver", "Permets d'ouvrir l'interface des serveurs enregistrer");
        $this->main = $main;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool{
        if($sender instanceof Player){
            $this->main->openTransferApi()->openTransferForm($sender);
            return true;
        }else{
            $sender->sendMessage("§cVous ne pouvez pas utiliser cette command sur la console !");
            return false;
        }
    }

}