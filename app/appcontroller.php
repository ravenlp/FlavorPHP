<?php

abstract class AppController extends Controller {
	
	protected $messages;
	
	public function __construct() {
		parent::__construct();
		
		// Instancia de la libreria de mensajes
		$this->messages = Message::getInstance();
	}
	
	public function beforeRender(){
	//	$this->debug->log('Pase por '.get_class($this).'<br>','Route',true);
	}
}
