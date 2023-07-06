<?php

/**
 * TreasureShip
 * https://cndl.synology.cn/download/Document/Software/DeveloperGuide/Package/DownloadStation/All/enu/DLM_Guide.pdf
 * Developer Notes:
 *  $tpb_api_b64: tpb default host ( use base64 encode)
 *  $tpb_get_b64: If no username mapping host is defined, the program obtains it from this source and parses the output in base64.
 */

class TreasureShip{
    public $debug = false;
    private $opts = ["ssl" => ["verify_peer"=>false, "verify_peer_name"=>false,]];

    private $tpb_api_b64 = "aHR0cHM6Ly9hcGliYXkub3JnL3EucGhwP3E9";
    private $trackers_url_b64 = 'aHR0cHM6Ly9uZ29zYW5nLmdpdGh1Yi5pby90cmFja2Vyc2xpc3QvdHJhY2tlcnNfYmVzdC50eHQ=';
    private $ts_cloud_url = "https://raw.githubusercontent.com/malimaliao/Synology-DLM-for-TPB_TreasureShip/main/ts.css";
    
    private $tpb_api = '';
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
        curl_setopt($curl,CURLOPT_REFERER,$url);
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
        # init
        if($this->tpb_api == ''){
            if ($username == "" or $password == "") { // get ts cloud b64
                $this->DebugLog("TPB(get start): ".$this->ts_cloud_url.PHP_EOL);
                $tmp = file_get_contents($this->ts_cloud_url,false, stream_context_create($this->opts));
                $this->DebugLog('@html:'.$tmp.PHP_EOL);
                // ts_cloud_api format: $tpb:tpb_api_b64@trackers:trackers_url_b64$
                if(preg_match('/\$tpb:(.+?)@trackers:(.+?)@/i',$tmp,$data)){
                    $tpb_api = base64_decode($data[1]);
                    $trackers_url = base64_decode($data[2]);
                    $this->DebugLog("TPB(get success, tpb): ".$tpb_api.PHP_EOL);
                    $this->DebugLog("TPB(get success, trackers): ".$trackers_url.PHP_EOL);
                }else{
                    $tpb_api = base64_decode($this->tpb_api_b64);
                    $trackers_url = base64_decode($this->trackers_url_b64);
                    $this->DebugLog("TPB(get bad and tpb use default): ".$tpb_api.PHP_EOL);
                    $this->DebugLog("TPB(get bad and trackers use default): ".$trackers_url.PHP_EOL);
                }
            }else{
                // use custom username and password replace the tpb&trackers
                if(preg_match("/http[s]?:\/\/[\w.]+[\w\/]*[\w.]*\??[\w=&\+\%]*/is",$username)){
                    $tpb_api = $username;
                    $this->DebugLog("TPB(tpb replace with username_url): ".$tpb_api.PHP_EOL);
                }else{
                    $tpb_api = base64_decode($this->tpb_api_b64);
                    $this->DebugLog("TPB(username_url check bad and use default): ".$tpb_api.PHP_EOL);
                }
                if(preg_match("/http[s]?:\/\/[\w.]+[\w\/]*[\w.]*\??[\w=&\+\%]*/is",$password)){
                    $trackers_url = $password;
                    $this->DebugLog("TPB(password_url check bad and use default): ".$trackers_url.PHP_EOL);
                }else{
                    $trackers_url = base64_decode($this->trackers_url_b64);
                    $this->DebugLog("TPB(get bad and trackers use default): ".$trackers_url.PHP_EOL);
                }
            }
            $this->tpb_api = $tpb_api; // update
        }
        if($this->trackers_list == ''){
            $this->trackers_list = $this->format_tpb_trackers($password); // update
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
                $page = "https://pirate-proxy.click/description.php?id=".$item['id'];
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

    function format_tpb_trackers($password_url){
        $trackers = '&tr=udp%3A%2F%2Ftracker.coppersurfer.tk%3A6969%2Fannounce&tr=udp%3A%2F%2Ftracker.openbittorrent.com%3A6969%2Fannounce&tr=udp%3A%2F%2F9.rarbg.to%3A2710%2Fannounce&tr=udp%3A%2F%2F9.rarbg.me%3A2780%2Fannounce&tr=udp%3A%2F%2F9.rarbg.to%3A2730%2Fannounce&tr=udp%3A%2F%2Ftracker.opentrackr.org%3A1337&tr=http%3A%2F%2Fp4p.arenabg.com%3A1337%2Fannounce&tr=udp%3A%2F%2Ftracker.torrent.eu.org%3A451%2Fannounce&tr=udp%3A%2F%2Ftracker.tiny-vps.com%3A6969%2Fannounce&tr=udp%3A%2F%2Fopen.stealth.si%3A80%2Fannounce';
        if(preg_match("/http[s]?:\/\/[\w.]+[\w\/]*[\w.]*\??[\w=&\+\%]*/is",$password_url)){
            $result = file_get_contents($password_url,false, stream_context_create($this->opts));
            if (strpos($result, 'announce') !== false) {
                $lines = preg_split('/[\s]+/',$result);
                if(count($lines)>0){
                    $trackers = ''; // ret
                    foreach ($lines as $line){
                        $trackers .= '&tr='.urlencode($line);
                    }
                }
            }
        }
        $this->DebugLog(PHP_EOL.PHP_EOL.$trackers.PHP_EOL.PHP_EOL);
        return $trackers;
    }

}
