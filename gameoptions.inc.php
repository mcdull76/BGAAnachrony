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
 * gameoptions.inc.php
 *
 * anachrony game options description
 * 
 * In this file, you can define your game options (= game variants).
 *   
 * Note: If your game has no variant, you don't have to modify this file.
 *
 * Note²: All options defined in this file should have a corresponding "game state labels"
 *        with the same ID (see "initGameStateLabels" in anachrony.game.php)
 *
 * !! It is not a good idea to modify this file when a game is running !!
 *
 */

$game_options = array(

    100 => array(
        'name' => totranslate('Player board side'),
        'values' => array(
            1 => array( 'name' => totranslate('Symetric A Side'), 'description' => totranslate('Play with identical player boards') ),
            2 => array( 'name' => totranslate('Asymetric B Side'), 'description' => totranslate('Play with board that better reflects your strengths and weaknesses'), 'premium' => true, 'nobeginner' => true ),
        ),
        'default' => 1
    ),
    
    101 => array(
        'name' => totranslate('Path selection'),
        'values' => array(
            1 => array( 'name' => totranslate('Automatic'), 'description' => totranslate('Paths are automatically assigned') ),
            2 => array( 'name' => totranslate('Manual'), 'premium' => true, 'description' => totranslate('Players choose their path') ),
        ),
        'default' => 1
    ),

);


