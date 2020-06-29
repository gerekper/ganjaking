<?php


class FUE_User_Agent{

	private $string = NULL;
	private $parsed = NULL;

	public function __construct($string = NULL){

		$this->string = (is_null($string)) ? $this->get_user_agent() : $string;

	}

	public function get(){
		return $this->get_parsed();
	}

	private function get_user_agent(){
		return isset($_SERVER['HTTP_USER_AGENT']) ? trim($_SERVER['HTTP_USER_AGENT']) : '';
	}

	private function get_client(){
		if(!$this->string) return '';
		$this->parse();

		return $this->parsed->client;

	}
	private function get_version(){
		if(!$this->string) return '';
		$this->parse();

		return $this->parsed->version;

	}

	private function get_parsed(){
		if(!$this->string) return '';
		$this->parse();

		return $this->parsed;

	}

	private function parse(){
		if($this->parsed) return $this->parsed;

		$object = new StdClass;

		if(preg_match('# Thunderbird/([0-9a-z.]+)#', $this->string, $hit)){
			$object->client = 'Thunderbird';
			$object->version = ($hit[1]);
			$object->type = 'desktop';

		}else if(preg_match('#Airmail ([0-9a-z.]+)#', $this->string, $hit)){
			$object->client = 'Airmail';
			$object->version = $hit[1];
			$object->type = 'desktop';

		}else if(preg_match('# ANDROIDGMAILAPP#', $this->string, $hit)){
			$object->client = 'Gmail App (Android)';
			$object->version = '';
			$object->type = 'mobile';

		}else if(preg_match('# GoogleImageProxy#', $this->string, $hit)){
			$object->client = 'Gmail';
			$object->version = '';
			$object->type = 'webmail';

		}else if(preg_match('#(iPod|iPod touch).*OS ([0-9_]+)#i', $this->string, $hit)){
			$object->client = 'iPod Touch';
			$object->version = 'iOS '.intval($hit[2]);
			$object->type = 'mobile';

		}else if(preg_match('#(iPhone|iPad).*OS ([0-9_]+)#', $this->string, $hit)){
			$object->client = $hit[1];
			$object->version = 'iOS '.intval($hit[2]);
			$object->type = 'mobile';

		}else if(preg_match('#(Android|BlackBerry|Windows Phone OS) ([0-9.]+)#', $this->string, $hit)){
			$object->client = $hit[1];
			$object->version = $hit[2];
			$object->type = 'mobile';

		}else if(preg_match('#(Kindle Fire|Kindle|IEMobile)/([0-9a-z.]+)#', $this->string, $hit)){
			$object->client = $hit[1];
			$object->version = $hit[2];
			$object->type = 'mobile';

		}else if(preg_match('#(Sparrow|Postbox|Eudora|Lotus-Notes|Shredder)/([0-9a-z.]+)#', $this->string, $hit)){
			$object->client = str_replace('-', ' ',$hit[1]);
			$object->version = $hit[2];
			$object->type = 'desktop';

		}else if(preg_match('#Outlook-Express/7\.0 \(MSIE ([0-9a-z.]+)#', $this->string, $hit)){
			$object->client = 'Windows Live Mail';
			$object->version = $hit[1];
			$object->type = 'desktop';

		}else if(preg_match('#Outlook-Express/6\.0#', $this->string, $hit)){
			$object->client = 'Outlook Express';
			$object->version = '6.0';
			$object->type = 'desktop';

		}else if(preg_match('#(MSAppHost)/([0-9.]+)#', $this->string, $hit)){
			$object->client = 'Windows Live Mail';
			$object->version = '';
			$object->type = 'desktop';

		}else if(preg_match('# (Microsoft Outlook|MSOffice) ([0-9]+)#', $this->string, $hit)){
			$object->client = 'Microsoft Outlook';
			$version = intval($hit[2]);
			switch($version){
				case 12: $object->version = 2007; break;
				case 14: $object->version = 2010; break;
				case 15: $object->version = 2013; break;
				default: $object->version = $hit[2];
			}
			$object->type = 'desktop';

		}else if(preg_match('#(Chrome|Safari|Firefox|Opera)/([0-9a-z.]+)#', $this->string, $hit)){
			$object->client = 'Web Client ('.$hit[1].')';
			$object->version = '';
			$object->type = 'webmail';

		}else if(preg_match('# Trident/.* rv:([0-9a-z.]+)#', $this->string, $hit)){
			$object->client = 'Web Client (Internet Explorer '.intval($hit[1]).')';
			$object->version = '';
			$object->type = 'webmail';

		}else if(preg_match('#MSIE ([0-9.]+).* Trident/#', $this->string, $hit)){
			$version = intval($hit[1]);
			if($version <= 7){ //most likly Outlook 2000-2003
				$object->client = 'Microsoft Outlook';
				$object->version = '2000-2003';
				$object->type = 'desktop';
			}else{
				$object->client = 'Web Client (Internet Explorer '.$version.')';
				$object->version = '';
				$object->type = 'webmail';
			}

		}else if(preg_match('# AppleWebKit/([0-9a-z.]+)#', $this->string, $hit)){
			if(preg_match('#Mac OS X 10_(\d+)#', $this->string, $versionhit)){
				$object->client = 'Apple Mail';
				$object->version = $versionhit[1]-2;
				$object->type = 'desktop';
			}else{
				$object->client = 'Web Client (WebKit based)';
				$object->version = $hit[1];
				$object->type = 'webmail';
			}

		}else if(preg_match('#Mozilla/([0-9a-z.]+)#', $this->string, $hit)){
			$object->client = 'Web Client (Mozilla based)';
			$object->version = $hit[1];
			$object->type = 'webmail';

		}else{
			$object->client = 'Web Client (unknown)';
			$object->version = '';
			$object->type = 'webmail';

		}

		$this->parsed = $object;

	}



}