<?php

namespace Zerko43\TransferServer\Api;

use pocketmine\player\Player;
use pocketmine\utils\Config;
use Form\CustomForm;
use Form\SimpleForm;
use libpmquery\PMQuery;
use Zerko43\TransferServer\Main;
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
                        $this->openError($player, "§cYou have not registered any servers!");
                    }else{
                        $this->openServerList($player);
                    }
                    break;
                case 2:
                    if(count($this->data->getAll()) === 0){
                        $this->openError($player, "§cYou have not registered any servers!");
                    }else{
                        $this->openDellServer($player);
                    }
                    break;
            }
            return true;
        });
        $form->setTitle("Transfer");
        $form->addButton("Added");
        $form->addButton("List of registered servers");
        $form->addButton("Delete");
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
                $form->setContent('§cNo server');
            }
            $ip = $this->data->get($value)[0];
            $port = $this->data->get($value)[1];
            $status = PMQuery::query($ip, $port);
            $form->addButton("§e{$status["HostName"]}\n§fPlayers: §e{$status["Players"]}§f/§e{$status["MaxPlayers"]}");
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
                $this->openError($player, "§eYou have added:\n §bName:§e $args[0]\n§bIp:§e $args[1]\n§bPort: $args[2]");
            }else{
                $this->openError($player, "§cThat numbers are accepted!");
                return false;
            }
            return true;
        });
        $form->setTitle("Adding");
        $form->addInput("name");
        $form->addInput("IP");
        $form->addInput("PORT");
        $player->sendForm($form);
    }

    public function openDellServer(Player $player) : void{
        $form = new SimpleForm(function (Player $player, $args = null){
            if($args === null){
                $this->openTransferForm($player);
                return false;
            }
            foreach(array_keys($this->data->getAll()) as $key){
                $this->openError($player, "§sYou have deleted §e$key from the server list!");
                $this->data->remove($key);
                $this->data->save();
                return true;
            }
            return true;
        });
        $form->setTitle("Remove");
        foreach(array_keys($this->data->getAll()) as $key){
            $form->addButton("$key");
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
