<!DOCTYPE html>

<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Mills College Library</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">
	<meta name="author" content="">

	<!-- styles -->
	<link href="css/bootstrap.css" rel="stylesheet">
	<link href="css/custom.css" rel="stylesheet">
	<link href="css/normalize.css" rel="stylesheet">

	<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
	<!--[if lt IE 9]>
	<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

</head>
<body>
<div class="container-fluid">
	<div class="row-fluid header">
		<div class="span12">
			<div class="navbar right">
				<div>
					<ul class="nav">
						<li><a href="http://library.mills.edu/patroninfo">My Minerva Login</a></li>
						<li><a href="http://www.mills.edu/academics/library/exceptions.php">Library Hours</a></li>
						<li><a href="http://library.mills.edu/">Minerva Catalog Home</a></li>
					</ul>
				</div>
			</div>
			<div class="topLogoSmall">
				<div class="mills">
					<a href="http://www.mills.edu" tabindex="10"><img src="http://library.mills.edu/screens/Mills_logo_280_scaled.png" height="49" width="170" alt="Mills College Library"></a>
				</div>
				<div>
					<span class="topLogoSmallText"><a href="http://www.mills.edu/academics/library/index.php">F.W. Olin Library:</a> <a href="http://library.mills.edu/">Minerva</a></span>
				</div>
			</div>
			<div class="navbar">
				<ul class="nav" role="navigation">
					<li class="dropdown">
						<a id="drop1" href="http://www.mills.edu/academics/library/index.php" role="button" class="dropdown-toggle" data-toggle="dropdown">Library Information <b class="caret"></b></a>
						<ul class="dropdown-menu" role="menu" aria-labelledby="drop1">
							<li>
								<a tabindex="-1" href="http://library.mills.edu/suggest">Suggestions</a>
							</li>
							<li>
								<a tabindex="-1" href="http://www.mills.edu/academics/library/library_information/news.php">Library News</a>
							</li>
							<li>
								<a tabindex="-1" href="http://www.mills.edu/academics/library/library_departments/circulation.php#renew">Renewals</a>
							</li>
							<li>
								<a tabindex="-1" href="http://www.mills.edu/academics/library/library_departments/circulation.php#fines">Fines and Payments</a>
							</li>
						</ul>
					</li>
					<li class="dropdown">
						<a href="http://library.mills.edu/help" id="drop2" role="button" class="dropdown-toggle" data-toggle="dropdown">Search Help <b class="caret"></b></a>
						<ul class="dropdown-menu" role="menu" aria-labelledby="drop2">
							<li>
								<a tabindex="-1" href="http://library.mills.edu/help#searching">Seearching</a>
							</li>
							<li>
								<a tabindex="-1" href="http://library.mills.edu/help#searchtips">Advanced Keyword Search Tips</a>
							</li>
							<li>
								<a tabindex="-1" href="http://library.mills.edu/help#prefsearch">Saving your searches</a>
							</li>
						</ul>
					</li>
					<li>
						<a href="http://library.mills.edu/screens/connect.html">Connect with a Librarian</a>
					</li>
					<li>
						<a href="http://www.mills.edu/academics/library/library_information/faq.php">Connect with a Librarian</a>
					</li>
				</ul>
			</div>
		</div>
	</div>
	<div class="row-fluid searchbar">
		<div class="span12">
			<select name="specializedSearch" id="specializedSearch">
				<option selected="selected" value=" ">
					Search Options
				</option>
				<option value="http://library.mills.edu/search/X">
					Advanced Keyword
				</option>
				<option value="http://library.mills.edu/search/t">
					Title
				</option>
				<option value="http://library.mills.edu/search/a">
					Author
				</option>
				<option value="http://library.mills.edu/search/q">
					Author and Title
				</option>
				<option value="http://library.mills.edu/search/d">
					Subject
				</option>
				<option value="http://library.mills.edu/search/s">
					Periodical Title
				</option>
				<option value="http://library.mills.edu/search/r">
					Course Reserve
				</option>
				<option value="http://library.mills.edu/search/y">
					Electronic Resource
				</option>
				<option value="http://library.mills.edu/search/c">
					Call Number
				</option>
				<option value="http://library.mills.edu/search/i">
					ISBN/ISSN/Music No.
				</option>
				<option value="http://library.mills.edu/search/l">
					LCCN
				</option>
				<option value="http://library.mills.edu/search/f">
					Publication No.
				</option>
				<option value="http://library.mills.edu/search/g">
					Sudocs No.
				</option>
				<option value="http://library.mills.edu/search/o">
					Utility No.
				</option>
				<option value="http://library.mills.edu/search/b">
					Barcode
				</option>
			</select>
			<input type="image" src="http://library.mills.edu/screens/ico_go_orange.png" class="searchNavBut" value="Go" onclick="GotoURL();">
		</div>
	</div>

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
	//print_r($xml);
	//$shelves = $xml->xpath("//user_shelf[not (name='read' or name='to-read' or name='currently-reading')]");
	$shelves = $xml->xpath("//user_shelf[not (name='read' or name='to-read')][not (starts-with(name,'weekly'))]");
	//print_r($shelves);

	print "<form action=\"" . $_SERVER['PHP_SELF'] . "\" method=\"get\">";
	print "<select name=\"list\">";
	print "<option>Select Book List</option>";


