<?php

include("action/construct.php");
include("action/purify.php");
include("action/research.php");
include("action/recruit.php");
include("action/council.php");
include("action/mine.php");
include("action/trade.php");
include("action/supply.php");
include("action/force.php");
include("action/leader1.php");
include("action/leader3.php");
include("action/evacuation.php");

class action extends APP_GameClass
{
    public $player_id = 0;
    public $player;
    public $actionCost = 0;
    public $needExosuit = false;
    public $freeAction = false;
    public $multispace = false;
    public $player_board = false;
    
    public $workerTypeRequired = 0;
    
    public $worker_id;
    public $worker_type;
    public $maxindex = 0;
        
    public function __construct()
    {
        $this->player_id = anachrony::$instance->getActivePlayerId();
        $this->player = new ANAPlayer($this->player_id);
        $this->slotname = "ph".get_class($this);
        
        if($this->player_board)
        {
            $this->slotname = $this->slotname."_".$this->player_id;
        }
    }
    
    public function isFree()
    {
        return $this->freeAction;
    }
    
    public function canDo($worker, $index = "", $ignorefull = false)
    {
        $add = "";
        if($index != "")
        {
            $add="_".$index;
        }
        return ($this->workerTypeRequired == 0 || $this->workerTypeRequired == $worker['type']  || GENIUS == $worker['type'] ||  ( SCIENTIST == $worker['type'] && $this->player->canUseSCasGE()) )
        && ($this->multispace || !$this->needExosuit || anachrony::$instance->getUniqueValueFromDB( "select count(*) from exosuit where location = '{$this->slotname}{$add}'") == 0)
        && (!$this->isFree() || anachrony::$instance->getUniqueValueFromDB( "select count(*) from path where location = '{$this->slotname}{$add}'") == 0)
        && ($this->needExosuit || $ignorefull || anachrony::$instance->getUniqueValueFromDB( "select count(*) from worker where location = '{$this->slotname}{$add}'") == 0)
        && ( !$this->needExosuit || anachrony::$instance->getUniqueValueFromDB( "select count(*) from blocked where (type = 0 or type%10=6) and location = '{$this->slotname}{$add}'") == 0);
    }
    
    
    public function getActionCost($index = 0)
    {
        $ret = $this->actionCost;
        
        if($this->player->hasActiveSuperProject(4))
        {
            $ret = $this->player->minusCost($this->actionCost, [W])[0];
        }
        
        return $ret;
    }
    
    public function arg($parg1, $parg2)
    {
    }
    
    public function do($parg1, $parg2, $varg1, $varg2)
    {
        
    }    
    
    public function getBlocked($index = "")
    {
        $ret = null;
        if($this->worker_id != null)
        {
            $exosuit_id =  (int) filter_var(self::getUniqueValueFromDB( "select location from worker where id = {$this->worker_id}"), FILTER_SANITIZE_NUMBER_INT);
            if($exosuit_id != null)
            {
                $location =  self::getUniqueValueFromDB( "select location from exosuit where id = {$exosuit_id}");
                $blocked = self::getObjectFromDB("SELECT * FROM blocked WHERE location = '{$location}'"); 
                if($blocked != null)
                {
                    $name = "blocked".$blocked['type'];
                    $b = new $name();
                    $b->player_id = $this->player_id;
                    $b->player = $this->player;
                    $b->action = $this;
                    $ret = $b;
                }
            }
        }
        return $ret;
    }
    
    public function boost($arg = null)
    {  
        $blocked = $this->getBlocked();
        if($blocked != null)
        {
            $blocked->execute($arg);
        }
    } 
    
