<?php 

class blocked32 extends blocked
{ 
    public function execute($arg)
    {
        $this->player->gain(null,null,EX);
    }
}