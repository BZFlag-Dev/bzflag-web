<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of about
 *
 * @author ian
 */
class AboutbzstatsController extends \Qore\Controller {
    
    public function __pre() {
        parent::__pre();
        $this->setExecutionState(true);
    }
    
    public function main_public(array $args) {
        echo $this->twig->render('bzstatsAbout.html.twig');
    }
}