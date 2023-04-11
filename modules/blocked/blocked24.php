<?php 

class blocked24 extends blocked
{ 
    public function execute($arg)
    {
        if (strpos($arg, 'superproject') === 0)
        {
            $this->player->gain(null,null,2*VP);
        }
    }
}