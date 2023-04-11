<?php
/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * anachrony implementation : © <Nicolas Gocel> <nicolas.gocel@gmail.com>
 * 
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * material.inc.php
 *
 * anachrony game material description
 *
 * Here, you can describe the material of your game with PHP variables.
 *   
 * This file is loaded in your game logic class constructor, ie these variables
 * are available everywhere in your game logic code.
 *
 */

if (!defined('SIDEA')) {
   
    define('SIDEA', 1);
    define('SIDEB', 2);
    
    define('ADMIN', 1);
    define('ENGINEER', 2);
    define('GENIUS', 3);
    define('SCIENTIST', 4);
    
    define('NEUTRONIUM', 1);
    define('GOLD', 2);
    define('URANIUM', 3);
    define('TITANIUM', 4);
    define('WATER', 5);
    define('ENERGY',6);
    
    define('N', 10);
    define('G', 100);
    define('U', 1000);
    define('T', 10000);
    define('W', 100000);
    define('E', 1000000);
    define('AD', 10000000);
    define('EN', 100000000);
    define('GE', 1000000000);
    define('SC', 10000000000);
    define('EX', 100000000000);
    define('VP', 1000000000000);
    define('P',  10000000000000);
    define('M',  100000000000000);
    
    define('ANOMALY',515);
}

$this->cards_mine = array(
    1 => [GOLD,URANIUM,URANIUM,URANIUM,TITANIUM],
    2 => [NEUTRONIUM, GOLD, URANIUM,TITANIUM,TITANIUM],
    3 => [NEUTRONIUM, GOLD, GOLD,URANIUM,TITANIUM],
    4 => [NEUTRONIUM, GOLD, URANIUM,URANIUM,TITANIUM],
    5 => [NEUTRONIUM, NEUTRONIUM, GOLD, TITANIUM,TITANIUM],
    6 => [NEUTRONIUM, NEUTRONIUM, URANIUM,URANIUM,URANIUM],
    7 => [GOLD, GOLD, GOLD, URANIUM,TITANIUM],
    8 => [GOLD, GOLD, URANIUM,TITANIUM,TITANIUM],
    9 => [GOLD, GOLD, URANIUM,URANIUM,TITANIUM],
    10 => [GOLD, URANIUM,TITANIUM,TITANIUM, TITANIUM],
    11 => [NEUTRONIUM, GOLD, TITANIUM,TITANIUM,TITANIUM]
);

$this->cards_worker = array(
    1 => [1,0,1,2],
    2 => [2,1,0,1],
    3 => [1,2,1,0],
    4 => [2,1,0,1],
    5 => [2,0,1,1],
    6 => [0,2,0,2],
    7 => [1,1,1,1],
    8 => [0,1,1,2],
    9 => [0,2,1,1],
    10 => [0,2,2,0],
    11 => [1,0,2,1] 
);

$this->trades = array(
    3*W => [E,2*T,2*U,2*G,T+U,T+G,U+G],
    E => [3*W, N],
    N => [E,2*T,2*U,2*G, T+U,T+G,U+G],
    2*T => [3*W,N],
    2*U => [3*W,N],
    2*G => [3*W,N],
    T+U => [3*W,N],
    T+G => [3*W,N],
    U+G => [3*W,N]
);

$this->tradesSP = array(
    N => [2*T,2*U,2*G, T+U,T+G,U+G],
    2*T => [N],
    2*U => [N],
    2*G => [N],
    T+U => [N],
    T+G => [N],
    U+G => [N]
);

$this->vortexs = array(
    1 => [0,N],
    2 => [0,U],
    3 => [0,T],
    4 => [0,G],
    5 => [0,2*W],
    6 => [W,AD],
    7 => [W,EN],
    8 => [W,SC],
    9 => [0,EX]
);

$this->paradoxes = [0,1,1,1,1,2];

$this->actions = ["research","construct","recruit","mine", "council", "purify", "trade", "supply", "force", "leader1", "leader3", "evacuation"];

$this->player_colors = [1=>"883405", 2=>"fabb20", 3=>"637b23", 4=>"1a355e"];
 