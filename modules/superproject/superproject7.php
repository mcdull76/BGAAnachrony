<?php 

class superproject7 extends superproject
{ 
    public $cost = U+T+T+G+W+W;
    public $vp = 7;
    public $fixedCostShape = 3;
    public $fixedCostIcon = 1;
    public $variableShape1 = 1;
    public $variableShape2 = 2;
    public $name = "Particle collider";
    public $freeAction = true;
    
    public function do($parg1, $parg2, $varg1, $varg2)
    {
        anachrony::$instance->addPending($this->player_id, 0, "trade", null, "restrict");
    }
    
}