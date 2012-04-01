<?php

class SessiontestController extends \Qore\Controller {
    public function __pre() {
        parent::__pre();
        $this->setExecutionState(true);
    }
    
    public function main_public(array $args) {
        if (!$this->session->varIsSet('test', 'var')) {
            $this->session->set('test', 'var', 1);
        } else {
            $this->session->set('test', 'var', $this->session->get('test', 'var') + 1);
        }
        
        var_dump($_SESSION);
        
        echo("Session ID: " . $this->session->getId());
        
         echo ("<hr />");
        
        //our data container that we will pass the view
        $data = array();
        
        echo $this->twig->render('bzstatsServers.html.twig', array('data' => $data));
    }
}