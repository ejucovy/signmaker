<?php
require_once "vendor/autoload.php";
include("include/settings.php");
include('include/mysql.php');

$pages = file_get_contents($vardir . "pages.json");
$pages = json_decode($pages, true);
$page = $pages[$_GET['page']];
if (!$page) {
  exit(0); //@@TODO 404
};

$images = file_get_contents($vardir . "images.json");
$images = json_decode($images, true);

$record = $mysqli->query("
  SELECT `id`, `first_name`, `last_name`, `template`, `generated` FROM `submissions`
  WHERE `id`='".$mysqli->real_escape_string($_GET['sid'])."'
");
$sign = $record->fetch_array();

if ($images[$sign['template']]['placement'] && count($images[$sign['template']]['placement']) > 1) {
  $gen_text = json_decode($sign['generated']);
  $i = 0; $message_str = "";
  foreach ($gen_text as $m) {
    $i += 1;
    $message_str = $message_str . "&m" . $i . "=" . urlencode($m);  
  }
} else {
  $message_str = "&message=" . urlencode($sign['generated']);
}

$sign['absolute_url'] = "http://" . $_SERVER['HTTP_HOST'];
if ($_GET['page']) {
  $sign['absolute_url'] = $sign['absolute_url'] . "/p/" . $_GET['page'];
}
$sign['absolute_url'] = $sign['absolute_url'] . "/share.php?sid=" . $sign['id'];

$sign['image_url'] = "http://" . $_SERVER['HTTP_HOST'] . "/image.php?t=" . urlencode($sign['template']) . $message_str;

$sign['hashtags'] = $page['twitter']['hashtags'];
$sign['twitter_via'] = $page['twitter']['via'];

$sign['message_str'] = $message_str;

$sign['tweet_text'] = $page['twitter']['text'];

$sign['email_subject'] = $page['email']['subject'];
$sign['email_body'] = $page['email']['body'] . $sign['image_url'];

$loader = new Twig_Loader_Filesystem($vardir . "templates");

$twig = new Twig_Environment($loader, array(
    'auto_reload' => true,
    'cache' => '/tmp/signmaker-twig-cache',
    ));
echo $twig->render('share.html',
                    array('sign' => $sign,
                          'active_theme' => "themes/" . $page['theme'] . ".html"));
