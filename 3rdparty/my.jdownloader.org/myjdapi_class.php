<?php

/*
 * My.jdownloader.org API class definition
 * https://github.com/tofika/my.jdownloader.org-api-php-class
 *
 * @author Anatoliy Kultenko "tofik"
 * @license BSD http://opensource.org/licenses/BSD-3-Clause
 */

class MYJDAPI
{
    private $api_url = "http://api.jdownloader.org";
    private $version = "1.0.29092020";
    private $rid_counter;
    private $appkey = "MYJDAPI_php";
    private $apiVer = 1;
    private $loginSecret;
    private $deviceSecret;
    private $sessiontoken;
    private $regaintoken;
    private $serverEncryptionToken;
    private $deviceEncryptionToken;
    private $SERVER_DOMAIN = "server";
    private $DEVICE_DOMAIN = "device";

    public function __construct( $email = "", $password = "") {
        $this -> rid_counter = time();
        if( ($email != "") && ($password != "")) {
            $res = $this -> connect( $email, $password);
            if( $res === false) {
                return false;
            }
        }
    }

    public function getVersion() {
        return $this -> version;
    }

    // Connect to api.jdownloader.org
    // if success - setup loginSecret, deviceSecret, sessiontoken, regaintoken, serverEncryptionToken, deviceEncryptionToken
    // input: email, password
    // return: true or false
    public function connect( $email, $password) {
        $this -> loginSecret = $this -> createSecret( $email, $password, $this -> SERVER_DOMAIN);
        $this -> deviceSecret = $this -> createSecret( $email, $password, $this -> DEVICE_DOMAIN);
        $query = "/my/connect?email=".urlencode( $email)."&appkey=".urlencode( $this -> appkey);
        $res = $this -> callServer( $query, $this -> loginSecret);
        if( $res === false) {
            return false;
        }
        $content_json = json_decode( $res, true);
        $this -> sessiontoken = $content_json["sessiontoken"];
        $this -> regaintoken = $content_json["regaintoken"];
        $this -> serverEncryptionToken = $this -> updateEncryptionToken( $this -> loginSecret, $this -> sessiontoken);
        $this -> deviceEncryptionToken = $this -> updateEncryptionToken( $this -> deviceSecret, $this -> sessiontoken);
        return true;
    }

    // Reconnect to api.jdownloader.org
    // if success - setup sessiontoken, regaintoken, serverEncryptionToken, deviceEncryptionToken
    // return: true or false
    public function reconnect() {
        $query = "/my/reconnect?appkey=".urlencode( $this -> appkey)."&sessiontoken=".urlencode( $this -> sessiontoken)."&regaintoken=".urlencode( $this -> regaintoken);
        $res = $this -> callServer( $query, $this -> serverEncryptionToken);
        if( $res === false) {
            return false;
        }
        $content_json = json_decode( $res, true);
        $this -> sessiontoken = $content_json["sessiontoken"];
        $this -> regaintoken = $content_json["regaintoken"];
        $this -> serverEncryptionToken = $this -> updateEncryptionToken( $this -> serverEncryptionToken, $this -> sessiontoken);
        $this -> deviceEncryptionToken = $this -> updateEncryptionToken( $this -> deviceSecret, $this -> sessiontoken);
        return true;
    }

    // Disconnect from api.jdownloader.org
    // if success - cleanup sessiontoken, regaintoken, serverEncryptionToken, deviceEncryptionToken
    // return: true or false
    public function disconnect() {
        $query = "/my/disconnect?sessiontoken=".urlencode( $this -> sessiontoken);
        $res = $this -> callServer( $query, $this -> serverEncryptionToken);
        if( $res === false) {
            return false;
        }
        $content_json = json_decode( $res, true);
        $this -> sessiontoken = "";
        $this -> regaintoken = "";
        $this -> serverEncryptionToken = "";
        $this -> deviceEncryptionToken = "";
        return true;
    }
    
    public function start($deviceId) {
        $res = $this -> callAction( "/toolbar/startDownloads", $deviceId);
        if( $res === false) {
            return false;
        }
        return $res;
    }
    
    public function stop($deviceId) {
        $res = $this -> callAction( "/toolbar/stopDownloads", $deviceId);
        if( $res === false) {
            return false;
        }
        return $res;
    }
    
    public function restart($deviceId) {
        $res = $this -> callAction( "/system/restartJD", $deviceId);
        if( $res === false) {
            return false;
        }
        return $res;
    }
    
