<?php

abstract class controller {
		
	protected $registry;
	protected $session;
	protected $cookie;
	protected $pagination;
	protected $l10n;
	protected $html;
	protected $ajax;
	protected $themes;
	protected $view;
	protected $path;
	protected $tfl= "";
	public $action;
	public $params;
	public $data;
	public $isAjax;

	public function __construct() {
		$this->registry = registry::getInstance();
		$this->session = $this->registry["session"];
		$this->cookie = $this->registry["cookie"];
		$this->view = $this->registry["views"];
		$this->themes = $this->registry["themes"];
		$this->path = $this->registry["path"];
		$this->debug = $this->registry["debug"];
		$this->l10n = l10n::getInstance();
		$this->html = html::getInstance();
		$this->ajax = new ajax();
		$this->pagination = pagination::getInstance();
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			$this->data = $_POST;
		} else {
			$this->data = NULL;
		}
		$this->isAjax = $this->isAjax();
	}

	abstract public function index($id=NULL);
		
	public function beforeRender() {}
	public function afterRender() {}
		
	public function redirect($url, $intern = true) {
		$_SESSION["flavor_php_session"]["validateErrors"] = $this->registry->validateErrors;
		
		if ($intern) {
			$url = (!$this->endsWith($url, "/")) ? $url."/" : $url ;
			$url = $this->path.$url;
		} else {
			$url = $url;
		}
		
		header("Location: ".$url);
		exit();
	}
	
	public function render($view=NULL) {
		if($this->html->type == "views"){
			if (is_null($view)) {
				$view = $this->action;
			}
			$this->beforeRender();
			$this->view->content_for_layout = $this->view->fetch($this->controllerName().".".$view);
			$this->view->title_for_layout = $this->tfl;
			echo $this->showDebug().$this->view->fetch("", "layout");
			$this->afterRender();
			$this->debug->clearLogs();
			exit();
		}else{
			$this->renderTheme($this->html->type);
		}
	}
	
	public function renderTheme($theme,$file='index.htm'){
		$this->beforeRender();
		$path = Absolute_Path.APPDIR.DIRSEP.$theme.DIRSEP."$file";
		echo $this->themes->fetch($path);
		$this->afterRender();
		exit;
	}

	public function fetchTheme($theme,$file='index.htm'){
		$path = Absolute_Path.APPDIR.DIRSEP."themes".DIRSEP.$theme.DIRSEP."$file";
		return $this->themes->fetch($path);
	}
	
	protected function title_for_layout($str){
		$this->tfl = $str;
	}
	
	protected function controllerName(){
		$source = get_class($this);
		if(preg_match("/([a-z])([A-Z])/", $source, $reg)){
			$source = str_replace($reg[0], $reg[1]."_".strtolower($reg[2]), $source);
		}	
		
		$controller = explode("_", $source);
		
		return strtolower($controller[0]);
	}
	
	protected function endsWith($str, $sub) {
		return (substr($str, strlen($str) - strlen($sub)) == $sub);
	}
	
	protected function showDebug(){
		if ($this->debug->isEnabled()) {
			return $this->debug->show();
		}else return '';
	}
	
	/*Why private??*/ function isAjax() {
		//var_dump($_SERVER);
		//die();
		return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH']=="XMLHttpRequest");
	} 
	
	/**
	* Magic method to load action helpers and run its init method
	* @param String $helper The helper to run
	* @param Array $args Array of arguments to the method
	* @return Unknown What returned the $method
	*/
	public function __call($helper, $args){
		$helper = new $helper();
		return call_user_func_array(array($helper, 'init'),$args);
	}
	
	/**
	* To call another method beside of 'init' of a helper
	* @param String $helper The name of the helper
	* @param String $method The method to run
<<<<<<< HEAD
=======
	* @param Array $args Array of arguments to the method
>>>>>>> 23a2fb296566f6c76d123fdee48bf55cd155b9c3
	* @return Unknown What returned $helper->$method($args)
	*/
	public function callHelper($helper, $method){
		$helper = new $helper();
		return call_user_func_array(array($helper, $method),array_slice($func_get_args(),2));
	}
}
?>
