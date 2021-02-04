                               <?PHP
class config{


	public $HostDB = "localhost";
	public $UserDB = "acp";
	public $PassDB = "proton";
	public $BaseDB = "nw";

	

	
	public $URL_ID_WM_LOGIN = "";

	public $httpreferer = "";

	public $https = false; 

	public $url = "";

	public $url_avtoserfLink = "";

	public function __construct(){


		$this->url = $this->https ? "https://serfnets.ru/" : "http://serfnets.ru/";

		$this->url_smartlink = $this->url;

		$this->url_avtoserfLink = $this->https ? "http://quarantinebux.site/" : "http://quarantinebux.site/";

		$this->URL_ID_WM_LOGIN = strtolower("482F5515-2BD7-424B-AA37-AC70009E0187");
                //$this->URL_ID_WM_LOGIN = strtolower("45B5307D-B676-47CD-94C5-A8D8012C5CB3");


		
	}

}
?> 
                            