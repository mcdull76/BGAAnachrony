<?php 

class superproject15 extends superproject
{ 
    public $cost = N+N+T+T+T+SC;
    public $vp = 8;
    public $fixedCostShape = 2;
    public $fixedCostIcon = 1;
    public $variableShape1 = 1;
    public $variableShape2 = 3;
    public $name = "Neutronium research center";
    
    public function canDo($worker, $index = "", $ignoreFull = false)
    {
        return false;
    }
    
    public function whenBuild()
    {
        anachrony::$instance->addPending($this->player_id, null, "research");
        anachrony::$instance->addPending($this->player_id, null, "research");
    }
}