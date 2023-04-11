<?php 

class blocked11 extends blocked
{ 
    public function execute($arg)
    {
        $this->player->gain(null,null,2*VP);
    }
}