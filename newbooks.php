<?php
include('../includes/header.html');
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

	print "<form class=\"form-inline\" role=\"form\" action=\"" . $_SERVER['PHP_SELF'] . "\" method=\"get\">";
	print "<div class=\"form-group\">";
			print "<select class=\"form-control\" name=\"list\">";
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
			print "</select>";
	print "</div>";
	print "<button type=\"submit\" class=\"btn btn-default\">Select</button>";
	print "</form>\n";
}

?>

	<div class="row maincontent">
		<div class="col-md-12">
			<div class="row contentcenter">
				<div class="col-md-4"></div>
				<div class="col-md-4">
					<?php feed_menu($shelflists); ?>
				</div>
				<div class="col-md-4"></div>
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
	</div> <!-- close row-fluid maincontent -->

	<?php
		} else {
	?>
		 </div> <!-- close cover container -->
			</div> <!-- close middle span4 -->
	</div>
	<?php
		}
	?>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-40625016-4', 'mills.edu');
  ga('send', 'pageview');

</script>

<?php
include('../includes/footer.html');
?>

<!-- Bootstrap Popover -->
<script>
$('[data-toggle="popover"]').popover({
		trigger: 'hover'
		});
</script>
