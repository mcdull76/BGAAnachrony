<?php 

class superproject6 extends superproject
{ 
    public $cost = U+U+U+T+SC;
    public $vp = 5;
    public $fixedCostShape = 1;
    public $fixedCostIcon = 2;
    public $variableShape1 = 2;
    public $variableShape2 = 3;
    public $name = "Uranium cores";
    public $freeAction = true;
    
    public function do($parg1, $parg2, $varg1, $varg2)
    {
        $this->player->gain(null,null,EX);
    }
}