<?php
/*
	p2 -  設定管理ページ
*/

include_once './conf/conf.inc.php';  // 基本設定
require_once './p2util.class.php';	// p2用のユーティリティクラス
require_once './filectl.class.php';

authorize(); // ユーザ認証

// 書き出し用変数 ========================================
$ptitle = 'ログイン管理';

if ($_conf['ktai']) {
	$status_st = "ｽﾃｰﾀｽ";
	$autho_user_st = "認証ﾕｰｻﾞ";
	$client_host_st = "端末ﾎｽﾄ";
	$client_ip_st = "端末IPｱﾄﾞﾚｽ";
	$browser_ua_st = "ﾌﾞﾗｳｻﾞUA";
	$p2error_st = "p2 ｴﾗｰ";
} else {
	$status_st = "ステータス";
	$autho_user_st = "認証ユーザ";
	$client_host_st = "端末ホスト";
	$client_ip_st = "端末IPアドレス";
	$browser_ua_st = "ブラウザUA";
	$p2error_st = "p2 エラー";
}

$autho_user_ht = "";
if ($login['use']) {
	$autho_user_ht = "{$autho_user_st}: {$login['user']}<br>";
}


$body_onload = "";
if (!$_conf['ktai']) {
	$body_onload = " onLoad=\"setWinTitle();\"";
}

// HOSTを取得
if (!$hc[remoto_host] = $_SERVER['REMOTE_HOST']) {
	$hc[remoto_host] = gethostbyaddr($_SERVER['REMOTE_ADDR']);
}
if ($hc[remoto_host] == $_SERVER['REMOTE_ADDR']) {
	$hc[remoto_host] = "";
}

$hc['ua'] = $_SERVER['HTTP_USER_AGENT'];

$hd = array_map('htmlspecialchars', $hc);

//=========================================================
// ■ HTMLプリント
//=========================================================
P2Util::header_nocache();
P2Util::header_content_type();
if ($_conf['doctype']) {
	echo $_conf['doctype'];
}
echo <<<EOP
<html>
<head>
	{$_conf['meta_charset_ht']}
	<meta name="ROBOTS" content="NOINDEX, NOFOLLOW">
	<meta http-equiv="Content-Style-Type" content="text/css">
	<meta http-equiv="Content-Script-Type" content="text/javascript">
	<title>{$ptitle}</title>
EOP;
if (!$_conf['ktai']) {
	@include("./style/style_css.inc");
	@include("./style/setting_css.inc");
	echo <<<EOP
	<script type="text/javascript" src="js/basic.js"></script>\n
EOP;
}
echo <<<EOP
</head>
<body{$body_onload}>
EOP;

// 携帯用表示
if (!$_conf['ktai']) {
	echo <<<EOP
<p id="pan_menu">ログイン管理</p>
EOP;
}

// インフォメッセージ表示
echo $_info_msg_ht;
$_info_msg_ht = "";

echo "<ul id=\"setting_menu\">";

if ($login['use']) {
	echo <<<EOP
	<li><a href="login.php{$_conf['k_at_q']}"{$access_login_at}>p2ログイン管理</a></li>
EOP;
}

echo <<<EOP
	<li><a href="login2ch.php{$_conf['k_at_q']}"{$access_login2ch_at}>2chログイン管理</a></li>
EOP;

echo '</ul>'."\n";

if ($_conf['ktai']) {
	echo "<hr>";
}

echo "<p id=\"client_status\">";
echo <<<EOP
{$autho_user_ht}
{$client_host_st}: {$hd['remoto_host']}<br>
{$client_ip_st}: {$_SERVER['REMOTE_ADDR']}<br>
{$browser_ua_st}: {$hd['ua']}<br>
EOP;
echo "</p>\n";


// フッタプリント===================
if ($_conf['ktai']) {
	echo '<hr>'.$_conf['k_to_index_ht']."\n";
}

echo '</body></html>';

?>