    public function pause($deviceId) {
        $res = $this -> callAction( "/toolbar/togglePauseDownloads", $deviceId);
        if( $res === false) {
            return false;
        }
        return $res;
    }
    
    public function getSystemInfos($deviceId) {
        $res = $this -> callAction( "/system/getSystemInfos", $deviceId);
        if( $res === false) {
            return false;
        }
        return $res;
    }
    
    public function restartJD($deviceId) {
        $res = $this -> callAction( "/system/restartJD", $deviceId);
        if( $res === false) {
            return false;
        }
        return $res;
    }
    
    public function getCoreRevision($deviceId) {
        $res = $this -> callAction( "/jd/getCoreRevision", $deviceId);
        if( $res === false) {
            return false;
        }
        return $res;
    }
    
    public function getCoreVersion($deviceId) {
        $res = $this -> callAction( "/jd/version", $deviceId);
        if( $res === false) {
            return false;
        }
        return $res;
    }
    
    public function getUptime($deviceId) {
        $res = $this -> callAction( "/jd/uptime", $deviceId);
        if( $res === false) {
            return false;
        }
        return $res;
    }
	
	public function isUpdateAvailable($deviceId) {
        $res = $this -> callAction( "/update/isUpdateAvailable", $deviceId);
        if( $res === false) {
            return false;
        }
        return $res;
    }
	
	public function getStatusToolbar($deviceId) {
        $res = $this -> callAction( "/toolbar/getStatus", $deviceId);
        if( $res === false) {
            return false;
        }
        return $res;
    }

    // Enumerate Devices connected to my.jdownloader.org
    // return: devices or false
    public function enumerateDevices() {
        $query = "/my/listdevices?sessiontoken=".urlencode( $this -> sessiontoken);
        $res = $this -> callServer( $query, $this -> serverEncryptionToken);
        if( $res === false) {
            return false;
        }
        return $res;
    }

    // Call action "/device/getDirectConnectionInfos" for each devices
    // return: connection infos or false
    public function getDirectConnectionInfos($deviceId) {
        $res = $this -> callAction( "/device/getDirectConnectionInfos", $deviceId);
        if( $res === false) {
            return false;
        }
        return $res;
    }

    // Send links to device using action /linkgrabberv2/addLinks
    // input: device - name of device, links - array or string of links, package_name - custom package name
    // {"url":"/linkgrabberv2/addLinks",
    //  "params":["{\"priority\":\"DEFAULT\",\"links\":\"YOURLINK\",\"autostart\":autostart, \"packageName\": \"YOURPKGNAME\"}"],
    //  "rid":YOURREQUESTID,"apiVer":1}
    public function addLinks( $links, $deviceId, $autostart, $package_name = null) {
        if( is_array( $links)) {
            $links = implode( ",", $links);
        }
        $params = '\"priority\":\"DEFAULT\",\"links\":\"'.$links.'\",\"autostart\":\"'.$autostart.'\", \"packageName\": \"'.$package_name.'\"';
        $res = $this -> callAction( "/linkgrabberv2/addLinks", $deviceId, $params);
        if( $res === false) {
            return false;
        }
        return $res;
    }

    // Retrive links
    public function queryLinksFromCollector( $deviceId, $params = []) {
        //taken from: https://docs.google.com/document/d/1IGeAwg8bQyaCTeTl_WyjLyBPh4NBOayO0_MAmvP5Mu4/edit# (LinkQueryStorable)
        $params_default = [
            "availability" => true,
            "bytesTotal" => true,
            "comment" => true,
            "enabled" => true,
            "host" => true,
            "maxResults" => -1,
            "priority" => true,
            "startAt" => 0,
            "status" => true,
            "url" => true,
            "variantID" => true,
            "variantIcon" => true,
            "variantName" => true,
            "variants" => true
        ];

        $params = array_merge( $params_default, $params);
        $params = str_replace('"', '\"', substr(json_encode($params),1,-1));
        $json_params = '"{'.$params.'}"';

        $res = $this -> callAction( "/linkgrabberv2/queryLinks", $deviceId, $json_params);
        return $res;
    }
    
