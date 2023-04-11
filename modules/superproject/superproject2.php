<?php 

class superproject2 extends superproject
{ 
    public $cost = N+T+T+G+G;
    public $vp = 5;
    public $fixedCostShape = 2;
    public $fixedCostIcon = 4;
    public $variableShape1 = 1;
    public $variableShape2 = 3;
    public $name = "Archive of the eras";
    
    public function canDo($worker, $index = "", $ignoreFull = false)
    {
        return false;
    }
}