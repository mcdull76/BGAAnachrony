<?php 

class blocked13 extends blocked
{ 
    function argsecondChoice($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['buttons'] = array();
        $ret['title'] = clienttranslate('${actplayer} must set the second Research die');
        $ret['titleyou'] = clienttranslate('${you} must set the second Research die');
                
                
        if (strpos($parg1, 'icon') === 0) {
            for($shape=1;$shape<=3;$shape++)
            {
                $ret['buttons'][] = 'shape'.$shape;
                $ret['selectable']['shape'.$shape] = array();
            }
        }
        else
        {
            for($icon=1;$icon<=5;$icon++)
            {
                $ret['buttons'][] = 'icon'.$icon;
                $ret['selectable']['icon'.$icon] = array();
            }
        }
        
        $ret['buttons'][] = 'Undo';
        $ret['selectable']['Undo'] = array(); 
        
        return $ret;
    }
    
    function dosecondChoice($parg1, $parg2, $varg1, $varg2 = null)
    {        
       $act = new research();
       $act->worker_id = $this->worker_id;
       $act->extra = $this->extra;
       $act->dochoice($parg1, $parg2, $varg1, $varg2 = null);
    }
    
}