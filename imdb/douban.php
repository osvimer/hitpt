<?
class douban {
	var $doubanxml,$dbarray;
	var $dbinfo;
	var $cachepath = "",$siteurl = "",$apikey = "";
	function __construct() {
		$this->cachepath = "./imdb/cache/";
		$this->imagepath = "./imdb/images/";
		$this->apikey = "03aa7b16be6307e40aff71443b2917ac";
   	}

	function __destruct() {
		xml_parser_free($xmlparser);
   	}
	function prinfo(){
		$page = "";
		$page .= "资源名称：";
		$page .= $this->dbinfo['name'];
		$page .="（";
		foreach($this->dbinfo['aka'] as $key => $value){
			if($value == $this->dbinfo['aka'][0] && $key !=0)
				;
			else
				if($key > 0)
					$page .="，".$value;
				else
					$page .= $value;
		}
		$page .="）";
		$page .="<br />";
		$page .="主要演员：";
		foreach($this->dbinfo['author'] as $key => $value){
			if($key > 0)
				$page .="，".$value;
			else
				$page .= $value;
		}
		$page .="<br />";
		$page .="其他演员：";
		foreach($this->dbinfo['cast'] as $key => $value){
			if($key > 0)
				$page .="，".$value;
			else
				$page .= $value;
		}
		$page .="<br />";
		$page .="电影类型：";
		foreach($this->dbinfo['movie_type'] as $key => $value){
			if($key > 0)
				$page .="，".$value;
			else
				$page .= $value;
		}
		$page .="<br />";
		$page .="电影语言：";
		foreach($this->dbinfo['language'] as $key => $value){
			if($key > 0)
				$page .="，".$value;
			else
				$page .= $value;
		}
		$page .="<br />";
		$page .="产　　地：";
		$page .=$this->dbinfo[country][0];
		$page .="<br />";
		$page .="导　　演：";
		$page .=$this->dbinfo[director][0];
		$page .="<br />";
		$page .="发布时间：";
		$page .=$this->dbinfo[pubdate][0];
		$page .="<br />";
		$page .="电影时长：";
		$page .=$this->dbinfo[movie_duration][0];
		$page .="<br />";
		$page .="豆瓣标签：";
		foreach($this->dbinfo['tag'] as $key => $value){
			if($key > 0)
				$page .="，".$value;
			else
				$page .=$value;
		}
		$page .="<br />";
		$page .="豆瓣评分：";
		$page .=$this->dbinfo[rating];
		$page .="<br />";
		$page .= "豆瓣链接：";
		$page .= "<a href=\"".$this->dbinfo[link][alternate]."\" target=\"_blank\">".$this->dbinfo[link][alternate]."</a>";
		$page .="<br />";
		$page .="简介：";
		$page .=$this->dbinfo[summary];
		return $page;
	}
	function init(){
		foreach($this->dbarray as $db){
			switch($db["tag"]){
				case "DB:TAG":
					$this->dbinfo["tag"][] = $db["attributes"]["NAME"];
					break;
				case "DB:ATTRIBUTE":
					$this->dbinfo[$db["attributes"]["NAME"]][] = $db["value"];
					break;
				case "LINK":
					$this->dbinfo["link"][$db["attributes"]["REL"]] = $db["attributes"]["HREF"];
					break;
				case "TITLE":
					$this->dbinfo["name"] = $db["value"];
					break;
				case "NAME":
					$this->dbinfo["author"][] = $db["value"];
					break;
				case "SUMMARY":
					$this->dbinfo["summary"] = $db["value"];
					break;
				case "GD:RATING":
					$this->dbinfo["rating"] = $db["attributes"]["AVERAGE"];
					break;
				default:
					break;
			}
		}
	}
	function setid($imdb_id = 0,$type = "imdb"){
		if($type == "imdb")
			$this->siteurl = "http://api.douban.com/movie/subject/imdb/tt";
		else if($type == "douban")
			$this->siteurl = "http://api.douban.com/movie/subject/";

		if(file_exists($this->cachepath.$imdb_id.".xml")){
			$this->doubanxml = file_get_contents($this->cachepath.$imdb_id.".xml");
		}else{
			$this->doubanxml = file_get_contents($this->siteurl.$imdb_id."?apikey=".$this->apikey);
			$this->change_spic_to_mpic();
			file_put_contents($this->cachepath.$imdb_id.".xml",$this->doubanxml);
		}
		$xmlparser = xml_parser_create();
		xml_parse_into_struct($xmlparser,$this->doubanxml,$this->dbarray);
		$this->init();
		file_put_contents($this->cachepath.$imdb_id.".page",$this->prinfo());
		@ copy($this->dbinfo[link][image],$this->imagepath.$imdb_id.".jpg");
	}
	function change_spic_to_mpic()
	{
		$pattern = '/(\shref=.*\/)spic\//i';
		$this->doubanxml = preg_replace($pattern, "$1"."mpic/", $this->doubanxml);	
	}
}
?>
