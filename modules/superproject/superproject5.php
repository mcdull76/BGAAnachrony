<?php 

class superproject5 extends superproject
{ 
    public $cost = T+T+T+T+EN;
    public $vp = 6;
    public $fixedCostShape = 1;
    public $fixedCostIcon = 1;
    public $variableShape1 = 2;
    public $variableShape2 = 3;
    public $name = "Anti-gravity field";
    
    public function canDo($worker, $index = "", $ignoreFull = false)
    {
        return false;
    }
}