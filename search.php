<?php

/**
 * TreasureShip
 * https://cndl.synology.cn/download/Document/Software/DeveloperGuide/Package/DownloadStation/All/enu/DLM_Guide.pdf
 * Developer Notes:
 *
 *  $ts_cloud_url: TPB_TreasureShip cloud api:
 *      >>: format: tpb@<web:url><api:url><trackers:url>
 *      >>: example: tpb@<web:http://tpb.com><api:http://tpb.com/api/q.php?q=><trackers:http://test.com/trackers.txt>
 */

class TreasureShip{
    public $debug = false;
    private $opts = ["ssl" => ["verify_peer"=>false, "verify_peer_name"=>false,]];

    private $default_api_url = "https://apibay.org/q.php?q="; // default
    private $default_api_host = "https://thepiratebay.org"; // default
    private $default_trackers_url = 'https://ngosang.github.io/trackerslist/trackers_best.txt'; // default
    private $default_trackers = '&tr=udp://tracker.openbittorrent.com:6969/announce&tr=udp://tracker.opentrackr.org:1337/announce&tr=udp://opentracker.io:6969/announce&tr=https://1337.abcvg.info:443/announce&tr=http://ipv6.rer.lol:6969/announce';

    #private $ts_cloud_url = "https://raw.githubusercontent.com/malimaliao/Synology-DLM-for-TPB_TreasureShip/main/ts.css";  // cloud
    private $ts_cloud_url = 'https://malimaliao.github.io/Synology-DLM-for-TPB_TreasureShip/ts.css'; // mirror form github, for cloud.

    private $tpb_api = '';
    private $tpb_host='';
    private $trackers_url = '';
    private $trackers_list = '';

    public function __construct(){
    }

    function GetPage($url){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($curl, CURLOPT_TIMEOUT, 20);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36 Edg/114.0.1823.37');
        # curl_setopt($curl,CURLOPT_REFERER,$url);
        $res = curl_exec($curl);
        if (curl_errno($curl)) {
            $res =  curl_error($curl);
        }
        curl_close($curl);
        return $res;
    }

    private function DebugLog($str){
        if ($this->debug == true) {
            file_put_contents('TreasureShip.debug.log', $str, FILE_APPEND);
        }
    }

    // Synology DownloadStation api function
    public function VerifyAccount($username, $password){
        $ret = FALSE;
        $username = trim($username);
        $this->DebugLog("TPB(VerifyAccount username): ".$username.PHP_EOL);
        if ($username != "") {
            $result = file_get_contents($username,false, stream_context_create($this->opts));
            $this->DebugLog("TPB(VerifyAccount username response): ".PHP_EOL.PHP_EOL.$result.PHP_EOL.PHP_EOL);
            if (strpos($result, '0000000000000000000000000000000000000000') !== false) {
                $ret = TRUE;
                $rem = 'ok';
                $this->tpb_api = $username; //update
            }else{
                $rem = 'error';
            }
            $this->DebugLog("TPB(VerifyAccount username echo):" .$rem.PHP_EOL);
        }
        return $ret;
    }

    // Synology DownloadStation api function
    public function prepare($curl, $query, $username, $password)
    {
        $query = trim($query);
        $username = trim($username);
        $password = trim($password);
        # init
        if($this->tpb_api == ''){
            if ($username == "" or $password == "") {
                // get ts cloud b64
                $this->DebugLog("TPB(get start): ".$this->ts_cloud_url.PHP_EOL);
                $tmp = file_get_contents($this->ts_cloud_url,false, stream_context_create($this->opts));
                $this->DebugLog('@html:'.$tmp.PHP_EOL);
                // ts_cloud_api format: $tpb:tpb_api_url@trackers:trackers_url_txt@from:tpb_host@
                if(preg_match('/tpb@<web:(.+?)><api:(.+?)><trackers:(.+?)>/i',$tmp,$data)){
                    $tpb_from = $data[1];
                    $tpb_api = $data[2];
                    $trackers_url = $data[3];
                    $this->DebugLog("TPB(get success, tpb): ".$tpb_api.PHP_EOL);
                    $this->DebugLog("TPB(get success, trackers): ".$trackers_url.PHP_EOL);
                }else{
                    $tpb_api = $this->default_api_url;
                    $trackers_url = $this->default_trackers_url;
                    $tpb_from = $this->default_api_host;
                    $this->DebugLog("TPB(get bad and tpb use default): ".$tpb_api.PHP_EOL);
                    $this->DebugLog("TPB(get bad and trackers use default): ".$trackers_url.PHP_EOL);
                }
            }else{
                // check username_url
                if(preg_match("/http[s]?:\/\/[\w.]+[\w\/]*[\w.]*\??[\w=&\+\%]*/is",$username)){
                    $tpb_api = $username;
                    $this->DebugLog("TPB(tpb replace with username_url): ".$tpb_api.PHP_EOL);
                }else{
                    $tpb_api = $this->default_api_url;
                    $this->DebugLog("TPB(username_url check bad and use default): ".$tpb_api.PHP_EOL);
                }
                # check password_url
                if(preg_match("/http[s]?:\/\/[\w.]+[\w\/]*[\w.]*\??[\w=&\+\%]*/is",$password)){
                    $trackers_url = $password;
                    $this->DebugLog("TPB(trackers replace with password_url): ".$trackers_url.PHP_EOL);
                }else{
                    $trackers_url = $this->default_trackers_url;
                    $this->DebugLog("TPB(password_url check bad and use default): ".$trackers_url.PHP_EOL);
                }
                $tpb_from = $this->default_api_host;
            }
            $this->tpb_api = $tpb_api; // update
            $this->trackers_url = $trackers_url; // update
            $this->tpb_host = $tpb_from; // update
        }
        if($this->trackers_list == ''){
            $this->trackers_list = $this->format_tpb_trackers($this->trackers_url); // update
        }
        # start
        $url = sprintf($this->tpb_api.'%s&cat=', urlencode($query));
        $this->DebugLog($url.PHP_EOL);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($curl, CURLOPT_TIMEOUT, 20);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36 Edg/114.0.1823.37');
    }

