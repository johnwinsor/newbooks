<?php
include 'config.php';
$shelflists = "http://www.goodreads.com/shelf/list.xml?user_id=" . $gruserid . "&key=" . $grkey;
$baserssurl = "http://www.goodreads.com/review/list_rss/14996177?per_page=200&shelf=";
//echo $shelflists;
function string_sanitize($s) {
	$q = str_replace('"', "", $s);
	$result = strip_tags($q);
	return $result;
}

function titlecap($shelf) {
	$strippedtitle = str_replace('Mills College Library\'s bookshelf: ', "", $shelf);
	$spacedtitle =  str_replace('-', " ", $strippedtitle);
	$feedtitle = ucwords($spacedtitle);
	return $feedtitle;
}

function feed_menu($shelflists) {
	$xml = simplexml_load_file($shelflists);
	$shelves = $xml->xpath("//user_shelf[not (name='read' or name='to-read')][not (starts-with(name,'weekly'))]");

	print "<form class=\"form-inline\" role=\"form\" action=\"" . $_SERVER['PHP_SELF'] . "\" method=\"get\">";
	print "<div class=\"form-group\">";
			print "<select class=\"form-control\" name=\"list\">";
			foreach ($shelves as $shelf) {
				$feedname =  $shelf->name;
				$name = titlecap($feedname);
				if($name=="Currently Reading") {
					$name = "New Arrivals";
				}
				print "<option value=\"$feedname\"";
				if($_GET['list']==$feedname) {
					echo " selected=selected";
				}
				print ">" . $name . "</option>";
				print "\n";
			}
			print "</select>";
	print "</div>";
	print "<button type=\"submit\" class=\"btn btn-default\">Select</button>";
	print "</form>\n";
}


if(isset($_GET["list"])) {
	require "PGFeed.php";
	$p = new PGFeed;
	$p->setOptions(0,200,0,NULL);

	$post = $_GET["list"];
	$source = $baserssurl.$post;
	$p->parse($source);
	$channel = $p->getChannel();

	$shelf = $channel["title"];
	$feedtitle = titlecap($shelf);

	if($feedtitle=="Currently Reading") {
		$feedtitle = "New Arrivals";
	}
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Mills College Library</title>

    <!-- styles -->
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jasny-bootstrap/3.1.3/css/jasny-bootstrap.min.css">
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css">
    <link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" />
    <link rel="stylesheet" href="/includes/spinner/ladda-themeless.min.css">
    <link href="/includes/css/global.css" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet">

    <!-- scripts -->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <script>
        $(function(){
            $("#millsheader").load("/includes/head.html");
            $("#millsfooter").load("/includes/foot.html");
        });
    </script>

</head>
<body>
<div class="container-fluid mainbackground">
    <div class="row" id="millsheader"></div>
    <div class="row maincontent">
		<div class="col-md-12">
			<div class="row contentcenter">
				<div class="col-md-12">
					<?php feed_menu($shelflists); ?>
				</div>
			</div>
			<div class="row page-header contentcenter">
				<div class="col-md-12">
					<h2><?php echo $feedtitle;?></h2>
				</div>
			</div>

<?php
$items = $p->getItems();     // gets news items
$covercount = 0;
foreach (array_slice($items,1) as $i) {
	$covercount++;
	//var_dump($i);
	$desc = string_sanitize($i["book_description"]);
	$review = $i["user_review"];
	$coverurl = $i["book_large_image_url"];
	if (preg_match("/nocover/i", $coverurl)) {
		$coverurl= "http://www.goodreads.com" . $coverurl;
	}
	//print $desc;
	print "<br />";
	print "<div class=\"row\">";
			print "<div class=\"col-md-4\"></div>";
			print "<div class=\"col-md-4\">";
					print "<div class=\"covercontainer\" data-toggle=\"popover\" data-container=\"div#caption-" . $covercount . "\" data-title=\"" . $i["title"] . "\" data-content=\"" . $desc . "\"><a href=\"http://library.mills.edu/search/i?SEARCH=" . $i["isbn"] . "&sortdropdown=-&searchscope=6\" target=\"_parent\"><img src=\"" . $coverurl . "\" alt=\"\"></a>";
							print "<p class=\"booktitle\"><a href=\"http://library.mills.edu/search/i?SEARCH=" . $i["isbn"] . "&sortdropdown=-&searchscope=6\" target=\"_parent\">" . $i["title"] . "</a></span>";
							print "<p class=\"bookauthor\">by " . $i["author_name"] . "</span>";
							print "<p class=\"bookauthor\">Call Number: " . $review . "</span>";
					print "</div>";
			print "</div>";
			print "<div class=\"col-md-4\" id=\"caption-" . $covercount . "\"></div>";
	print "</div>";
	print "<hr>";
}
?>
		</div>
	</div> <!-- close maincontent -->
    <div class="row" id="millsfooter"></div>
</div> <!-- close container -->
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jasny-bootstrap/3.1.3/js/jasny-bootstrap.min.js"></script>
<script src="http://malsup.github.io/jquery.form.js"></script>

<script>
$('[data-toggle="popover"]').popover({
		trigger: 'hover'
		});
</script>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-40625016-4', 'auto');
  ga('send', 'pageview');

</script>

</body>
</html>
