<?php
/* CLEARN 'ER UP */
function clean($value) {
  if (get_magic_quotes_gpc()) $value = stripslashes($value);
  //if (!is_numeric($value))  $value = mysql_real_escape_string($value);
  if (!is_numeric($value))  $value = addslashes($value);
  return $value;
}
array_walk($_GET,'clean');
array_walk($_POST,'clean');
array_walk($_COOKIE,'clean');

/* EMAIL FUNCTION */
require_once('includes/ses.php');
function sendEmail($to, $subject, $html, $text, $from = false) {
  $ses = new SimpleEmailService('AKIAJAF3HBQ5UIWDV3MA', 'Ck0YiLp/kWuSGebYJ9SoyjuvtOxyYErlzvXxxi3j');
  $from_name = 'System Email';
  $from_email = 'ecoker@brownshoe.com';
  $m = new SimpleEmailServiceMessage();
  $m->addTo($to);
  $m->setFrom($from_email);
  $m->setSubject($subject);
  $m->setMessageFromString($text, $html);
  $result = $ses->sendEmail($m);
  return $result;
}

/* INCLUDES */
include('includes/inc.zipprep.php');
$host = $_SERVER['HTTP_HOST'];
$self = strrpos($_SERVER['PHP_SELF'], '/') > 0 ? substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/') + 1) : $_SERVER['PHP_SELF'];
$baseURL = "http://$host$self"; 
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>D2C Image Crop</title>
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Le HTML5 shim, for IE6-8 support of HTML elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- Le styles -->
    <link href="styles/bootstrap.css" rel="stylesheet">
    <style type="text/css">
      /* Override some defaults */
      html, body {
        background-color: #eee;
      }
      body {
        padding-top: 40px; /* 40px to make the container go all the way to the bottom of the topbar */
      }
      .container > footer p {
        text-align: center; /* center align it with the container */
      }
      .container {
        width: 820px; /* downsize our container to make the content feel a bit tighter and more cohesive. NOTE: this removes two full columns from the grid, meaning you only go to 14 columns and not 16. */
      }

      /* The white background content wrapper */
      .container > .content {
        background-color: #fff;
        padding: 20px;
        margin: 0 -20px; /* negative indent the amount of the padding to maintain the grid system */
        -webkit-border-radius: 0 0 6px 6px;
           -moz-border-radius: 0 0 6px 6px;
                border-radius: 0 0 6px 6px;
        -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.15);
           -moz-box-shadow: 0 1px 2px rgba(0,0,0,.15);
                box-shadow: 0 1px 2px rgba(0,0,0,.15);
      }

      /* Page header tweaks */
      .page-header {
        background-color: #f5f5f5;
        padding: 20px 20px 10px;
        margin: -20px -20px 20px;
      }

      /* Styles you shouldn't keep as they are for displaying this base example only */
      .content .span10,
      .content .span4 {
        min-height: 500px;
      }
      /* Give a quick and non-cross-browser friendly divider */
      .content .span4 {
        margin-left: 0;
        padding-left: 19px;
        border-left: 1px solid #eee;
      }

      .topbar .btn {
        border: 0;
      }
      .span4 label {
        display:block;
        float:none;
        text-align:left;
      }
      .span4 form { padding:10px 20px; }
      .span4 button {
        margin-top:12px;
      }

    </style>
        <!-- Le fav and touch icons -->
    <link rel="shortcut icon" href="images/favicon.ico">
    <link rel="apple-touch-icon" href="images/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="72x72" href="images/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="114x114" href="images/apple-touch-icon-114x114.png">
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
  </head>
