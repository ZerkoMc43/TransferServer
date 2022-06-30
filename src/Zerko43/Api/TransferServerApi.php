<?php

namespace Zerko43\Api;

use pocketmine\player\Player;
use pocketmine\utils\Config;
use Form\CustomForm;
use Form\SimpleForm;
use libpmquery\PMQuery;
use Zerko43\Main;
use libpmquery\PmQueryException;

class TransferServerApi{

    private Config $data;

    /**
     * TransferServerApi constructor.
     * @param Main $main
     */
    public function __construct(Main $main){
        $this->data = new Config($main->getDataFolder() . "data.json", Config::JSON);
    }

    public function openTransferForm(Player $player) : void{
        $form = new SimpleForm(function(Player $player, $args = null){
            if($args === null){
                return false;
            }
            switch($args){
                case 0:
                    $this->openAddServer($player);
                    break;
                case 1:
                    if(count($this->data->getAll()) === 0){
                        $this->openError($player, "§cAucun serveur a été ajouter !");
                    }else{
                        $this->openServerList($player);
                    }
                    break;
                case 2:
                    if(count($this->data->getAll()) === 0){
                        $this->openError($player, "§cAucun serveur a supprimer !");
                    }else{
                        $this->openDellServer($player);
                    }
                    break;
            }
            return true;
        });
        $form->setTitle("Transfer");
        $form->addButton("Ajouter des serveurs");
        $form->addButton("Liste des serveurs enregistrés");
        $form->addButton("Supprimer un serveur");
        $player->sendForm($form);
    }

    /**
     * @throws PmQueryException
     */
    public function openServerList(Player $player) : void{
        $form = new SimpleForm(function(Player $player, $args = null){
            if($args === null){
                $this->openTransferForm($player);
                return false;
            }
            foreach(array_keys($this->data->getAll()) as $key){
                $ip = $this->data->get($key)[0];
                $port = $this->data->get($key)[1];
                $player->transfer($ip, $port);
            }
            return true;
        });
        $form->setTitle("Transfer");
        foreach(array_keys($this->data->getAll()) as $value){
            if(count($this->data->getAll()) == 0){
                $form->setContent('Aucun serveur dans votre liste');
            }
            $ip = $this->data->get($value)[0];
            $port = $this->data->get($value)[1];
            $status = PMQuery::query($ip, $port);
            $form->addButton("§e {$status["HostName"]}\n§fPlayers: §e{$status["Players"]}§f/§e{$status["MaxPlayers"]}");
        }
        $player->sendForm($form);
    }


    public function openAddServer(Player $player): void{
        $form = new CustomForm(function (Player $player, $args = null){
            if($args === null){
                $this->openTransferForm($player);
                return false;
            }

            if(is_numeric($args[2])){
                $this->data->set($args[0], array($args[1], $args[2]));
                $this->data->save();
                $this->openError($player, "§eVous avez ajouter:\n §bName:§e $args[0]\n§bIp:§e $args[1]\n§bPort: $args[2]");
            }else{
                $this->openError($player, "§cQue des chiffre sont acceptés !");
                return false;
            }
            return true;
        });
        $form->setTitle("Ajouter un serveur");
        $form->addInput("Nom du serveur","Nom du serveur.");
        $form->addInput("Ip du serveur", "Ip du serveur.");
        $form->addInput("Port du serveur", "Port du serveur.");
        $player->sendForm($form);
    }

    public function openDellServer(Player $player) : void{
        $form = new SimpleForm(function (Player $player, $args = null){
            if($args === null){
                $this->openTransferForm($player);
                return false;
            }
            foreach(array_keys($this->data->getAll()) as $key){
                $this->openError($player, "§sVous avez supprimer §e$key de la lists des serveur !");
                $this->data->remove($key);
                $this->data->save();
                return true;
            }
            return true;
        });
        $form->setTitle("Supprimer un serveur");
        foreach(array_keys($this->data->getAll()) as $key){
            $form->addButton("§e$key");
        }
        $player->sendForm($form);
    }
    public function openError(Player $player, string $error): void{
        $form = new CustomForm(function (Player $player, $args = null){
            $this->openTransferForm($player);
            if($args === null){
                return false;
            }
            return true;
        });
        $form->setTitle("Transfer");
        $form->addLabel($error);
        $player->sendForm($form);
    }

}
