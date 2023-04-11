<?php 

class superproject13 extends superproject
{ 
    public $cost = T+T+T+G+EN;
    public $vp = 6;
    public $fixedCostShape = 3;
    public $fixedCostIcon = 2;
    public $variableShape1 = 1;
    public $variableShape2 = 2;
    public $name = "Tectonic drill";
    
    public function canDo($worker, $index = "", $ignoreFull = false)
    {
        return false;
    }
}