// <?php if($_POST['YourCountry']=="Argentina") echo "selected=selected

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
	print "</select>\n";
	print "<input type=\"submit\" value=\"Submit\">\n";
	print "</form>\n";
}

?>

	<div class="row-fluid maincontent">
		<div class="span4"></div>
		<div class="span4">
			<div class="covercontainer">
				<div class="row-fluid feedmenu">
					<div class="span12">
						<?php feed_menu($shelflists); ?>
					</div>
				</div>
				<?php

				if(isset($_GET["list"])) {
					require "PGFeed.php";
					$p = new PGFeed;
					$p->setOptions(0,200,0,NULL);

					$post = $_GET["list"];
					//$source = $_GET["feed"];
					$source = $baserssurl.$post;
					//$source = "http://www.goodreads.com/review/list_rss/14996177?shelf=history-new-books";
					//echo $source;
					$p->parse($source);
					$channel = $p->getChannel();
					//var_dump($channel);

					$shelf = $channel["title"];
					$feedtitle = titlecap($shelf);

					if($feedtitle=="Currently Reading") {
						$feedtitle = "New Arrivals";
					}

					?>
					<div class="row">
						<div class="span12">
							<h2><?php echo $feedtitle;?></h2>
						</div>
					</div>


					<?php
					$items = $p->getItems();     // gets news items

					foreach (array_slice($items,1) as $i) {
						//var_dump($i);
						$desc = string_sanitize($i["book_description"]);
						$review = $i["user_review"];
						$coverurl = $i["book_large_image_url"];
						if (preg_match("/nocover/i", $coverurl)) {
							$coverurl= "http://www.goodreads.com" . $coverurl;
						}
						//print $desc;
						print "<br />";
						print "<div class=\"bookcover description\" data-trigger=\"hover\" data-title=\"" . $i["title"] . "\" data-content=\"" . $desc . "\"><a href=\"http://library.mills.edu/search/i?SEARCH=" . $i["isbn"] . "&sortdropdown=-&searchscope=6\" target=\"_parent\"><img src=\"" . $coverurl . "\" alt=\"\"></a></div>";
						print "<div class=\"booktitle\"><a href=\"http://library.mills.edu/search/i?SEARCH=" . $i["isbn"] . "&sortdropdown=-&searchscope=6\" target=\"_parent\">" . $i["title"] . "</a></div>";
						print "<div class=\"bookauthor\">by " . $i["author_name"] . "</div>";
						print "<div class=\"bookauthor\">Call Number: " . $review . "</div>";
						print "<hr>";
					}
				?>
			</div> <!-- close cover container -->
		</div> <!-- close middle span4 -->
		<div class="span4"></div> <!-- empty third column -->
	</div> <!-- close row-fluid maincontent -->

	<?php
		} else {
	?>
		 </div> <!-- close cover container -->
			</div> <!-- close middle span4 -->
			<div class="span4"></div> <!-- empty third column -->
	</div> <!-- close row-fluid maincontent -->
	<?php
		}
	?>
</div>


	<!-- javascript
	================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
	<script src="js/jquery.js"></script>
	<script src="js/bootstrap.js"></script>

<script>
$(document).ready(function() {
	$(".description").popover();
});
</script>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-40625016-4', 'mills.edu');
  ga('send', 'pageview');

</script>


	</body>
</html>
