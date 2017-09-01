<?php
/**
 * Theme player function file
 * @package Louie
 * @since Theme version 1.0.0
 */

/**
 * 参考
 * http://blog.csdn.net/fdipzone/article/details/28766357
 */
function jsonFormat( $data, $indent = null ) {
  
    // 对数组中每个元素递归进行urlencode操作，保护中文字符
    array_walk_recursive($data, 'jsonFormatProtect');
    // json encode
    $data = json_encode($data);
    // 将urlencode的内容进行urldecode
    $data = urldecode($data);
    // 缩进处理
    $ret = '';
    $pos = 0;
    $length = strlen($data);
    $indent = isset($indent)? $indent : '    ';
    $newline = "\n";
    $prevchar = '';
    $outofquotes = true;
  
    for($i=0; $i<=$length; $i++){
  
        $char = substr($data, $i, 1);
  
        if($char=='"' && $prevchar!='\\'){
            $outofquotes = !$outofquotes;
        }elseif(($char=='}' || $char==']') && $outofquotes){
            $ret .= $newline;
            $pos --;
            for($j=0; $j<$pos; $j++){
                $ret .= $indent;
            }
        }
  
        $ret .= $char;
          
        if(($char==',' || $char=='{' || $char=='[') && $outofquotes){
            $ret .= $newline;
            if($char=='{' || $char=='['){
                $pos ++;
            }
 
            for($j=0; $j<$pos; $j++){
                $ret .= $indent;
            }
        }
  
        $prevchar = $char;
    }
  
    return $ret;
}  
  
/**
 * 将数组元素进行urlencode
 * @param String $val
 */
function jsonFormatProtect( $val ){
    if($val!==true && $val!==false && $val!==null){
        $val = urlencode($val);
    }
}

/**
 * Get music data
 */
function get_list( $id ){

	$response = netease_curl(0, $id);

	if ($response["code"] == 200 && $response["result"]) {
		//处理音乐信息
		$result = $response["result"]["tracks"];
		$count  = count($result);

		if ($count < 1) return false;

		$collect_name   = $response["result"]["name"];
		$collect_cover  = $response["result"]["coverImgUrl"];
		$collect_tags   = $response["result"]["tags"];
		//$collect_description = $response["result"]["description"]; //描述

		$collect = array(
			"collect_id"     => $playlist_id,
			"collect_title"  => $collect_name,
			"collect_author" => '',
			"collect_type"   => "collects",
			"collect_count"  => $count
		);

		foreach ($result as $k => $value) {
			$mp3_url = str_replace("http://m", "https://p", $value["mp3Url"]);
			$cover_url = str_replace("http://", "https://", $value['album']['picUrl']);
			$mp3_title = str_replace('"', ' · ', $value["name"]);
			$artists = array();
			foreach ($value["artists"] as $artist) {
				$artists[] = $artist["name"];
			}

			$artists = implode(",", $artists);

			$collect["songs"][] = array(
				"song_id"     => $value["id"],
				"song_title"  => $mp3_title,
				//"song_length" => ceil($value['duration']/1000),
				"song_src"    => $mp3_url,
				"song_author" => $artists,
				"song_cover"  => $cover_url,
				"song_sheet"  => $collect_name,
				"collect_cover" => $collect_cover,
				"collect_tags" => $collect_tags 
			);
		}

		return $collect;
	}

	return false;
}

function get_album( $id ) {

	$response = netease_curl(1, $id);

	if ($response["code"] == 200 && $response["album"]) {
		//处理音乐信息
		$result = $response["album"]["songs"];
		$count  = count($result);

		if ($count < 1) return false;

		$album_name   = $response["album"]["name"];
		$album_author = $response["album"]["artist"]["name"];
		$cover        = $response["album"]['picUrl'];
		$album_cover  = $response["album"]["blurPicUrl"];

		$album = array(
			"album_id"     => $album_id,
			"album_title"  => $album_name,
			"album_author" => $album_author,
			"album_type"   => "albums",
			"album_count"  => $count
		);

		foreach ($result as $k => $value) {
			$mp3_url          = str_replace("http://m", "http://p", $value["mp3Url"]);
			$album["songs"][] = array(
				"song_id"     => $value["id"],
				"song_title"  => $value["name"],
				//"song_length" => ceil($value['duration']/1000),
				"song_src"    => $mp3_url,
				"song_author" => $album_author,
				"song_cover"  => $cover,
				"song_sheet"  => $album_name,
				"collect_cover" => $album_cover
			);
		}

		return $album;
	}

	return false;
}

/**
 * Curl
 */  
function netease_curl( $type, $id ) {
	$header = array(
		"Accept:*/*",
		"Accept-Language:zh-CN,zh;q=0.8",
		"Cache-Control:no-cache",
		"Connection:keep-alive",
		"Content-Type:application/x-www-form-urlencoded;charset=UTF-8",
		"Cookie:visited=true;",
		"DNT:1",
		"Host:music.163.com",
		"Pragma:no-cache",
		"Referer:http://music.163.com/outchain/player?type={$type}&id={$id}&auto=1&height=430&bg=e8e8e8",
		"User-Agent:Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/44.0.2403.155 Safari/537.36"
	);

	$prefix = 'http://music.163.com/api/';

	switch($type){
		//歌单
		case 0:
			$url = "playlist/detail?id={$id}&ids=%5B%22{$id}%22%5D&limit=10000&offset=0";
			break;
		//专辑
		case 1:
			$url = "album/{$id}?id={$id}&id={$id}&ids=%5B%22{$id}%22%5D&limit=10000&offset=0";
			break;
	}

	$url = $prefix . $url;
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	$cexecute = curl_exec($ch);
	@curl_close($ch);
	if ($cexecute) {
		$result = json_decode($cexecute, TRUE);
		return $result;
	}
	else {
		return false;
	}
}


