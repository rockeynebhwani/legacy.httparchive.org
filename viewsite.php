<?php 
/*
Copyright 2010 Google Inc.

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

     http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
*/

require_once("ui.php");
require_once("utils.php");

$gPageid = ( array_key_exists('pageid', $_GET) ? $_GET['pageid'] : "" );
$gArchive = ( array_key_exists("a", $_GET) ? $_GET["a"] : "" );
// TODO - better error handling starting here!
$query = "select harfile from $gPagesTable where pageid=$gPageid;";
$harfile = doSimpleQuery($query);
$query = "select url from $gPagesTable where pageid=$gPageid;";
$url = doSimpleQuery($query);

$gTitle = "View Site";
?>
<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>HTTP Archive - <?php echo $gTitle ?></title>
<link rel="stylesheet" href="har.css" type="text/css">

<?php echo globalCss() ?>
<style>
#stats { font-size: 0.8em; font-family: Arial; }
#stats TD { border-left: 1px solid #CCC; padding: 4px; }
#stats TH { border-left: 1px solid #CCC; padding: 4px; }
.odd { background: #F0F0F0; }
.avg { background: #FDE0B5; }
.nobr { white-space: nowrap; }
</style>
</head>

<body>
<?php echo uiHeader($gTitle); ?>

<div style="margin-top: 16px;">
<span style="font-size: 1.5em;"><?php echo siteLink($url) ?></span>
<span style='margin-left: 20px; font-size: 0.8em;'><a href='viewarchive.php?a=<?php echo $gArchive ?>'>back to <?php echo $gArchive ?></a></div>
</div>

<div style="margin-top: 20px; margin-bottom: 10px; font-weight: bold; font-size: 1.5em; border-bottom: 0px solid;">
HTTP Waterfall
</div>

<div id=harviewer>
<div id=pageliststeve></div>
<div class=tabDOMBody id=tabDOMBody></div>
</div>


<script src='schema.js'></script>
<script src='har.js'></script>
<script src='harviewer.js?f=<?php echo $harfile ?>'></script>

<script>
function initHAR() {
	var _3cb = document.getElementById('pageliststeve');
	var _3cc = HARjson;
	if ( _3cc ) {
		// in IE there's a race condition
		if ( "undefined" === typeof(HAR.Model) ) {
			setTimeout(initHAR, 1000);
			return;
		}

		HAR.Model.appendData(_3cc);
		HAR.Tab.Preview.append(_3cc, _3cb);
		var _3ce = document.getElementById("tabDOMBody");
		_3ce.updated = false;
	}
};

initHAR();
</script>

<div style="margin-top: 20px; margin-bottom: 10px; border-bottom: 0px solid;">
<span style="font-weight: bold; font-size: 1.5em;">Requests</span>
<a style="font-size: 0.8em; margin-left: 20px;" href="download.php?p=<?php echo $gPageid ?>&format=csv">download CSV</a>
</div>

<table id=stats class=sortable border=0 cellpadding=0 cellspacing=0 style="border: 1px solid #CCC; border-left: 0; margin-bottom: 20px;">
	<tr>
<th class="sorttable_numeric">req#</th> 
<th>URL</th> 
<th>mime type</th>
<th>method</th>
<th class=sorttable_numeric>status</th>
<th class="sorttable_numeric">time</th> 
<th class=sorttable_numeric>response<br>Size</th>
<th class=sorttable_numeric>request<br>Cookie Len</th>
<th class=sorttable_numeric>response<br>Cookie Len</th>
<th>request<br>Http&nbsp;Ver</th>
<th>response<br>Http&nbsp;Ver</th>
<th>request Accept</th>
<th>request Accept-Charset</th>
<th>request Accept-Encoding</th>
<th>request Accept-Language</th>
<th>request Connection</th>
<th>request Host</th>
<th>request Referer</th>

<th>response<br>Accept-Ranges</th>
<th>response<br>Age</th>
<th>response<br>Cache-Control</th>
<th>response<br>Connection</th>
<th>response<br>Content-Encoding</th>
<th>response<br>Content-Language</th>
<th>response<br>Content-Length</th>
<th>response<br>Content-Location</th>
<th>response<br>Content-Type</th>
<th>response<br>Date</th>
<th>response<br>Etag</th>
<th>response<br>Expires</th>
<th>response<br>Keep-Alive</th>
<th>response<br>Last-Modified</th>
<th>response<br>Location</th>
<th>response<br>Pragma</th>
<th>response<br>Server</th>
<th>response<br>Transfer-Encoding</th>
<th>response<br>Vary</th>
<th>response<br>Via</th>
<th>response<br>X-Powered-By</th>
</tr>

<?php
// MySQL Table
$sRows = "";
$iRow = 0;
$gFirstStart = 0;

$query = "select * from $gRequestsTable where pageid = $gPageid;";
$result = doQuery($query);
if ( $result ) {
	while ($row = mysql_fetch_assoc($result)) {
		if ( !$gFirstStart ) {
			$gFirstStart = intval($row['startedDateTime']);
		}
		$iRow++;
		$sRow = "<tr" . ( $iRow % 2 == 0 ? " class=odd" : "" ) . ">";
		$sRow .= "<td class=tdnum>$iRow</td> ";
		$sRow .= "<td class=nobr style='font-size: 0.9em;'><a href='" . $row['url'] . "'>" . shortenUrl($row['url']) . "</a></td> ";
		$sRow .= tdStat($row, "mimeType", "", "nobr");
		$sRow .= tdStat($row, "method", "", "");
		$sRow .= tdStat($row, "status");
		$sRow .= tdStat($row, "time");
		$sRow .= tdStat($row, "respSize", "kB");
		$sRow .= tdStat($row, "reqCookieLen", "b");
		$sRow .= tdStat($row, "respCookieLen", "b");
		$sRow .= tdStat($row, "reqHttpVersion", "", "");
		$sRow .= tdStat($row, "respHttpVersion", "", "");
		$sRow .= tdStat($row, "req_accept", "snip", "nobr");
		$sRow .= tdStat($row, "req_accept_charset", "", "");
		$sRow .= tdStat($row, "req_accept_encoding", "", "nobr");
		$sRow .= tdStat($row, "req_accept_language", "", "");
		$sRow .= tdStat($row, "req_connection", "", "");
		$sRow .= tdStat($row, "req_host", "", "");
		$sRow .= tdStat($row, "req_referer", "url", "");
		$sRow .= tdStat($row, "resp_accept_ranges", "", "");
		$sRow .= tdStat($row, "resp_age", "", "");
		$sRow .= tdStat($row, "resp_cache_control", "", "");
		$sRow .= tdStat($row, "resp_connection", "", "");
		$sRow .= tdStat($row, "resp_content_encoding", "", "");
		$sRow .= tdStat($row, "resp_content_language", "", "");
		$sRow .= tdStat($row, "resp_content_length", "", "");
		$sRow .= tdStat($row, "resp_content_location", "url", "");
		$sRow .= tdStat($row, "resp_content_type", "", "");
		$sRow .= tdStat($row, "resp_date", "", "nobr");
		$sRow .= tdStat($row, "resp_etag", "", "");
		$sRow .= tdStat($row, "resp_expires", "", "nobr");
		$sRow .= tdStat($row, "resp_keep_alive", "", "");
		$sRow .= tdStat($row, "resp_last_modified", "", "nobr");
		$sRow .= tdStat($row, "resp_location", "url", "");
		$sRow .= tdStat($row, "resp_pragma", "", "");
		$sRow .= tdStat($row, "resp_server", "", "");
		$sRow .= tdStat($row, "resp_transfer_encoding", "", "");
		$sRow .= tdStat($row, "resp_vary", "", "");
		$sRow .= tdStat($row, "resp_via", "", "");
		$sRow .= tdStat($row, "resp_x_powered_by", "", "");

		$sRows .= $sRow;
	}
	mysql_free_result($result);
}
echo $sRows;
?>
</table>

<script type="text/javascript">
var script = document.createElement('script');
script.src = "sorttable-async.js";
script.text = "sorttable.init()";  // this is optional - without it, "sorttable.init()" is called during onload
document.getElementsByTagName('head')[0].appendChild(script);
</script>

<?php
// Page Speed
doFile($harfile);

function doFile($harfile) {
	if ( ! $harfile ) {
		return;
	}

	// If everything is ok we run it through Page Speed.
	$output = array();
	$return_var = 128;
	exec("./har_to_pagespeed '$harfile'", $output, $return_var);
	if ( 0 === $return_var ) {
		$len = count($output);
		$rules = array();
		$curRule = -1;
		for ( $i = 0; $i < $len; $i++ ) {
			$line = $output[$i];
			$matches = array();
			if ( preg_match("/_(.*)_ \(score=([0-9]+)/", $line, $matches) ) {                            // don't need to worry about 0 vs false
				if ( "Optimize images" != $matches[1] ) {
					$curRule++;
					array_push($rules, array($matches[1], $matches[2], ""));
				}
			}
			else {
				if ( -1 < $curRule ) {
					$line = fixUrls($line);
					$rules[$curRule][2] .= $line . "<br>";
				}
			}
		}
		displayRules($rules);
	}
	else { 
		echo "There was an error parsing the HAR file. $return_var";
	}

}

function displayRules($rules) {
	$ruleStrings = array();  // a hash whose keys are the rule scores
	$totalScore = 0;

	for ( $r = 0; $r < count($rules); $r++ ) {
		$rule = $rules[$r];
		$score = $rule[1];
		$totalScore += $score;

		$ruleString = displayRule($rule);
		if ( ! array_key_exists($score, $ruleStrings) ) {
			$ruleStrings[$score] = "";
		}
		$ruleStrings[$score] .= $ruleString;
	}

	$overallScore = intval(0.5 + ($totalScore/count($rules)));
	echo "<table cellpadding=0 cellspacing=0 border=0>\n" .
		"  <tr><td style='padding-top: 40px; border-bottom: 2px solid; padding-bottom: 8px;'><span style='font-size: 1.2em; padding: 0 2px 0 2px; font-weight: bold; border: 2px solid; color: " . 
		scoreColor($overallScore) .
		"'>$overallScore</span></td>" .
		"<td style='padding-top: 40px; border-bottom: 2px solid; font-weight: bold; font-size: 1.5em;'>Page Speed score</td></tr>\n";

	$scores = array_keys($ruleStrings);
	sort($scores);
	for ( $i = 0; $i < count($scores); $i++ ) {
		$score = $scores[$i];
		echo $ruleStrings[$score];
	}
	echo "</table>\n";
}


function displayRule($rule) {
	$title = $rule[0];
	$score = $rule[1];
	$details = $rule[2];

	$detailsHtml = "";
	if ( $details ) {
		$detailsHtml = "<a href='show details' alt='show details' onclick='toggleDetails(this); return false;' style='text-decoration: none;'><img src='images/right-arrow-17x14.gif' width=17 height=14 style='vertical-align: middle;' border=0></a>" .
			"<div style='position: absolute; top: 220px; padding: 8px; left: 360px; width: 500px; background: #F3F3F3; border: 1px solid #CCC; padding-left: 20px; font-size: 0.9em; display: none;'>$details</div>\n";
	}

	$html = "<tr><td valign=top style='padding-top: 0; padding-right: 8px; text-align: right; font-weight: bold; color: " . 
		scoreColor($score) .
		";'>$score</td><td style='padding-top: 0;'>$title $detailsHtml</td></tr>\n";

	return $html;
}


function scoreColor($score) {
	return ( $score >= 80 ? "#008000" : ( $score >= 60 ? "#808000" : "#9F0000" ) );
}

function fixUrls($line) {
	$pos = ( strpos($line, "http://") ? strpos($line, "http://") : strpos($line, "https://") );
	if ( $pos ) {
		$pos2 = ( strpos($line, " ", $pos+1) ? strpos($line, " ", $pos+1) : strlen($line) );
		$url = substr($line, $pos, $pos2-$pos);
		return substr($line, 0, $pos) . "<a href='$url'>" . substr($url, 0, 40) . ( strlen($url) > 40 ? "..." : "" ) . "</a>" . substr($line, $pos2);
	}

	return $line;
}

?>

</div>


<script>
var curDetails;

function toggleDetails(elem) {
	if ( curDetails ) {
		curDetails.style.display = "none";
	}

	var div = elem.parentNode.getElementsByTagName('DIV')[0];
	if ( div ) {
		div.style.display = "block";
		curDetails = div;
	}
}
</script>


<?php echo uiFooter() ?>

</body>

</html>

<?php
function tdStat($row, $field, $suffix = "", $class = "tdnum") {
	global $gFirstStart;

	$value = $row[$field];
	$snipmax = 12;

	if ( "kB" === $suffix ) {
		if ( 0 == $value ) {
			$value = "&nbsp;";
			$suffix = "";
		}
		else {
			$value = formatSize($value);
			if ( 0 == $value ) {
				$value = 1;  // don't round down to zero
			}
		}
	}
	else if ( "b" === $suffix ) {
		if ( 0 == $value ) {
			$value = "&nbsp;";
			$suffix = "";
		}
	}
	else if ( "snip" === $suffix ) {
		$suffix = "";
		if ( strlen($value) > ($snipmax+5) ) {
			$value = "<a href='javascript:' title='$value'>" . substr($value, 0, $snipmax) . "</a>";
		}
	}
	else if ( "url" === $suffix ) {
		$suffix = "";
		$url = $value;
		$iLastSlash = strrpos($url, "/");
		$sFilename = substr($url, $iLastSlash, $snipmax);
		$value = "<a href='$url'>$sFilename</a>";
	}
	else if ( "start" == $suffix ) {
		$suffix = "";
		$value = intval($value) - $gFirstStart;
	}

	if ( $class ) {
		$class = " class=$class";
	}
	
	return ( $suffix ? "<td$class>$value&nbsp;$suffix</td>" : "<td$class>$value</td>" );
}

function formatSize($num) {
	return round($num / 1000);
}

?>