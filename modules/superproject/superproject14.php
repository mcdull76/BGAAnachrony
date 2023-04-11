<?php 

class superproject14 extends superproject
{ 
    public $cost = E+T+T+W+W+W;
    public $vp = 5;
    public $fixedCostShape = 1;
    public $fixedCostIcon = 3;
    public $variableShape1 = 2;
    public $variableShape2 = 3;
    public $name = "Rescue pods";
    
    public function canDo($worker, $index = "", $ignoreFull = false)
    {
        return false;
    }
}