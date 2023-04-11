<?php 

class building210 extends building
{
    public $vp = 1;
    public $actionCost = 3*W;
    
    public $workerTypeRequired = ENGINEER;
    
    public function do($parg1, $parg2, $varg1, $varg2)
    {
        anachrony::$instance->addPendingSub($this->player_id, $this->worker_id, get_class($this), "sgain",null, 0);
    }
    
    
    public function argsgain($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['buttons'] = array();
        $ret['title'] = clienttranslate('${actplayer} must choose what to gain');
        $ret['titleyou'] = clienttranslate('${you} must choose what to gain');
        if($parg1 == 0)
        {
            $ret['buttons'][] = 'res'.N;
            $ret['selectable'][N] = array();
        }
        $ret['buttons'][] = 'res'.G;
        $ret['selectable'][G] = array();
        $ret['buttons'][] = 'res'.U;
        $ret['selectable'][U] = array();
        $ret['buttons'][] = 'res'.T;
        $ret['selectable'][T] = array();  
        
        $ret['buttons'][] = 'Undo';
        $ret['selectable']['Undo'] = array();
        return $ret;
    }
    
    public function dosgain($parg1, $parg2, $varg1, $varg2)
    {
        $this->player->gain(null,null,$varg1);
        if($varg1 != 'res'.N && $parg1<2)
        {
            anachrony::$instance->addPendingSub($this->player_id, $this->worker_id, get_class($this), "sgain",null, $parg1+1);            
        }
    }
}