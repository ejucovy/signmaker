<?php
require_once "vendor/autoload.php";

include("include/settings.php");
$images = file_get_contents($vardir . "images.json");
$images = json_decode($images, true);

$pages = file_get_contents($vardir . "pages.json");
$pages = json_decode($pages, true);
$page = $pages[$_GET['page']];
if (!$page) {
  exit(0); //@@TODO 404
};

$signs = $page['images'];
$signs_prefilled = $page['imagedata'];
if ($signs_prefilled) {
  $signs = $signs_prefilled;
  $twig_template = "index-prefilled.html";
} else {
  $twig_template = "index.html";
}

$skin = $page['theme'];

if (file_exists($vardir . "templates/themes/" . $skin) && !is_file($vardir . "templates/themes/" . $skin)) {
    $loader1 = new Twig_Loader_Filesystem($vardir . "templates/themes/" . $skin);
    $loader2 = new Twig_Loader_Filesystem($vardir . "templates");
    $loader = new Twig_Loader_Chain(array($loader1, $loader2));
} else {
    $loader = new Twig_Loader_Filesystem($vardir . "templates");
}

$twig = new Twig_Environment($loader, array(
    'auto_reload' => true,
    'cache' => '/tmp/signmaker-twig-cache',
    ));
echo $twig->render($twig_template,
                   array('signs' => $signs,
                         'images' => $images,
                         'active_view' => "index",
                         'query_string' => $_SERVER['QUERY_STRING'],
                         'active_theme' => "themes/" . $skin . ".html"));

?>
