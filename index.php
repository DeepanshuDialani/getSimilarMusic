

<!DOCTYPE html>
<html lang="en">
  <head>
  <meta http-equiv="X-Frame-Options" content="GOFORIT">
  <meta content="text/html;charset=utf-8" http-equiv="Content-Type">
  <meta content="utf-8" http-equiv="encoding">

  <script src="js/jquery-latest.js"></script>
  <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
    <link href="bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
    <title>Get Similar Music</title>
    <style type="text/css">
      html,body {
        padding-top: 1px;
        background-color: #f5f5f5;
         height: 100%;
         padding-left:8px;
         padding-right:5px;
         
      }

    </style>
    
  
 <script type="text/javascript">
        var q,plID;
        var similarartists=[];
        var similarurls=[]
        var current_id=0;
        function searchClicked(query)
        {
		q=query.value;
		similarartists.length=0;
		similarurls.length=0;
		current_id=0;
		console.log(q);
		callLastfmCorrectedQuery(q);
		document.getElementById('playselfplaylist').style.visibility='visible'; // hide  
		document.getElementById("playselfplaylist").title = "Play "+q;
		console.log("Corrected query:"+q);
 	        return false;
        }
        function callLastfmCorrectedQuery(q)
        {
        	//var retq;
        	$.ajax({
			dataType: "json",
			url: "http://ws.audioscrobbler.com/2.0/?method=artist.getcorrection&artist="+q+"&api_key=62946503db1841aa35c3a483687fda1e&format=json",
			async: false,
			success: function (msg) 
				{
					newmsg=msg;
					if(!msg.corrections.correction)	//query is correct
						console.log("correct!"+q);
					else				//query is wrong
					{
						console.log("Incorrect:"+q);
						q=msg.corrections.correction.artist.name;
					}
					callLastfmSimilarArtists(q);
					//return q;
				},
		});
    		console.log("we"+q);
    		//return false;
        }

	function callLastfmSimilarArtists(q)
	{
		
		$.ajax({
			dataType: "json",
			url: "http://ws.audioscrobbler.com/2.0/?method=artist.getsimilar&artist="+q+"&api_key=62946503db1841aa35c3a483687fda1e&format=json",
			async: false,
			success: function (msg) 
				{
					newmsg=msg;
					getSimilarArtists(newmsg);
					console.log(similarartists.length);	
					current_id=0;
   				},
    		       });

	}
	function getSimilarArtists(msg)
	{
		var artistsize=msg.similarartists.artist.length;
		var buffersize=artistsize/3;
		var i;
		$("#similarartists").empty();
		$("#similarartists").append("<li class=\"nav-header\">Similar Artists</li>");
		$("#similarartists").append("<li class=\"divider\"></li>");
		for(i=0;i<=14;i++)
		{
			similarartists.push(msg.similarartists.artist[i].name);
			 $("#similarartists").append("<li><a href='' onclick='return callYtApi(2,"+i+")'>"+msg.similarartists.artist[i].name+"</a></li>");
			similarurls.push("http://"+msg.similarartists.artist[i].url);
		}
		
		//console.log(similarartists.length);
		callYtApi(0,-1);
	}
	function callYtApi(i, qid)		//plays the first similar artist
	{
	    //console.log(similarartists.length);
            //create a JavaScript element that returns our JSON data.
            console.log(current_id);
            document.getElementById("highlight").className = "alert alert-info";
            var art_name,art_url;
            if(i==0)
            {
            	art_name=similarartists[current_id];
            	art_url=similarurls[current_id];
            	current_id=current_id+1;
            }
            else if(i==1)
            {
            	art_name=q;
            	art_url="#";
            	document.getElementById('artist_name').target="";		//todo-change url of self artist 
            }
            else if(i==2)
            {
            	art_name=similarartists[qid];
            	art_url=similarurls[qid];
                current_id=qid;
            }
            document.getElementById('artist_name').innerHTML=art_name;
            document.getElementById('artist_name').href=art_url;
            var script = document.createElement('script');
            script.setAttribute('id', 'jsonScript');
            script.setAttribute('type', 'text/javascript');
            //console.log(similarartists[current_id]);
            script.setAttribute('src', 'http://gdata.youtube.com/feeds/api/playlists/snippets?alt=json-in-script&q='+art_name+'&start-index=1&max-results=1&v=2&callback=showVideo');
            document.documentElement.firstChild.appendChild(script);
            return false;
	}
	function showVideo(data) 
	{
	  var feed = data.feed;
	  var entries = feed.entry;
	  for (var i = 0; i < entries.length; i++) 
	  {
	    var entry = entries[i];
	    plID = entry.yt$playlistId.$t;
	    //console.log(plID);
	  }
	  var site='http://www.youtube.com/embed?autoplay=1&listType=playlist&showinfo=1&list='+plID;
	  console.log(site);
	  document.getElementById('ytplayer').src = site;
	}
    </script>
    
<!--Google Analytics-->
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-39509039-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>

</head>
  <body >
  <h3 align="center">Get Similar Music</h3>
    <div class="input-append pagination-centered" id="searchbar" style="width:100%; margin:0 auto;">
    <form id="searchform" onsubmit="return searchClicked(document.getElementById('query'))" action="#" method="post">
        <input  id="query" type="text" class="input-xlarge" placeholder="Search for an artist or a band">
        <button name="submit" class="btn" title="Search" type="submit"><i class="icon-search"></i></button>
        <button id="playselfplaylist" style="visibility:hidden" class="btn"  title="Play" type="submit" onclick="return callYtApi(1,-1)"><i class="icon-play"></i></button>
       </form>
    </div>
    <div align="center" id="highlight">
    <a style="text-align:center" id="artist_name" target="_blank"></a>
    </div>
    <div class="row">
	    <div class="span11">
	    	<iframe id="ytplayer" align="center" width="100%" src="http://www.youtube.com" height="450px" frameborder="0"></iframe> 
	    </div>
	    <div class="span2" align="right">
	    	    <ul class="nav nav-list" id="similarartists" align="right">
		    </ul>
	    </div>
	</div>	 
	<br>  
	<div class="footer" align="center">
	<a class="muted" href="#" text-align="center">@deepanshu.dialani</a>
	</div>
  </body> 
</html>
<!--
todo
//-make searches more relevant
//-on back-browser
//add url/info for each artist including self
//-add footer
-->
