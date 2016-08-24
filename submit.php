<?php
include('include/mysql.php');
include("include/settings.php");
$images = file_get_contents($vardir . "images.json");
$images = json_decode($images, true);

session_start();
if($_POST){
  if( $_POST['username'] ) {
    header(' ', true, 200);
    die();
  }

  $val = Array();
  foreach($_POST AS $post_key => $post_val){
    if (is_array($post_val)) {
        $val[$post_key] = $mysqli->real_escape_string(json_encode($post_val));
    } else {
        $val[$post_key] = $mysqli->real_escape_string($post_val);
    }     
  }
  
  if(!$mysqli->query("
    INSERT INTO `submissions` (
      `template`,
      `generated`
    ) VALUES (
      '".$val['template']."',
      '".$val['gen_text']."'
    )
    ")){
      echo "Error: ".$mysqli->sqlstate;
    } else {
      $_SESSION['sid'] = $mysqli->insert_id;
      $location = "";
      if ($_GET['page']) {
        $location = "/p/" . $_GET['page'];
      }
      $location = $location . "/share.php?sid=" . $mysqli->insert_id;
      header("Location: ".$location); ?>
      Now you can share with friends!<br />
<?php
    }
}
?>