    // Synology DownloadStation api function
    public function parse($plugin, $response)
    {
        $response = str_replace(array("\r\n", "\r", "\n"), "", $response); //抛弃所有换行便于正则匹配
        $this->DebugLog(PHP_EOL.$response.PHP_EOL.PHP_EOL);

        $items = json_decode($response,true);
        $res = 0;
        if($items != null){
            foreach ($items as $item){
                $title = $item['name'];
                $hash = $item['info_hash'];
                $leechers = $item['leechers'];
                $seeders = $item['seeders'];
                $datetime = date('Y-m-d H:i:s',(int)$item['added']);
                $size = (int)$item['size'];
                $category = $this->format_tpb_category($item['category']);
                $page = $this->tpb_host."/description.php?id=".$item['id'];
                $download = "magnet:?xt=urn:btih:".$hash;
                $download .= '&dn='.urlencode($title);
                $download .= $this->trackers_list;

                # push Plugin
                if ($title != "") {
                    if ($this->debug == true) {
                        // out debug
                        $this->DebugLog($title.'|'.$download.'|'.$size.'|'.$datetime.'|'.$page.'|'.$hash.'|'.$seeders.'|'.$leechers.'|'.$category.PHP_EOL);
                    }else{ // out plugin
                        $plugin->addResult($title, $download, $size, $datetime, $page, $hash, $seeders, $leechers, $category);
                    }
                    $res++;
                }
            }
        }
        return $res;
    }

    function format_tpb_category($category_code){
        switch ($category_code){
            case '1':
                $category = 'Audio';
                break;
            case '2':
                $category = 'Video';
                break;
            case '3':
                $category = 'Video';
                break;
            case '4':
                $category = 'Games';
                break;
            case '5':
                $category = 'Porn';
                break;
            case '6':
                $category = 'Other';
                break;
            case '101':
                $category =  'Music';
                break;
            case '102':
                $category =  'Audio Books';
                break;
            case '103':
                $category =  'Sound clips';
                break;
            case '104':
                $category =  'FLAC';
                break;
            case '199':
                $category =  'Other';
                break;
            case '201':
                $category =  'Movies';
                break;
            case '202':
                $category =  'Movies DVDR';
                break;
            case '203':
                $category =  'Music videos';
                break;
            case '204':
                $category =  'Movie Clips';
                break;
            case '205':
                $category =  'TV-Shows';
                break;
            case '206':
                $category =  'Handheld';
                break;
            case '207':
                $category =  'HD Movies';
                break;
            case '208':
                $category =  'HD TV-Shows';
                break;
            case '209':
                $category =  '3D';
                break;
            case '299':
                $category =  'Other';
                break;
            case '301':
                $category =  'Windows';
                break;
            case '302':
                $category =  'Mac/Apple';
                break;
            case '303':
                $category =  'UNIX';
                break;
            case '304':
                $category =  'Handheld';
                break;
            case '305':
                $category =  'IOS(iPad/iPhone)';
                break;
            case '306':
                $category =  'Android';
                break;
            case '399':
                $category =  'Other OS';
                break;
            case '401':
                $category =  'PC';
                break;
            case '402':
                $category =  'Mac/Apple';
                break;
            case '403':
                $category =  'PSx';
                break;
            case '404':
                $category =  'XBOX360';
                break;
            case '405':
                $category =  'Wii';
                break;
            case '406':
                $category =  'Handheld';
                break;
            case '407':
                $category =  'IOS(iPad/iPhone)';
                break;
            case '408':
                $category =  'Android';
                break;
            case '499':
                $category =  'Other OS';
                break;
            case '501':
                $category =  'Movies';
                break;
            case '502':
                $category =  'Movies DVDR';
                break;
            case '503':
                $category =  'Pictures';
                break;
            case '504':
                $category =  'Games';
                break;
            case '505':
                $category =  'HD-Movies';
                break;
            case '506':
                $category =  'Movie Clips';
                break;
            case '599':
                $category =  'Other';
                break;
            case '601':
                $category =  'E-books';
                break;
            case '602':
                $category =  'Comics';
                break;
            case '603':
                $category =  'Pictures';
                break;
            case '604':
                $category =  'Covers';
                break;
            case '605':
                $category =  'Physibles';
                break;
            case '699':
                $category =  'Other';
                break;
            default:
                $category = '';
        }
        return $category;
    }

    function format_tpb_trackers($trackers_cloud_url){
        $trackers = urlencode($this -> default_trackers);
        if(preg_match("/http[s]?:\/\/[\w.]+[\w\/]*[\w.]*\??[\w=&\+\%]*/is",$trackers_cloud_url)){
            $result = file_get_contents($trackers_cloud_url,false, stream_context_create($this->opts));
            if (strpos($result, 'announce') !== false) {
                $lines = preg_split('/[\s]+/',$result);
                if(count($lines)>0){
                    $trackers = ''; // ret
                    foreach ($lines as $line){
                        if(trim($line) != ''){
                            $trackers .= '&tr='.urlencode(trim($line));
                        }
                    }
                }
            }
        }
        $this->DebugLog(PHP_EOL.PHP_EOL.$trackers.PHP_EOL.PHP_EOL);
        return $trackers;
    }

}
