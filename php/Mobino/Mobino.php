<?php

/**
 * A PHP class to integrate the Mobino payment solution
 * 
 * @link http://mobino.com
 * @author Manuel Reinhard <manu@sprain.ch>
 * @version 0.1
 */

class Mobino {

	private $configFile = 'Mobino/settings.ini';
	private $configData = array();
	private $params     = array();

	/**
	 * Constructor
	 * @author Manuel Reinhard <manu@sprain.ch>
	 * @param float $amount
	 * @param string $reference_number
	 * @param string $transaction_type Should be "regular" or "gift"
	 * @param string $env Should be "production" or "staging"
	 * @return bool
	 */
	public function __construct($amount, $reference_number='', $transaction_type='regular', $env='production', $lang = 'en', $country = ''){
	
		//read config file
		if(!$this->readConfig()){return false;}
		
		//save data
		$this->params['amount'] = sprintf('%.2f', $amount);
		$this->params['reference_number']  = $reference_number;
		$this->params['transaction_type']  = $transaction_type;
		$this->params['env'] = $env;
		$this->params['lang'] = $lang;
		$this->params['country'] = $country;

		return true;
	}


	/**
	 * Reads the config file and puts its content into an array.
	 * @author Manuel Reinhard <manu@sprain.ch>
	 * @return bool
	 */
	private function readConfig(){
		
		//Does config file exist?
		if(file_exists($this->configFile)){
		
			//Read config file
			if(function_exists("parse_ini_file")){
				if($this->configData = parse_ini_file($this->configFile, 1)){
					return true;
				}//if
			}//if
			
			//Sometimes parse_ini_file fails due to server settings. Let's try an alternative way!
			if($this->configData = $this->alternativeParseIniFile($this->configFile)){
				return true;
			}//if
			
		}//if
		
		return false;
	}//function
	
	
	/**
	 * An alternative to parse_ini_file()
	 * @author Manuel Reinhard <manu@sprain.ch>
	 * @return mixed
	 */
	private function alternativeParseIniFile($file) {
		$lines = file($file);
		$config = array();
		$arrayKey = "";
		
		foreach ($lines as $line) {
			if(preg_match('/^\[(.*)\]$/', trim($line), $match)){$arrayKey = $match[1]; continue;}
			if(strpos($line, "=")){
		   	 	$vals = explode('=', $line, 2);
		   	 	if($arrayKey != ""){
		   	 		$config[$arrayKey][trim($vals[0])] = str_replace("'", "", trim($vals[1]));
		   	 	}else{
		    		$config[trim($vals[0])] =  str_replace("'", "", trim($vals[1]));
		    	}//if
		    }//if
		}
		
		return $config;
		
	}//function
	
	
	/**
	 * Generates the signature
	 * @author Manuel Reinhard <manu@sprain.ch>
	 * @return string
	 */
	private function getSignature(){
		
		$paramsToHashInCorrectOrder = array(
			'amount'  => $this->params['amount'],
			'api_key' => $this->configData[$this->params['env']]['api_key'],
			'merchant_id' => $this->configData[$this->params['env']]['merchant_id'],
			'reference_number' => $this->params['reference_number'],
			'transaction_type' => $this->params['transaction_type']
		);
		
		//Turn into string
		$stringToHash = '';
		foreach($paramsToHashInCorrectOrder as $key=>$value){
			$stringToHash .= $key.":".$value.",";
		}//foreach
		$stringToHash = substr($stringToHash, 0, -1).$this->configData[$this->params['env']]['api_secret'];
		
		//Hash an return
		return md5($stringToHash);
		
	}
	
	
	/**
	 * Returns HTML code of payment widget
	 * @author Manuel Reinhard <manu@sprain.ch>
	 * @return string
	 */
	public function getWidget(){
		return "<div id='mobino'>\n<script src='http://mobino.com/api/mobino.js'></script>\n"
			."<script>\n"
				.'MobinoLoader.initializer({"env":"'.$this->params['env'].'", "lang": "'.$this->params['lang'].'"'.($this->params['country'] == ""? '' : ' ,"country":"'.$this->params['country'].'"').'});'."\n"
				.'MobinoLoader.createPayment({"amount": "'.$this->params['amount'].'", "merchant_id": '.$this->configData[$this->params['env']]['merchant_id'].', "api_key": "'.$this->configData[$this->params['env']]['api_key'].'", "reference_number": "'.$this->params['reference_number'].'", "signature": "'.$this->getSignature().'", "transaction_type": "'.$this->params['transaction_type'].'"});."\n"
			."</script>\n"
		."</div>\n";
	}

	
}
