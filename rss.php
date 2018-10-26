<?php
 
	 //csv file
	$csvfile = "ImageShareSchedule.csv"; //change this
	
	//cdm data
	$cdmcollection = "p16007coll33"; //change this
	$cdmserver = "server16007"; //change this
	$basecdmurl = "http://ohiomemory.org"; //change this - probably too https://$cdmserver.contentdm.oclc.org/ - you can view this by navigating to your collection and looking at the URL
	
    //bit.ly
    $login = "bitly-login"; //change this
	$appkey = "bitly-app-key"; //change this
	$format = "txt";
    
    function get_bitly_short_url($url,$login,$appkey,$format='txt') {
	$connectURL = 'http://api.bit.ly/v3/shorten?login='.$login.'&apiKey='.$appkey.'&uri='.urlencode($url).'&format='.$format;
	//echo $connectURL;
	
     $ch2 = curl_init();
     curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
     curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
     curl_setopt($ch2, CURLOPT_URL, $connectURL);
     $result = curl_exec($ch2);
     curl_close($ch2);
	
     $obj = json_decode($result, true);
     //echo $result;
     return $result;

	}
	
 
 $today = getdate();
 $d = $today['mday'];
 $m = $today['mon'];
 $y = $today['year'];
 $currentdate = "$m/$d/$y";
 
 //echo $currentdate;
// echo "<br/>";

 //$row = 4;
 //$column = 2;
 //echo $csv[$row][$column];

 $csv = array();
 $x=0;


 if(($handle = fopen($csvfile, "r")) !== FALSE)
 {
    while(($data = fgetcsv($handle, 1000, ",")) !== FALSE)
    {
    	$csv[] = $data;
    	$rdate = $csv[$x][0];
    	//echo "x = $x | date = $rdate";
    	//echo "</br>";
    	
    	if ($currentdate == $rdate) {
    			//echo "x = $x | date = $rdate";
    			//echo "</br>";
    			$cdmmdpointer = $csv[$x][1];
    			$cdmimgpointer = $csv[$x][2];
    			$cdmimg = "$basecdmurl/digital/iiif/$cdmcollection/$cdmimgpointer/full/400,200/0/default.jpg";
    			$cdmmd = "https://$cdmserver.contentdm.oclc.org/dmwebservices/index.php?q=dmGetItemInfo/$cdmcollection/$cdmmdpointer/json";
    			
    			
$ch2 = curl_init();
curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch2, CURLOPT_URL, "https://$cdmserver.contentdm.oclc.org/dmwebservices/index.php?q=dmGetItemInfo/$cdmcollection/$cdmmdpointer/json");
$result2 = curl_exec($ch2);
curl_close($ch2);    

$obj2 = json_decode($result2, true);
//echo $ojb2;
$title = $obj2['title']; 
//echo $title;
//$title = mysql_real_escape_string($title);
    			
    			
				$cdmlink = "$basecdmurl/cdm/singleitem/collection/$cdmcollection/id/$cdmimgpointer/rec/1";
				
				//Keep bit.ly
				$bitlycdmlink = get_bitly_short_url($cdmlink,$login,$appkey);

				//Remove bit.ly
				//$bitlycdmlink = $cdmlink;
    		
    			
//RSS FEED CREATION   			
echo '<?xml version="1.0" encoding="UTF-8" ?>
<rss version="2.0">
<channel>
<title>'.$title.'</title>
<link>'.$bitlycdmlink.'</link>
<description>'.$cdmimg.'</description>
<item>
<title>'.$title.'</title>
<link>'.$bitlycdmlink.'</link>
<description>'.$cdmimg.'</description>
</item>
</channel>
</rss>';    			
    			
    		}
    	$x++;
    }
 }

 fclose($handle);



?>