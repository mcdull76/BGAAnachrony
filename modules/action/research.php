<?php 

class research extends action
{     
    public $needExosuit = true;
    
    function __construct( )
    {
        parent::__construct();
        $this->maxindex = self::getUniqueValueFromDB("select count(*) from player")>3?3:2;
    }
    
    public function canDo($worker, $index = "", $ignorefull = false)
    {
        return parent::canDo($worker, $index, $ignorefull)
        && $worker['type'] != ENGINEER && $worker['type'] != ADMIN;
    }
    
    public function getActionCost($index = 0)
    {
        if(anachrony::$instance->getGameStateValue( "after_impact") == 1)
        {
            return 0;
        }
        return W*max(0, $index - 1 - ($this->player->hasActiveSuperProject(4)?1:0));
    }    
    
    function arg($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['buttons'] = array();
        $ret['title'] = clienttranslate('${actplayer} must set one Research die');
        $ret['titleyou'] = clienttranslate('${you} must set one Research die');
        
        for($shape=1;$shape<=3;$shape++)
        {
            $ret['buttons'][] = 'shape'.$shape;
            $ret['selectable']['shape'.$shape] = array();
        }
        
        for($icon=1;$icon<=5;$icon++)
        {
            $ret['buttons'][] = 'icon'.$icon;
            $ret['selectable']['icon'.$icon] = array();
        }
        
        if($this->extra == null)
        {
            $ret['buttons'][] = 'Undo';
            $ret['selectable']['Undo'] = array();
        }
        return $ret;
    }
    
    function do($parg1, $parg2, $varg1, $varg2 = null) {
        
        if($this->getBlocked() != null && $this->getBlocked()->name == "blocked13")
        {
            anachrony::$instance->notifyAllPlayers( "note", clienttranslate('${player_name} set ${dice}'), array(
                'player_id' => $this->player_id,
                'player_name' => $this->player->player_name,
                'dice' => $varg1
            ) );
            anachrony::$instance->addPendingSub($this->player_id, $this->worker_id, "blocked13", "secondchoice", $this->extra, $varg1);
        }
        else if($this->player->hasActiveBuilding(411) && $parg1==null)
        {
            anachrony::$instance->notifyAllPlayers( "note", clienttranslate('${player_name} set ${dice}'), array(
                'player_id' => $this->player_id,
                'player_name' => $this->player->player_name,
                'dice' => $varg1
            ) );
            anachrony::$instance->addPendingSub($this->player_id, $this->worker_id, "building411", "secondchoice", $this->extra, $varg1);
        }
        else
        {
            if (strpos($varg1, 'icon') === 0) {
                $dice2 = 'shape'.bga_rand(1,3);
            }
            else
            {
                $dice2 = 'icon'.bga_rand(1,6);
            }
            
            
            anachrony::$instance->setGameStateValue( 'no_undo', 1);
            
            anachrony::$instance->notifyAllPlayers( "note", clienttranslate('${player_name} set ${dice} and rolls ${dice2}'), array(
                'player_id' => $this->player_id,
                'player_name' => $this->player->player_name,
                'dice' => $varg1,
                'dice2' => $dice2
            ) );
            
            if($dice2 == "icon6")
            {
                anachrony::$instance->addPendingSub($this->player_id, $this->worker_id, "research", "choice", $this->extra, $varg1);
            }
            else
            {
                $this->researchGain($varg1, $dice2);
            }
        }
    }
    
    function argchoice($parg1)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['buttons'] = array();
        $ret['title'] = clienttranslate('${actplayer} must set the icon Research die');
        $ret['titleyou'] = clienttranslate('${you} must set the icon Research die');
        
        for($icon=1;$icon<=5;$icon++)
        {
            $ret['buttons'][] = 'icon'.$icon;
            $ret['selectable']['icon'.$icon] = array();
        }
        
        return $ret;
    }
    
    function dochoice($parg1, $parg2, $varg1, $varg2 = null) {
        
        anachrony::$instance->notifyAllPlayers( "note", clienttranslate('${player_name} set ${dice} and ${dice2}'), array(
            'player_id' => $this->player_id,
            'player_name' => $this->player->player_name,
            'dice' => $parg1,
            'dice2' => $varg1
        ) );
        
        $this->researchGain($varg1, $parg1);        
    }
    
    function researchGain($arg1, $arg2)
    {
        if (strpos($arg1, 'icon') === 0) {
            $icon = (int) filter_var($arg1, FILTER_SANITIZE_NUMBER_INT) ;
            $shape = (int) filter_var($arg2, FILTER_SANITIZE_NUMBER_INT);
        }
        else
        {
            $icon = (int) filter_var($arg2, FILTER_SANITIZE_NUMBER_INT) ;
            $shape = (int) filter_var($arg1, FILTER_SANITIZE_NUMBER_INT) ;
        }
        
        $b_id = self::getUniqueValueFromDB( "SELECT max(id) FROM breakthrough where player_id IS NULL and shape = {$shape} and icon = {$icon}");
        if($b_id == null)
        {
            anachrony::$instance->notifyAllPlayers( "text", clienttranslate('This breakthrough is not available anymore'), array() );
            anachrony::$instance->addPendingSub($this->player_id, $this->worker_id, "research", "reroll", $this->extra, $arg1, $arg2);
        }
        else
        {
            anachrony::$instance->notifyAllPlayers( "move", '', array(
                'mobile' => 'breakthrough_'.$b_id,
                'parent' => "breakthroughs_{$this->player_id}"
            ) );
            self::DbQuery("update breakthrough set player_id = {$this->player_id}, location='breakthroughs_{$this->player_id}' where id = {$b_id}");
            
            $this->boost();
        }
    }
    
    function argreroll($arg1, $arg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['buttons'] = array();
        $ret['title'] = clienttranslate('${actplayer} must reroll one Research die');
        $ret['titleyou'] = clienttranslate('${you} must reroll one Research die');
        
        $ret['buttons'][] = $arg1;
        $ret['selectable'][$arg1] = array();
        $ret['buttons'][] = $arg2;
        $ret['selectable'][$arg2] = array();
        return $ret;
    }
    
    function doreroll($parg1, $parg2, $varg1, $varg2 = null) {
        
        if (strpos($varg1, 'icon') === 0) {
            
            if (strpos($parg1, 'icon') === 0) {
                $icon = bga_rand(1,6);
                $shape = (int) filter_var($parg2, FILTER_SANITIZE_NUMBER_INT);
            }
            else
            {
                $icon = bga_rand(1,6);
                $shape = (int) filter_var($parg1, FILTER_SANITIZE_NUMBER_INT);
            }
            $dice = "icon".$icon;
        }
        else
        {
            if (strpos($parg1, 'icon') === 0) {
                $shape = bga_rand(1,3);
                $icon = (int) filter_var($parg1, FILTER_SANITIZE_NUMBER_INT);
            }
            else
            {
                $shape = bga_rand(1,3);
                $icon = (int) filter_var($parg2, FILTER_SANITIZE_NUMBER_INT);
            }
            $dice = "shape".$shape;
        }
        
        anachrony::$instance->setGameStateValue( 'no_undo', 1);
        
        anachrony::$instance->notifyAllPlayers( "note", clienttranslate('${player_name} rerolls ${dice}'), array(
            'player_id' => $this->player_id,
            'player_name' => $this->player->player_name,
            'dice' => $dice
        ) );
        
        $this->researchGain("icon".$icon, "shape".$shape);  
    }
    
    
}