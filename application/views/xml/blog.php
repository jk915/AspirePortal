<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/" 
    xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
    xmlns:admin="http://webns.net/mvcb/"
    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
    xmlns:content="http://purl.org/rss/1.0/modules/content/">
    <channel>
    	<title><?php echo isset($rss_title) ? $rss_title: ""; ?></title>
    	<link><?php echo isset($rss_url) ? $rss_url : ""; ?></link>
    	<description><?php echo isset($rss_description) ? $rss_description : ""; ?></description>
    	<dc:language><?php echo isset($rss_language) ? $rss_language : "en-us"; ?></dc:language>
    	<dc:creator><?php echo isset($rss_creator) ? $rss_creator : ""; ?></dc:creator>
    	<dc:rights>Copyright <?php echo gmdate("Y", time()); ?></dc:rights>
    	<admin:generatorAgent rdf:resource="http://www.codeigniter.com/" />
    <?php foreach($articles->result() as $article): ?>
        <item>
        	<title><?php echo $this->utilities->xml_encode($article->article_title); ?></title>
          	<link><?php echo $detail_url . $article->article_code; ?></link>
          	<guid><?php echo $detail_url . $article->article_code; ?></guid>
          	<description><![CDATA[
            <?php echo $article->short_description;?>
            <br/><a href="<? echo $detail_url . $article->article_code; ?>">read more</a>
        ]]></description>
      		<pubDate><?php 
      			$temp_date = explode(" ",$article->pubDate);
      			$temp_YMD= explode("-",$temp_date[0]); //year-month-day
      			$temp_HMS= explode(":",$temp_date[1]); //hour-minute-second
    			//echo date($entry->AvailabilityDate); 'L, F d, Y g:i A', 
      			//echo date('r',mktime($temp_HMS[0], $temp_HMS[1], $temp_HMS[2], $temp_YMD[1], $temp_YMD[2], $temp_YMD[0]));
      			echo standard_date('DATE_RSS',mktime($temp_HMS[0], $temp_HMS[1], $temp_HMS[2], $temp_YMD[1], $temp_YMD[2], $temp_YMD[0]));
      		?></pubDate>
        </item>
    <?php endforeach; ?>
    </channel>
</rss>