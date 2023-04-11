<?php 

for($dist = 1; $dist<=18;$dist++)
{
    include("superproject/superproject{$dist}.php");
}

class superproject extends action
{
    public $type = 0;
    public $id = 0;
    public $cost = 0;
    public $vp = 0;
    public $fixedCostShape = 0;
    public $fixedCostIcon = 0;
    public $variableShape1 = 0;
    public $variableShape2 = 0;
    public $name = "";
    
    public function __construct()
    {
        parent::__construct();
        $this->type = (int) filter_var(get_class($this), FILTER_SANITIZE_NUMBER_INT);        
    }
    
    public function whenBuild()
    {
        
    }
    
    function getVP()
    {
        return $this->vp + ($this->player->hasActiveSuperProject(16)?3:0);
    }
    
    function getCost($worker_type)
    {        
        $cost = $this->cost;
        if((intdiv($cost, T) % 10) > 0 && ($worker_type == ENGINEER || $worker_type == GENIUS ||  ( SCIENTIST == $worker_type && $this->player->canUseSCasGE())))
        {
            $cost -= T;
        }
        return $cost;
    }
    
    function isUseful($tested, $used)
    {
        if($this->fixedCostShape != 0 && $used!=null && $used['shape'] == $this->fixedCostShape && $used['icon'] == $this->fixedCostIcon)
        {
            //cost already paid
            return false;
        }        
        if($this->fixedCostShape != 0 && $used!=null && $tested['shape'] == $this->fixedCostShape && $tested['icon'] == $this->fixedCostIcon)
        {
            //cost already paid partially, full won't do
            return false;
        }        
        if($this->fixedCostShape != 0 && $used==null && $tested['shape'] == $this->fixedCostShape && $tested['icon'] == $this->fixedCostIcon)
        {
            //cost pays fully
            return true;
        }        
        if($tested['shape'] == $this->variableShape1 && ($used == null || $used['shape'] == $this->variableShape2))
        {
            return true;
        }
        if($tested['shape'] == $this->variableShape2 && ($used == null || $used['shape'] == $this->variableShape1))
        {
            return true;
        }
        
        return false;
        
    }
    
    function checkBreakthroughCost($bts, $checkNumber)
    {        
        $solo = false;
        $double1 = false;
        $double2 = false;        
        foreach($bts as $bt_id => $bt)
        {
            if($this->fixedCostShape != 0 && $bt['shape'] == $this->fixedCostShape && $bt['icon'] == $this->fixedCostIcon)
            {
                $solo = true;
            }
            if($bt['shape'] == $this->variableShape1 && !$double1)
            {
                $double1 = true;
            }
            else if($bt['shape'] == $this->variableShape2)
            {
                $double2 = true;
            }
        }        
        return  ($solo && (!$checkNumber || count($bts) == 1)) || ($double1 && $double2 && (!$checkNumber || count($bts) == 2));
    }
    
    function log()
    {
        anachrony::$instance->notifyAllPlayers( "note", clienttranslate('${player_name} uses Superproject <b>${superproject_name}</b>'),
            array(
                "player_id" => $this->player->player_id,
                "player_name" => $this->player->player_name,
                'superproject_name' => $this->name,
            )
            );
    }
    
}