    // Retrive packages
    public function queryPackagesFromCollector( $deviceId, $params = []) {
        //taken from: https://docs.google.com/document/d/1IGeAwg8bQyaCTeTl_WyjLyBPh4NBOayO0_MAmvP5Mu4/edit# (LinkQueryStorable)
        $params_default = [
            "availableOfflineCount" => true,
            "availableOnlineCount" => true,
            "availableTempUnknownCount" => true,
            "availableUnknownCount" => true,
            "bytesTotal" => true,
            "childCount" => true,
            "comment" => true,
            "enabled" => true,
            "hosts" => true,
            "maxResults" => -1,
            "priority" => true,
            "saveTo" => true,
            "startAt" => 0,
            "status" => true
        ];

        $params = array_merge( $params_default, $params);
        $params = str_replace('"', '\"', substr(json_encode($params),1,-1));
        $json_params = '"{'.$params.'}"';
        
        $res = $this -> callAction( "/linkgrabberv2/queryPackages", $deviceId, $json_params);
        return $res;
    }
    
    // Retrive links
    public function queryLinksFromDownloads( $deviceId, $params = []) {
        //taken from: https://docs.google.com/document/d/1IGeAwg8bQyaCTeTl_WyjLyBPh4NBOayO0_MAmvP5Mu4/edit# (LinkQueryStorable)
        $params_default = [
            "addedDate" => true,
            "bytesLoaded" => true,
            "bytesTotal" => true,
            "comment" => true,
            "enabled" => true,
            "eta" => true,
            "extractionStatus" => true,
            "finished" => true,
            "finishedDate" => true,
            "host" => true,
            "maxResults" => -1,
            "priority" => true,
            "running" => true,
            "skipped" => true,
            "speed" => true,
            "startAt" => 0,
            "status" => true,
            "url" => true,
        ];

        $params = array_merge( $params_default, $params);
        $params = str_replace('"', '\"', substr(json_encode($params),1,-1));
        $json_params = '"{'.$params.'}"';
        
        $res = $this -> callAction( "/downloadsV2/queryLinks", $deviceId, $json_params);
        return $res;
    }
    
    // Retrive packages
    public function queryPackagesFromDownloads( $deviceId, $params = []) {
        //taken from: https://docs.google.com/document/d/1IGeAwg8bQyaCTeTl_WyjLyBPh4NBOayO0_MAmvP5Mu4/edit# (LinkQueryStorable)
        $params_default = [
            "bytesLoaded" => true,
            "bytesTotal" => true,
            "childCount" => true,
            "comment" => true,
            "enabled" => true,
            "eta" => true,
            "finished" => true,
            "hosts" => true,
            "maxResults" => -1,
            "priority" => true,
            "running" => true,
            "saveTo" => true,
            "speed" => true,
            "startAt" => 0,
            "status" => true
        ];

        $params = array_merge( $params_default, $params);
        $params = str_replace('"', '\"', substr(json_encode($params),1,-1));
        $json_params = '"{'.$params.'}"';
        
        $res = $this -> callAction( "/downloadsV2/queryPackages", $deviceId, $json_params);
        return $res;
    }
    
    public function setEnableFromDownloads($deviceId, $params = []) {
        $params = substr(json_encode($params),1,-1);
        
        $res = $this -> callAction( "/downloadsV2/setEnabled", $deviceId, $params);
        return $res;
    }
    
    public function setEnableFromCollector($deviceId, $params = []) {
        $params = substr(json_encode($params),1,-1);
        
        $res = $this -> callAction( "/linkgrabberv2/setEnabled", $deviceId, $params);
        return $res;
    }
    
    public function forceDownload($deviceId, $params = []) {
        $params = substr(json_encode($params),1,-1);
        
        $res = $this -> callAction( "/downloadsV2/forceDownload", $deviceId, $params);
        return $res;
    }
    
    public function moveToDownloadlist($deviceId, $params = []) {
        $params = substr(json_encode($params),1,-1);
        
        $res = $this -> callAction( "/linkgrabberv2/moveToDownloadlist", $deviceId, $params);
        return $res;
    }
    
    public function removeFromDownloads($deviceId, $params = []) {
        $params = substr(json_encode($params),1,-1);
        
        $res = $this -> callAction( "/downloadsV2/removeLinks", $deviceId, $params);
        return $res;
    }
    
    public function removeFromCollector($deviceId, $params = []) {
        $params = substr(json_encode($params),1,-1);
        
        $res = $this -> callAction( "/linkgrabberv2/removeLinks", $deviceId, $params);
        return $res;
    }
    
