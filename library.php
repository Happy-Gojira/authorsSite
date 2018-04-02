<?php
if (!isset($_GET['lib']) || $_GET['lib']=='') { 
	header("Location: index.php"); 
	exit();
} 
?>
<?php include("header_lib.php") ?>
<div align="center"><a href="index.php">Back to SALS Directory</a></div>

<?php 
	$link = mysql_connect('localhost', 'directory', 'm.us[');
	if (!$link) {
		die('Could not connect: ' . mysql_error());
	}
	$db_selected = mysql_select_db('directory', $link);
	$query=sprintf("SELECT * FROM library WHERE code='".$_GET['lib']."'");
	$result = mysql_query($query);
	if (!$result) {
   		$message  = 'Invalid query: ' . mysql_error() . "\n";
   		$message .= 'Whole query: ' . $query;
   		die($message);
	}
	$row = mysql_fetch_assoc($result);
	
	printf("<h2 align='center'>".$row['library']."</h2>");
?>
<br>
<div align="center">
  
  <?php 
  $url=sprintf("http://salsblog.sals.edu/Library_pictures/".strtolower($row['code']).".jpg");
  $fp=fopen("$url","rb");
  if($fp){
fclose($fp);
 ?>
  <p><img src="<?php printf($url);?>" alt="<?php printf($row['library']);?>"></p>
  <?php } ?>
  <table border="0" cellspacing="5" cellpadding="5">
    <tr>
      <td valign="top" width="150"><strong>County</strong></td>
      <td valign="top"><?php printf($row['county']); ?></td>
    </tr>
    <tr>
      <td valign="top"><strong>Phone/Fax</strong></td>
      <td valign="top"><?php 
		$phone=sprintf($row['phone']);
		if($row['fax']!='' && $row['fax']!=$row['phone']){
			$phone=sprintf($phone."/".$row['fax']);
		}
		printf($phone);
		?></td>
    </tr>
	<?php 
		if($row['email']!=''){
    		printf("<tr><td valign='top'><strong>Library Email</strong></td><td valign='top'><a href='mailto:".$row['email']."'>".$row['email']."</a></td></tr>");
		}
	?>
    <?php 
		if($row['website']!=''){
    		printf("<tr><td valign='top'><strong>Library Website</strong></td><td valign='top'><a href='http://".$row['website']."'>".$row['website']."</a></td></tr>");
		}
	?>
    <tr>
      <td valign="top"><strong>Wireless Access</strong></td>
      <td valign="top">
	  	<?php if($row['wireless']=='' OR $row['wireless']=='0'){
  					$wireless="No";
				} else { $wireless="Yes"; }
				printf($wireless);
		?>	  </td>
    </tr>
    <tr>
	<?php
		$addy=sprintf($row['address1']."<br>");
		$addy2=$row['address1'];
		if($row['address2']!=''){
			$addy=sprintf($addy.$row['address2']."<br>");
		}
		$addy=sprintf($addy.$row['city'].", ".$row['state']." ".$row['zip']);
		$addy2=sprintf($addy2." ".$row['city'].", ".$row['state']." ".$row['zip']);
	?>
      <td valign="top"><strong>Address</strong>
	  <?php if($row['code'] <> 'LGL' and $row['code'] <> 'EAS'){ printf("<br><strong><a href='http://maps.google.com/maps?f=q&amp;hl=en&amp;q=".$addy2."'>Directions and Map</a></strong>");}
	  
	  if($row['code']=='EAS'){ printf("<br><strong><a href='http://maps.google.com/maps?f=q&amp;hl=en&amp;q=".$addy2."+(Easton+Library)+@43.012758,-73.550195'>Directions and Map</a></strong>");}
	  
	  if($row['code']=='LGL'){ printf("<br><strong><a href='http://maps.google.com/maps?f=q&amp;hl=en&amp;q=43.9722,-74.4205+(Long+Lake+Library,+6+Main+St.+Long+Lake,+NY+12847)'>Directions and Map</a></strong>");}
	  ?></td>
	  
      <td valign="top"><?php 
		
		printf($addy);
	?></td>
    </tr>
    <?php /*if ($row['code']=='LGL') { ?>
	  	<tr><td valign="top"><strong>Directions</strong></td>
			<td valign="top">Take Exit 23 from I87. Follow Route 9 north to Warrensburg. Just outside of the town, take Route 28 north to Blue Mountain Lake. Turn right in Blue Mountain Lake on to Route 30 to Long Lake. The library is on Route 30 in the town, next to Key Bank.</td></tr>
	  <?php }*/?>
    <tr>
      <td valign="top"><strong>Director</strong></td>
      <td valign="top">
	  <?php
	  	$query2=sprintf("SELECT name FROM director WHERE lib_id=".$row['id']);
		$result2 = mysql_query($query2);
		if (!$result2) {
   			$message  = 'Invalid query: ' . mysql_error() . "\n";
	   		$message .= 'Whole query: ' . $query2;
   			die($message);
		}
		$dir = mysql_fetch_assoc($result2);
		printf($dir['name']);
	?>
	  </td>
    </tr>
    <tr>
      <td valign="top"><strong>Hours</strong></td>
      <td valign="top">
	  <?php
		$query3=sprintf("SELECT distinct type FROM hours WHERE lib_id=".$row['id']." ORDER BY type DESC");
		$result3 = mysql_query($query3);
		if (!$result3) {
   			$message  = 'Invalid query: ' . mysql_error() . "\n";
	   		$message .= 'Whole query: ' . $query3;
   			die($message);
		}
		while($type = mysql_fetch_assoc($result3)) { printf("<p align='left'>");
			if($type['type']=='Year Round') { printf("<table cellpadding=0 cellspacing=0 border=0>");
			} else { 
				printf("<strong>".$type['type']." Hours</strong>"); 
				if($type['type']=='Winter' AND $row['winter_begin']!=''){ 
					printf("<br><strong>(From ".$row['winter_begin']." to ".$row['winter_end'].")</strong>");
				} elseif ($type['type']=='Summer' AND $row['sum_begin']!=''){ 
					printf("<br><strong>(From ".$row['sum_begin']." to ".$row['sum_end'].")</strong>");
				}
				printf("<br><table cellpadding=0 cellspacing=0 border=0>");
			}
			$query4=sprintf("SELECT hours FROM hours WHERE lib_id=".$row['id']." AND day='mon' AND type='".$type['type']."'");
			$result4 = mysql_query($query4);
			if (!$result4) {
   				$message  = 'Invalid query: ' . mysql_error() . "\n";
	   			$message .= 'Whole query: ' . $query3;
   				die($message);
			}
			$mon = mysql_fetch_assoc($result4);
			if($mon['hours']=='') { $mon1="Closed"; } else { $mon1=sprintf($mon['hours']); }
			printf("<tr><td valign='top'>Monday</td><td valign='top'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$mon1."</td>");
			$query5=sprintf("SELECT hours FROM hours WHERE lib_id=".$row['id']." AND day='tue' AND type='".$type['type']."'");
			$result5 = mysql_query($query5);
			if (!$result5) {
   				$message  = 'Invalid query: ' . mysql_error() . "\n";
	   			$message .= 'Whole query: ' . $query5;
   				die($message);
			}
			$tue = mysql_fetch_assoc($result5);
			if($tue['hours']=='') { $tue1="Closed"; } else { $tue1=sprintf($tue['hours']); }
			printf("<tr><td valign='top'>Tuesday</td><td valign='top'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$tue1."</td>");
			
			$query6=sprintf("SELECT hours FROM hours WHERE lib_id=".$row['id']." AND day='wed' AND type='".$type['type']."'");
			$result6 = mysql_query($query6);
			if (!$result6) {
   				$message  = 'Invalid query: ' . mysql_error() . "\n";
	   			$message .= 'Whole query: ' . $query6;
   				die($message);
			}
			$wed = mysql_fetch_assoc($result6);
			if($wed['hours']=='') { $wed1="Closed"; } else { $wed1=sprintf($wed['hours']); }
			printf("<tr><td valign='top'>Wednesday</td><td valign='top'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$wed1."</td>");
			
			$query7=sprintf("SELECT hours FROM hours WHERE lib_id=".$row['id']." AND day='thu' AND type='".$type['type']."'");
			$result7 = mysql_query($query7);
			if (!$result7) {
   				$message  = 'Invalid query: ' . mysql_error() . "\n";
	   			$message .= 'Whole query: ' . $query7;
   				die($message);
			}
			$thu = mysql_fetch_assoc($result7);
			if($thu['hours']=='') { $thu1="Closed"; } else { $thu1=sprintf($thu['hours']); }
			printf("<tr><td valign='top'>Thursday</td><td valign='top'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$thu1."</td>");
			
			$query8=sprintf("SELECT hours FROM hours WHERE lib_id=".$row['id']." AND day='fri' AND type='".$type['type']."'");
			$result8 = mysql_query($query8);
			if (!$result8) {
   				$message  = 'Invalid query: ' . mysql_error() . "\n";
	   			$message .= 'Whole query: ' . $query8;
   				die($message);
			}
			$fri = mysql_fetch_assoc($result8);
			if($fri['hours']=='') { $fri1="Closed"; } else { $fri1=sprintf($fri['hours']); }
			printf("<tr><td valign='top'>Friday</td><td valign='top'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$fri1."</td>");
			
			$query9=sprintf("SELECT hours FROM hours WHERE lib_id=".$row['id']." AND day='sat' AND type='".$type['type']."'");
			$result9 = mysql_query($query9);
			if (!$result9) {
   				$message  = 'Invalid query: ' . mysql_error() . "\n";
	   			$message .= 'Whole query: ' . $query9;
   				die($message);
			}
			$sat = mysql_fetch_assoc($result9);
			if($sat['hours']=='') { $sat1="Closed"; } else { $sat1=sprintf($sat['hours']); }
			printf("<tr><td valign='top'>Saturday</td><td valign='top'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$sat1."</td>");
			
			$query10=sprintf("SELECT hours FROM hours WHERE lib_id=".$row['id']." AND day='sun' AND type='".$type['type']."'");
			$result10 = mysql_query($query10);
			if (!$result10) {
   				$message  = 'Invalid query: ' . mysql_error() . "\n";
	   			$message .= 'Whole query: ' . $query10;
   				die($message);
			}
			$sun = mysql_fetch_assoc($result10);
			if($sun['hours']=='') { $sun1="Closed"; } else { $sun1=sprintf($sun['hours']); }
			printf("<tr><td valign='top'>Sunday</td><td valign='top'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$sun1."</td>");
		
		printf("</table></p>");
	}
	?>
	  </td>
    </tr>
    <tr>
      <td valign="top"><strong>Holidays</strong></td>
      <td valign="top"><?php 
		$query12=sprintf("SELECT lh.note,h.holiday FROM lib_holiday lh INNER JOIN holidays h ON h.id=lh.holiday_id WHERE lh.lib_id=".$row['id']." ORDER BY holiday_id");
			$result12 = mysql_query($query12);
			if (!$result12) {
   				$message  = 'Invalid query: ' . mysql_error() . "\n";
	   			$message .= 'Whole query: ' . $query12;
   				die($message);
			}
			$hols="";
			while($hol = mysql_fetch_assoc($result12)) {
				$hols=sprintf($hols.$hol['holiday']);
				if($hol['note']!=''){ $hols=sprintf($hols." (".$hol['note'].")"); }
				$hols=sprintf($hols.", ");
			}
			$lgth=strlen($hols)-2;
			$hols2=substr($hols, 0,$lgth);
			printf($hols2);
	?></td>
    </tr>
    <tr>
      <td valign="top"><strong>Type of Library</strong></td>
      <td valign="top"><?php printf($row['lib_type']);?></td>
    </tr>
	<?php if($row['population']!='') { printf("<tr><td valign='top'><strong>Chartered Population</strong></td><td valign='top'>".$row['population']."</td></tr>"); }
	if($row['code']!='SALS'){
	?>	
    <tr>
      <td valign="top"><strong>Statistics</strong></td>
      <td valign="top"><?php
		$query11=sprintf("SELECT * FROM stats WHERE lib_id=".$row['id']);
			$result11 = mysql_query($query11);
			if (!$result11) {
   				$message  = 'Invalid query: ' . mysql_error() . "\n";
	   			$message .= 'Whole query: ' . $query11;
   				die($message);
			}
			$stats = mysql_fetch_assoc($result11);
			printf("<strong>Annual Report For ".$stats['yr']."</strong> <br>");
			printf("Library Materials Owned: ".$stats['mat_owned']."<br>");
			printf("Library Materials Borrower: ".$stats['mat_borrow']."<br>");
			printf("Library Visits: ".$stats['visits']."<br>");
			printf("Programs Held: ".$stats['progs']."<br>");
			printf("Public Computers: ".$stats['comps']);
	?></td>
    </tr>
 	<?php }
			$query7=sprintf("select d.district FROM districts d INNER JOIN lib_district ld ON ld.dis_id=d.id WHERE d.type='Assembly' AND ld.lib_id=".$row['id']);
		$ass = mysql_query($query7);
		if (!$ass) {
   			$message  = 'Invalid query: ' . mysql_error() . "\n";
   			$message .= 'Whole query: ' . $query7;
   			die($message);
		}
		if(mysql_num_rows($ass) > 1){
			while($ass_row = mysql_fetch_assoc($ass)) {
				$ass_dist=sprintf($ass_dist.$ass_row['district']." and ");
			}
			$lgth=strlen($ass_dist)-5;
			$ass_dist2=substr($ass_dist, 0,$lgth);    
		} else { $ass_row = mysql_fetch_assoc($ass); $ass_dist2=sprintf($ass_row['district']); }
		
		$query8=sprintf("select d.district FROM districts d INNER JOIN lib_district ld ON ld.dis_id=d.id WHERE d.type='Congressional' AND ld.lib_id=".$row['id']);
		$con = mysql_query($query8);
		if (!$con) {
   			$message  = 'Invalid query: ' . mysql_error() . "\n";
   			$message .= 'Whole query: ' . $query8;
   			die($message);
		}
		if(mysql_num_rows($con) > 1){
			while($con_row = mysql_fetch_assoc($con)) {
				$con_dist=sprintf($con_dist.$con_row['district']." and ");
			}
			$lgth=strlen($con_dist)-5;
			$con_dist2=substr($con_dist, 0,$lgth);    
		} else { $con_row = mysql_fetch_assoc($con);$con_dist2=sprintf($con_row['district']); }
		
		$query9=sprintf("select d.district FROM districts d INNER JOIN lib_district ld ON ld.dis_id=d.id WHERE d.type='Judicial' AND ld.lib_id=".$row['id']);
		$jud = mysql_query($query9);
		if (!$jud) {
   			$message  = 'Invalid query: ' . mysql_error() . "\n";
   			$message .= 'Whole query: ' . $query9;
   			die($message);
		}
		if(mysql_num_rows($jud) > 1){
			while($jud_row = mysql_fetch_assoc($jud)) {
				$jud_dist=sprintf($jud_dist.$jud_row['district']." and ");
			}
			$lgth=strlen($jud_dist)-5;
			$jud_dist2=substr($jud_dist, 0,$lgth);    
		} else { $jud_row = mysql_fetch_assoc($jud);$jud_dist2=sprintf($jud_row['district']); }
		
		$query10=sprintf("select d.district FROM districts d INNER JOIN lib_district ld ON ld.dis_id=d.id WHERE d.type='Senatorial' AND ld.lib_id=".$row['id']);
		$sen = mysql_query($query10);
		if (!$sen) {
   			$message  = 'Invalid query: ' . mysql_error() . "\n";
   			$message .= 'Whole query: ' . $query10;
   			die($message);
		}
		if(mysql_num_rows($sen) > 1){
			while($sen_row = mysql_fetch_assoc($sen)) {
				$sen_dist=sprintf($sen_dist.$sen_row['district']." and ");
			}
			$lgth=strlen($sen_dist)-5;
			$sen_dist2=substr($sen_dist, 0,$lgth);    
		} else { $sen_row = mysql_fetch_assoc($sen);$sen_dist2=sprintf($sen_row['district']); }
	if($ass_dist2!='' || $sen_dist2!='' || $con_dist2!='' || $jud_dist2!='') {printf("<tr><td valign='top'><b>Electoral Districts</b></td><td valign='top'>");
	if($ass_dist2!=''){printf($ass_dist2." State Assembly District<br>");}
	if($sen_dist2!=''){printf($sen_dist2." State Senatorial District<br>");}
	if($con_dist2!=''){printf("$con_dist2 U.S. Congressional District<br>");}
	if($jud_dist2!=''){printf("$jud_dist2 State Judicial District");}
	printf("</td></tr>");
	}
	?>

<?php
//Board
	$query5=sprintf("SELECT name,president FROM trustees WHERE lib_id=".$row['id']." ORDER BY president DESC,SUBSTRING(name, (length(name)-locate(' ',reverse(name))+1)),name");
	$result5 = mysql_query($query5);
	if (!$result5) {
   		$message  = 'Invalid query: ' . mysql_error() . "\n";
		$message .= 'Whole query: ' . $query5;
   		die($message);
	}
	$num=mysql_num_rows($result5);
	if($num>0){
    	printf("<tr><td valign='top'><strong>Board of Trustees</strong></td><td valign='top'>");
		while($brd= mysql_fetch_assoc($result5)) {
			printf($brd['name']);
			if ($brd['president']==1){ printf(" (President)"); }
			printf("<br>");
		}
		printf("</td></tr>");
	}
?>
<?php
	$query12=sprintf("SELECT name,position FROM fol WHERE lib_id=".$row['id']." ORDER BY SUBSTRING(name, (length(name)-locate(' ',reverse(name))+1)),name,position");
	$result12 = mysql_query($query12);
	if (!$result12) {
   		$message  = 'Invalid query: ' . mysql_error() . "\n";
		$message .= 'Whole query: ' . $query12;
   		die($message);
	}
	$num=mysql_num_rows($result12);
	if($num>0){
    	printf("<tr><td valign='top'><strong>Friends of the Library</strong></td><td valign='top'>");
		while($fol= mysql_fetch_assoc($result12)) {
			printf($fol['name']);
			if ($fol['position']!=1){ printf(" (".$fol['position'].")"); }
			printf("<br>");
		}
	    printf("</td></tr>");
	}
?>
   
  </table>
 
</div>
<p></p>
<div align="center"><a href="index.php">Back to SALS Directory</a>
<?php 
mysql_free_result($result);
mysql_free_result($result2);
mysql_free_result($result3);
mysql_free_result($result4);
mysql_free_result($result5);
mysql_free_result($result6);
mysql_free_result($result7);
mysql_free_result($result8);
mysql_free_result($result9);
mysql_free_result($result10);
mysql_free_result($result11);
mysql_free_result($result12);
include("footer.php") ?>