    public function getAvailableSlots($exosuit_id, $workers)
    {
        $ret = array();
        
        if(!$this->needExosuit || $exosuit_id != null)
        {
            if($this->isFree() || count($workers)>0)
            {
                if($this->maxindex==0)
                {
                    if($this->player->checkCost($this->getActionCost()))
                    {
                        if($this->isFree() && $this->canDo(0))
                        {
                            $ret[$this->slotname] = array();                    
                        }
                        else
                        {
                            $correctWorkers = array();
                            foreach($workers as $worker_id => $worker)
                            {
                                if($this->canDo($worker) && !in_array("workertype_".$worker['type'], $correctWorkers))
                                {
                                    $correctWorkers[] = "workertype_".$worker['type'];
                                }
                            }            
                            if(count($correctWorkers)>0)
                            {
                                $ret[$this->slotname] = array();
                                $ret[$this->slotname]['titleyou'] = clienttranslate('${you} must choose a worker to do this action');
                                $ret[$this->slotname]['target'] = $correctWorkers;
                            }
                        }
                    }
                }
                else
                {
                    foreach($workers as $worker_id => $worker)
                    {
                        for($i=1;$i<=$this->maxindex;$i++)
                        {
                            if($this->player->checkCost($this->getActionCost($i)))
                            {
                                $correctWorkers = array();
                                foreach($workers as $worker_id => $worker)
                                {
                                    if($this->canDo($worker,$i) && !in_array("workertype_".$worker['type'], $correctWorkers))
                                    {
                                        $correctWorkers[] = "workertype_".$worker['type'];
                                    }
                                }
                                if(count($correctWorkers)>0)
                                {
                                    $ret["{$this->slotname}_{$i}"] = array();
                                    $ret["{$this->slotname}_{$i}"]['titleyou'] = clienttranslate('${you} must choose a worker to do this action');
                                    $ret["{$this->slotname}_{$i}"]['target'] = $correctWorkers;
                                }
                            }
                        }
                    }
                }
            }
        }
        return $ret;
    }
    
    public function init($parg1, $parg2, $varg1, $varg2)
    {
        $expl = explode("_",$varg1);
        $action = substr($expl[0], 2);
        $index = "";
        if(count($expl)>1)
        {
            $index =  explode("_",$varg1)[1];
        }
        
        $this->worker_type = (int) filter_var($varg2, FILTER_SANITIZE_NUMBER_INT);
        $this->worker_id =  self::getUniqueValueFromDB("select max(id) from worker where type = {$this->worker_type} and location = 'awake_{$this->player_id}'");
        
        $this->log();
        
        $cost = $this->getActionCost($index);
        if($cost >0)
        {
            $this->player->pay(null,null, $cost);
        }
        
        
        if($this->isFree())
        {
            self::DbQuery("INSERT INTO path (player_id, path, location) VALUES ({$this->player_id},{$this->player->path->type},'{$this->slotname}')");
            $path = self::getObjectFromDB( "select * from path where location = '{$this->slotname}'");
            anachrony::$instance->notifyAllPlayers( "newpath", '', array(
                'path' => $path
            ) );
        }   
        else if($this->needExosuit)
        {            
            $exosuit_id = self::getUniqueValueFromDB( "select id from exosuit where location like 'phpower%_{$this->player_id}' order by location limit 1");
        
            //move worker
            self::DbQuery("update worker set location = 'exosuit_{$exosuit_id}' where id = {$this->worker_id}");
            anachrony::$instance->notifyAllPlayers( "move", '', array(
                'mobile' => 'worker_'.$this->worker_id,
                'parent' => "exosuit_{$exosuit_id}"
            ) );
            
            anachrony::$instance->notifyAllPlayers( 'simplePause', '', [ 'time' => 500] );
            
            
            if($this->multispace)
            {
                $varg1 = $action."stack";   
            }
            
            self::DbQuery("update exosuit set location = '{$varg1}' where id = {$exosuit_id}");
            anachrony::$instance->notifyAllPlayers( "move", '', array(
                'mobile' => 'exosuit_'.$exosuit_id,
                'parent' => "{$varg1}"
            ) );
        }
        else
        {
            //move worker
            self::DbQuery("update worker set location = '{$varg1}' where id = {$this->worker_id}");
            anachrony::$instance->notifyAllPlayers( "move", '', array(
                'mobile' => 'worker_'.$this->worker_id,
                'parent' => "{$varg1}"
            ) );
        }
          
        anachrony::$instance->addPending($this->player_id, $this->worker_id, $action, null, null);
        
    }
    
    function isCleanupAwake($workertype)
    {
        return false;
    }
    
    function isCleanupDead($workertype)
    {
        return false;
    }
    
    function log()
    {
        
    }
}