    // Make a call to my.jdownloader.org
    // input: query - path+params, key - key for encryption, params - additional params
    // return: result from server or false
    private function callServer( $query, $key, $params = false) {
        if( $params != "") {
            if( $key != "") {
                $params = $this -> encrypt( $params, $key);
            }
            $rid = $this -> rid_counter;
        } else {
            $rid = $this -> getUniqueRid();
        }
        if( strpos( $query, "?") !== false) { $query = $query."&"; } else { $query = $query."?"; }
        $query = $query."rid=".$rid;
        $signature = $this -> sign( $key, $query);
        $query = $query."&signature=".$signature;
        $url = $this -> api_url.$query;
        if( $params != "") {
            $res = $this -> postQuery( $url, $params, $key);
        } else {
            $res = $this -> postQuery( $url, "", $key);
        }
        if( $res === false) {
            return false;
        }
        $content_json = json_decode( $res, true);
        if( $content_json["rid"] != $this -> rid_counter) {
            return false;
        }
        return $res;
    }

    // Make a call to API function on my.jdownloader.org
    // input: device_name - name of device to send action, action - action pathname, params - additional params
    // return: result from server or false
    public function callAction( $action, $deviceId, $params = "") {
        $query = "/t_".urlencode( $this -> sessiontoken)."_".urlencode( $deviceId).$action;
        if( $params !== "") {
            $json_data = '{"url":"'.$action.'","params":['.$params.'],"rid":'.$this -> getUniqueRid().',"apiVer":'.$this -> apiVer.'}';
        } else {
            $json_data = '{"url":"'.$action.'","rid":'.$this -> getUniqueRid().',"apiVer":'.$this -> apiVer.'}';
        }
        $json_data = $this -> encrypt( $json_data, $this -> deviceEncryptionToken);
        $url = $this -> api_url.$query;
        $res = $this -> postQuery( $url, $json_data, $this -> deviceEncryptionToken);
        if( $res === false) {
            return false;
        }
        $content_json = json_decode( $res, true);
        if( $content_json["rid"] != $this -> rid_counter) {
            return false;
        }
        return $res;
    }

    // Genarate new unique rid
    // return new rid_counter
    public function getUniqueRid() {
        $this -> rid_counter++;
        return $this -> rid_counter;
    }

    // Return current rid_counter
    public function getRid() {
        return $this -> rid_counter;
    }

    private function createSecret( $username, $password, $domain) {
        return hash( "sha256", strtolower( $username) . $password . strtolower( $domain), true);
    }

    private function sign( $key, $data) {
        return hash_hmac( "sha256", $data, $key);
    }

    private function decrypt( $data, $iv_key) {
        $iv = substr( $iv_key, 0, strlen( $iv_key)/2);
        $key = substr( $iv_key, strlen( $iv_key)/2);
        return openssl_decrypt( base64_decode( $data), "aes-128-cbc", $key, OPENSSL_RAW_DATA, $iv);
    }

    private function encrypt( $data, $iv_key) {
        $iv = substr( $iv_key, 0, strlen( $iv_key)/2);
        $key = substr( $iv_key, strlen( $iv_key)/2);
        return base64_encode( openssl_encrypt( $data, "aes-128-cbc", $key, OPENSSL_RAW_DATA, $iv));
    }

    private function updateEncryptionToken( $oldToken, $updateToken) {
        return hash( "sha256", $oldToken.pack( "H*", $updateToken), true);
    }

    // postQuery( $url, $postfields, $iv_key)
    // Make Get or Post Request to $url ( $postfields)
    // Send Payload data if $postfields not null
    // return plain response or decrypted response if $iv_key not null
    private function postQuery( $url, $postfields = false, $iv_key = false) {
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $url);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true);
        if( $postfields) {
            $headers[] = "Content-Type: application/aesjson-jd; charset=utf-8";
            curl_setopt( $ch, CURLOPT_POST, true);
            curl_setopt( $ch, CURLOPT_POSTFIELDS, $postfields);
            curl_setopt( $ch, CURLOPT_HEADER, true);
            curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
        }
        $response = array();
        $response["text"] = curl_exec( $ch);
        $response["info"] = curl_getinfo( $ch);
        $response["code"] = $response["info"]["http_code"];
        if( $response["code"] != 200) {
            return false;
        }
        if( $postfields) {
            $response["body"] = substr( $response["text"], $response["info"]["header_size"]);
        } else {
            $response["body"] = $response["text"];
        }
        if( $iv_key) {
            $response["body"] = $this -> decrypt( $response["body"], $iv_key);
        }
        curl_close( $ch);
        return $response["body"];
    }
}
