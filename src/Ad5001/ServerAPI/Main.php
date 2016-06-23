<?php
namespace Ad5001\ServerAPI ; 
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\Plugin;
use pocketmine\scheduler\PluginTask;
use pocketmine\Server;
use pocketmine\permission\PermissibleBase;
use pocketmine\permission\PermissionAttachment;
use pocketmine\utils\Config;
 use pocketmine\Player;


class Main extends PluginBase{
    
    
    
public function onEnable(){
  if(!file_exists($this->getServer()->getPluginPath() . "Online/config.yml")) {
      $this->getLogger()->alert("This plugin require Online to work with ! You can download it from ImagicalMine !");
      $this->setEnable(false);
  } else {
      $this->saveDefaultConfig();
      $this->saveResource("api.php");
      $cfg = new Config($this->getServer()->getPluginPath() . "Online/config.yml");
      $denied = $cfg->get("denied-pages");
      array_push($denied, "command"); // Just to be sure that people can't modify this.
      array_push($denied, "pass");
      array_push($denied, "return");
      $cfg->set("denied-pages", $denied);
      file_put_contents($this->getServer()->getPluginPath() . "Online/pass", sha1($this->getConfig()->get("username")) . "///" . sha1($this->getConfig()->get("password")));
      $this->getServer()->getScheduler()->scheduleRepeatingTask(new APIChanger($this), 20);
  }
 }
 
 
 public function exe(array $args) {
     switch(strtolower($args[0])) {
         
         
         case "setmotd":
         unset($args[0]);
         $this->getServer()->setMotd(implode(" ", $args));
         return [true];
         break;
         
         
         case "thisinconfig":
         return [$this->getServer()->getConfigString($args[1])];
         break;
         
         
         case "setinconfig":
         $set = $args[1];
         $this->getServer()->getConfigString($args[1], json_decode($args[2]));
         return [true];
         break;
         
         
         case "hasplugin":
         if($this->getServer()->getPluginManager()->getPlugin($args[1]) !== null) {
             return [true];
         } else {
             return [false];
         }
         break;
         
         
         case "isop":
         if($this->getServer()->isOp($args[1])) {
             return [true];
         } else {
             return [false];
         }
         break;
         
         
         case "execommand":
         unset($args[0]);
         $this->getServer()->dispatchCommand(new ServerAPISender(), implode(" ", $args));
         if(isset($this->log)) {
             return $this->log;
         } else {
             return null;
         }
         break;
     }
 }
 
 public function log(ServerAPISender $sender, string $message) {
     $this->lastlog = $message;
 }
 
 
}























class APIChanger extends PluginTask {
    
    
    public function __construct(Plugin $main) {
        parent::__construct($main);
        $this->main = $main;
        $this->lastcmd = null;
    }
    
    
    public function onRun($tick) {
        if(file_exists($this->main->getDataFolder() . "api.php")) {
            $api = file_get_contents($this->main->getDataFolder() . "api.php");
        }else {
            $this->main->saveResource("api.php");
            $api = file_get_contents($this->main->getDataFolder() . "api.php");
        }
        $players = [];
        
        /*
        Getting things that can be get
        */
        
        foreach($this->main->getServer()->getOnlinePlayers() as $player) {
            array_push($players, $player->getName());
        }
        $api = str_ireplace("api_players", implode('", "', $players), $api);
        $api = str_ireplace("api_pmversion", $this->main->getServer()->getPocketMineVersion(), $api);
        $api = str_ireplace("api_version", $this->main->getServer()->getVersion(), $api);
        $api = str_ireplace("api_name", $this->main->getServer()->getName(), $api);
        $api = str_ireplace("api_motd", $this->main->getServer()->getMotd(), $api);
        $api = str_ireplace("api_port", $this->main->getServer()->getPort(), $api);
        $api = str_ireplace("api_codename", $this->main->getServer()->getPort(), $api);
        $api = str_ireplace("api_plugin_api_code", $this->main->getServer()->getApiVersion(), $api);
        $api = str_ireplace("api_maxplayers", $this->main->getServer()->getMaxPlayers(), $api);
        file_put_contents($this->main->getServer()->getPluginPath() . "Online/api.php", $api);
        
        
        /*
        Getting that can be set
        */
        
        if(file_exists($this->main->getServer()->getPluginPath() . "Online/command")) {
            if($cmd = file_get_contents($this->main->getServer()->getPluginPath() . "Online/command") !== $this->lastcmd) {
                $args = explode(" ", $cmd);
                file_put_contents($this->main->getServer()->getPluginPath() . "Online/return", serialize($this->main->exe($args)));
            }
        }
    }
}



























class ServerAPISender implements CommandSender {
    
    
    public function getServer() {
        return Server::getInstance();
    }
    
    
    public function __construct(){
		$this->perm = new PermissibleBase($this);
	}
    
    
    
	public function getName() : string{
		return "ServerAPI";
	}
    
    
    
    public function isPermissionSet($name){
		return $this->perm->isPermissionSet($name);
	}

	
    
    
	public function hasPermission($name){
		return true;
	}

	
	public function addAttachment(Plugin $plugin, $name = null, $value = null){
		return $this->perm->addAttachment($plugin, $name, $value);
	}

    
    
	public function removeAttachment(PermissionAttachment $attachment){
		$this->perm->removeAttachment($attachment);
	}
    
    

	public function recalculatePermissions(){
		$this->perm->recalculatePermissions();
	}

	
	public function getEffectivePermissions(){
		return $this->perm->getEffectivePermissions();
	}

	
	public function isPlayer(){
		return false;
	}
    
    
    
    
	public function sendMessage($message){
		Main::log($this, $message);
        return true;
	}
    
    
    
	public function isOp(){
		return true;
	}
    
    
    
	public function setOp($value){}

    
    
}