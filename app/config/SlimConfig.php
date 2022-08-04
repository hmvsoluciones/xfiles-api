<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SlimConfig
 *
 * @author SISTEMAS
 */
class SlimConfig {

    private $slimConfiguration;

    function __construct() {
        $this->slimConfiguration = [
            'settings' => [
                'displayErrorDetails' => true,
            ],
        ];
    }

    function getSlimConfiguration() {
        return $this->slimConfiguration;
    }

    function setSlimConfiguration($slimConfiguration) {
        $this->slimConfiguration = $slimConfiguration;
    }

}
