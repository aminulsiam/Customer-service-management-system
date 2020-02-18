<?php 
if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.
class Mage_Shortcode_Hello{
	public function __construct(){
		add_shortcode('hello-mage',array($this,'mage_shortcode_hello'));
	}

	public function mage_shortcode_hello($atts, $content=null){
		$defaults = array(
			"cat"					=> "0"
		);
		$params 					= shortcode_atts($defaults, $atts);
		$cat						= $params['cat'];
// ob_start();
		echo 'Hello World';
	}
}
new Mage_Shortcode_Hello();