<?php
if (isset($_POST['shoe1']) || isset($_POST['shoe2']) || isset($_POST['shoe3']) || isset($_POST['shoe4'])) {
?>
  <div class="topbar">
      <div class="fill">
        <div class="container">
          <a class="brand" href="#">D2C Creative</a>
          <ul class="nav" style="float:right; margin-right:-20px">
            <!-- 
            <li class="active"><a href="#">Home</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="#contact">Contact</a></li>
            <li><img src="shoesLogo.gif"></li>
            -->
          </ul>
          <!--
          <form action="" class="pull-right">
            <input class="input-small" type="text" placeholder="Username">
            <input class="input-small" type="password" placeholder="Password">
            <button class="btn" type="submit">Sign in</button>
          </form>
          -->
        </div>
      </div>
    </div>

    <div class="container">
      <div class="content">
        <div class="page-header">
          <h1>Cropped Images<small></small></h1>
        </div>
        <div class="row">
          <div class="span10">
      <style>
      .span10 img { display:block; margin-bottom:10px; border:1px solid #e0e0e0; }
      </style>
<?php
  include('includes/resizeCrop.php'); /* FANCY IMAGE FUNCTIONS */
  $files=$matchurls=array();
  foreach ($_POST as $key => $value) {
    if (strpos($key, 'shoe') > -1 && strlen($value) > 1) {
      if (@fopen($value, "r")) {
        defineFolder($value);

        $matchurls[] = $urlMatch;
        // YATTA YATTA
        $imageDir = 'shoeImages/'.$brandFolder.'/';
        $imageDirArr = explode('/', $imageDir);
        $currImgDir = '';
        foreach($imageDirArr as $imgKey => $dirVal) {
          if (strlen($dirVal) > 1) {
            $currImgDir .= $dirVal.'/';
            if (!file_exists($currImgDir)) {
              if (mkdir($currImgDir)) {
                echo '<div class="alert-message success span9"><a class="close" href="#">×</a><p><strong>Yo!</strong> '.$currImgDir.' was created.</p></div>';
              } else {
                echo '<div class="alert-message warning span9"><a class="close" href="#">×</a><p><strong>Yikes!</strong> '.$currImgDir.' could not be created.</p></div>';
              }
            }
          }
        }

        echo cropWhiteSpace('shoeImages/'.$brandFolder.'/', $value, false);
        $files[] = $newFileName;
      } else {
        echo '<div class="alert-message warning span9"><a class="close" href="#">×</a><p><strong>Yikes!</strong> '.$value.' does not exist.</p></div>';
      }
    }
  }
  if (count($files) > 0) {
    $destination = 'zipFiles/'.date('Y-m-d_Gis').'.zip';
    create_zip($files, $destination);
  }
?>
        </div>
        <div class="span4">
            <h3>Ask a Friend to Move Files to Production</h3>
            <form class="well form-search" method="post">
              <label class="checkbox"><input type="checkbox" value="glabarre@brownshoe.com" name="friend1" checked> Glenn Labarre</label>
              <label class="checkbox"><input type="checkbox" value="ecoker@brownshoe.com" name="friend2"> Ehren Coker</label>
              <label class="checkbox"><input type="checkbox" value="jwhite@brownshoe.com" name="friend3"> Jacob White</label>
               <label class="checkbox"><input type="checkbox" value="mrcoker@gmail.com" name="friend4"> Gmail Test</label>
              <?php
              if (count($files) > 0) {
                foreach($files as $key => $fileName) {
                  // echo $baseURL.$fileName.' | '.$matchurls[$key].'/Content/'.date('Y').'/emails/TJI'.substr($fileName, strrpos($fileName, '/'))."<br>";
                  // echo '<input type="hidden" name="cropImage'.$key.'" value="'.$fileName.'" />';
                  echo '<input type="hidden" name="cropImage'.$key.'" value="'.$baseURL.$fileName.' | '.$matchurls[$key].'/Content/'.date('Y').'/emails/TJI'.substr($fileName, strrpos($fileName, '/')).'" />';
                }
              }
              if (isset($_POST['email'])) {
                echo '<input type="hidden" value="'.$_POST['email'].'" name="email" />'; 
              }
              ?>
              <input type="hidden" value="<?php echo $destination; ?>" name="destination" />
              <button type="submit" class="btn">Move My Images</button>
            </form>
          <a href="<?php echo $destination; ?>">Download Zip of New Images</a>
          </div>
        </div>
      </div>

      <footer>
        <p>&copy; Copyright <?php echo date('Y'); ?></p>
      </footer>

    </div> <!-- /container -->
<?php
} else {
  $friendFlag = false;
  $to = array();
  $files = array();
  foreach($_POST as $key => $value) {
    if (strpos($key, 'friend') > -1) {
      $friendFlag = true;
      $to[] = $value;      
    } elseif(strpos($key, 'cropImage') > -1) {
      $files[]=$value;
    }
  }
  if ($friendFlag) {
    $to = implode(", ", $to);
    $subject = 'TJI :: Move Image - '.date("F j, Y");
    $origURLs = array();
    $newURLs  = array();
    foreach ($_POST as $key => $value) {
      if (strpos($key, 'cropImage') > -1) {
        $urls = explode(' | ', $value);
        $origURLs[] = $urls[0];
        $newURLs[]  =  $urls[1];
      } else if (strpos($key, 'email') > -1) {
        $emailAddr=$value;
      }
    }
    $fileMoverURL = 'http://localhost/_svn/D2Ctoolkit/tools/fileMover/index.php?';
    $msgImgs = '';
    foreach($origURLs as $key => $value) {
      if ($key > 0) { $fileMoverURL.='&'; }
      $k = $key+1;
      $fileMoverURL.='o'.$k.'='.$origURLs[$key].'&n'.$k.'='.$newURLs[$key];
      $ext = substr($value, strrpos($value, '.'));
      $imgTypes = array('.jpg', '.gif', '.png', '.jpeg');
      if (in_array($ext, $imgTypes)) {
        list($width, $height, $type, $attr) = getimagesize($origURLs[$key]);
        $msgImgs .= '<img src="'.$origURLs[$key].'" style="border:1px solid #333333" width="'.$width.'" height="'.$height.'">';
      }
    }
    if (isset($emailAddr)) {
      $fileMoverURL .= '&email='.$emailAddr;
    }
    $plaintext = $emailAddr.' would like you to move a file at this location: '.$fileMoverURL;
    $message = '<span style="font-size:13px">'.$emailAddr.' would like you to <a href="'.$fileMoverURL.'" style="color: #0069D6;text-decoration: none;">move some files</a>.</span>';
    $message .= '<br><table width="100%" align="center"><tbody><tr><td align="center" style="text-align:center">'.$msgImgs.'</td></tr></tbody></table>';
    include('D2CemailTemplate.php'); // DEFINE 
    $htmlEmail = str_replace('@@HEADLINE@@', 'File Move Request', $htmlEmail);
    $htmlEmail = str_replace('@@MESSAGE@@', $message, $htmlEmail);
    $htmlEmail = str_replace('@@COPYYEAR@@', date('Y'), $htmlEmail);
    $from = $_POST['email'];
    $emailSent = sendEmail($to, $subject, $htmlEmail, $plaintext, $from);
  }
?>
  <body>
    <div class="topbar">
      <div class="fill">
        <div class="container">
          <a class="brand" href="#">D2C Creative</a>
          <ul class="nav" style="float:right; margin-right:-20px">
            <!-- 
            <li class="active"><a href="#">Home</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="#contact">Contact</a></li>
            <li><img src="shoesLogo.gif"></li>
            -->
          </ul>
          <!--
          <form action="" class="pull-right">
            <input class="input-small" type="text" placeholder="Username">
            <input class="input-small" type="password" placeholder="Password">
            <button class="btn" type="submit">Sign in</button>
          </form>
          -->
        </div>
      </div>
    </div>

    <div class="container">

      <div class="content">
        <div class="page-header">
          <h1><span data-bind="text: emailName">Magical Image Cropper</span> <small></small></h1>
        </div>
        <div class="row">
          <div class="span9">
            <?php
            if ($friendFlag && $emailSent) {
              echo '<div class="alert-message success"><a href="#" class="close">×</a><p><strong>Hooray!</strong> Your email has been sent.</p></div>';
            } else if ($friendFlag && !$emailSent) {
              echo '<div class="alert-message warning span9"><a class="close" href="#">×</a><p><strong>Crud!</strong> There was a problem sending your email.</p></div>';
            }
            ?>
            <form action="" method="post">
            <h3>Your Email Address</h3>
            <div id="shoeLinks">
              <div class="clearfix"><input class="span9" id="" name="email" type="text" placeholder="Email Address" /></div>
            </div>
            <h3>Select Your Image(s)</h3>
            <div id="shoeLinks">
              <div class="clearfix"><input class="span9" id="" name="shoe1" type="text" placeholder="Shoe Image" /></div>
              <div class="clearfix"><input class="span9" id="" name="shoe2" type="text" placeholder="Shoe Image" /></div>
              <div class="clearfix"><input class="span9" id="" name="shoe3" type="text" placeholder="Shoe Image" /></div>
              <div class="clearfix"><input class="span9" id="" name="shoe4" type="text" placeholder="Shoe Image" /></div>
            </div>
            <div class="span9">
              <!-- <button id="addShoe" class="btn success">+ Add Shoe</button> -->
              <input type="submit" class="btn primary pull-right" value="Crop Images">
            </div>
        </form>
          </div>
<!--        <div class="span4">
          </div> -->
        </div>
      </div>

      <footer>
        <p>&copy; Copyright <?php echo date('Y'); ?></p>
      </footer>

    </div> <!-- /container -->
<?php
}
?>
  <script type="text/javascript">
  $('.alert-message a.close').click(function() {
    $(this).closest('.alert-message').fadeOut('slow', function() { $(this).remove() });
    return false;
  });
  </script>
  </body>
</html>
<?php
function defineFolder($newURL) {
  global $subdomain, $domain, $brandFolder, $baseFolder, $destFolder, $fileName, $urlMatch;
  $pattern = '/^http[^:]*:\/\/[^\/]*/';
  preg_match($pattern, $newURL, $matches, PREG_OFFSET_CAPTURE);
  $urlMatch = $matches[0][0];
  $basedomain = substr($matches[0][0], strrpos($matches[0][0], '/') + 1);
  $destFolder = str_replace($matches[0][0], '', $newURL);
  $destFolder = substr($destFolder, 0, strrpos($destFolder, '/') + 1);
  $fileName = substr($newURL, strrpos($newURL, '/') + 1);
  $domainArr = explode('.', $basedomain);
  $count = count($domainArr);
  $subdomain=array();
  $domain=array();
  for ($i=0;$i<$count-2;$i++) { $subdomain[]=$domainArr[$i]; }
  $subdomain = implode('.',$subdomain);
  for ($i=$count-2;$i<$count;$i++) { $domain[]=$domainArr[$i]; }
  $domain = implode('.',$domain);
  switch ($subdomain) {
    case 'uat.stage':
      $baseFolder = 'file:///Volumes/Creative/UAT-Staging/';
      break;
    default:
      $baseFolder = 'file:///Volumes/EcommCreative-Prod/';
      break;
  }
  switch ($domain) {
    case 'carlosshoes.com':
      $brandFolder = 'Carlos';
      break;
    case 'drschollsshoes.com':
      $brandFolder = 'DrScholls';
      break;    
    case 'famousfootwear.com':
      if ($subdomain == 'uat.stage') { $brandFolder = 'FamousNew'; } 
      else { $brandFolder = 'Famous'; }
      break;
    case 'fergieshoes.com':
      $brandFolder = 'Fergie';
      break;
    case 'francosarto.com':
      $brandFolder = 'FrancoSarto';
      break;
    case 'lifestride.com':
      $brandFolder = 'lifestride';
      break;
    case 'naturalizer.com':
      $brandFolder = 'Naturalizer';
      break;
    case 'naturalizer.ca':
      if ($subdomain == 'uat.stage') { $brandFolder = 'NaturalizerCA'; }
      else { $brandFolder = 'NaturalizerCanada'; }
      break;
    case 'nayashoes.com':
      $brandFolder = 'Naya';
      break;
    case 'nevados.com':
      $brandFolder = 'Nevados';
      break;
    case 'publicity.brownshoe.com':
      $brandFolder = 'publicity';
      break;
    case 'ryka.com':
      $brandFolder = 'Ryka';
      break;
    case 'shoes.com':
      $brandFolder = 'Shoes';
      break;
    case 'shoesteal.com':
      $brandFolder = 'ShoeSteal';
      break;
    case 'skatestyles.com':
      $brandFolder = 'SkateStyles';
      break;
    case 'viaspiga.com':
      $brandFolder = 'ViaSpiga';
      break;
    case 'zodiacusashoes.com':
      $brandFolder = 'Zodiac';
      break;
    default:
      $brandFolder = 'Shoes';
      break;
  }
}
?>