/**
 * Music list
 */  
function json( $id, $type ) {
	switch ( $type ) {
	    case 0:
	        $data = get_list($id);
	        break;
	    case 1:
	        $data = get_album($id);
	        break;
    }
	$data = $data['songs'];
	$json = '';
	if ($data) {
		foreach ($data as $key => $value) {
			$json .= jsonFormat(array(
				'title' =>$value['song_title'],
				'artist' => $value['song_author'],
				'cover' => $value['song_cover'].'?param=80x80',
				'id' => $value['song_id'],
				)
			);
			$json .=',';
		}

		return $json;
	}

	return '找不到歌单数据，检查或更换歌单ID';
}

/**
 * Auto play
 */
function autoplay() {
	$bgm = get_option('bgm_options');
	$that = !empty($bgm['autoplay']) ? 'on' : 'off';
	return $that;
}

/**
 * Shuffle play
 */
function shuffleplay() {
	$bgm = get_option('bgm_options');
	$that = !empty($bgm['shuffle']) ? 'on' : 'off';
	return $that;
}

function jsonurl() {
	return THEME_URL.'/modules/player/';
}

/**
 * Setting
 */
add_action( 'init', 'BGMjson' );
add_action( 'init', 'BGMdata' );
add_action( 'admin_init', 'BGMoptions' );
add_action( 'admin_enqueue_scripts', 'BGMscripts', 9999999 );
if ( object('extension_player') ) add_action( 'admin_menu', 'BGMsetting' );
function BGMscripts() {
    wp_enqueue_style( 'player-setting', THEME_URL . '/assets/css/player-setting.css', array(), THEME_VERSION, 'all' );
    wp_enqueue_script( 'init', THEME_URL . '/assets/js/init.js', array(), THEME_VERSION, true );
    wp_enqueue_script( 'setting', THEME_URL . '/assets/js/setting.js', array(), THEME_VERSION, true );
}
function BGMsetting() {
    add_menu_page('网易云音乐', '网易云音乐', 'manage_options', __FILE__, 'BGMSettingpage');
}
function BGMoptions() {
	register_setting('bgm_setting_group', 'bgm_options');
}
function BGMjson() {
	if($_GET['action'] == 'music_json_get' && 'GET' == $_SERVER['REQUEST_METHOD']) {
		echo json($_GET["id"], $_GET['type']);
		die();
	}

	return;
}
function BGMdata() {
	if($_GET['action'] == 'music_list_get' && 'GET' == $_SERVER['REQUEST_METHOD']) {
		$bgm = get_option('bgm_options');
		echo '<script type="text/javascript">var playlist = ['. $bgm['json'] .']</script>';
		die();
	}

	return;
}

/**
 * Setting page
 */
function BGMSettingpage() {
	$bgm = get_option('bgm_options');
	?>
	<section class="setting">
		<div class="setting-inner">
			<h2 class="title">网易云音乐</h2>
			<div class="bgm-get">
				<form action="<?php echo $_SERVER["REQUEST_URI"]; ?>" method="post" class="get-json">
					<select id="music-type" name="type">
						
						<?php if($bgm['type'] == 0) : ?>
							<option value="0">歌单</option>
							<option value="1">专辑</option>
						<?php else : ?>
							<option value="1">专辑</option>
							<option value="0">歌单</option>
						<?php endif; ?>
					</select> 
					<input type="text" name="mid" id="mid" value="<?php echo isset($_POST['mid'])?$_POST['mid']:$bgm['mid']; ?>"  placeholder="歌单ID" required />
					<input id="button" class="button" name="submit" type="submit" value="获取">
					<input type="hidden" name="get_json" value="<?php echo $_SERVER['REQUEST_URI']; ?>" />
				</form>
			</div>
			<form method="post" action="options.php" class="sava-setting">
				<?php settings_fields('bgm_setting_group');
				$input = isset($_POST['mid']) ? $_POST['mid'] : $bgm['mid'];
				$type = isset($_POST['type']) ? $_POST['type'] : $bgm['type'];
				$json = empty($_POST['get_json']) ? $bgm['json'] : json($input); ?>
				<textarea class="json-data" rows="8" cols="100" name="bgm_options[json]"><?php echo $json; ?></textarea>
				<p class="checkbox-menu">
					<span><input type="checkbox" name="bgm_options[autoplay]" class="autoplay" value="on" <?php checked('on',$bgm['autoplay']); ?> />自动播放</span>
					<span><input type="checkbox" name="bgm_options[shuffle]" class="shuffle" value="on" <?php checked('on',$bgm['shuffle']); ?> />随机播放</span>
					<span><input type="checkbox" name="bgm_options[search]" class="search" value="on" <?php checked('on',$bgm['search']); ?> disabled="disabled"/>音乐搜索（施工中）</span>
					<input type="submit" name="save" class="button" value="保存设置" />
				</p>
				<input type="hidden" name="bgm_options[type]" class="type" value="<?php echo $type; ?>" />
				<input type="hidden" name="bgm_options[mid]" class="mid" value="<?php echo $input; ?>" />
				
			</form>
			<?php if ( isset($_REQUEST['settings-updated']) ){
				echo '<div id="message" class="updated"><p>设置更新成功。</p></div>';
			}?>
		</div>
	</section>
<?php
}