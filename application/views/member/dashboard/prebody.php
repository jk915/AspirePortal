	<script type="text/javascript" src="<?php echo base_url();?>js/member/jquery.corner.js"></script>
	<script type="text/javascript">
	 	var fadeDuration=2000;
		var slideDuration=4000;
		var currentIndex=1;
		var nextIndex=1;
		$(document).ready(function()
		{
			$('ul.slide_featured_property li img').corner();
			$('ul.slide_featured_property li').css({opacity: 0.0});
			$("'ul.slide_featured_property li:nth-child("+nextIndex+")'").addClass('show').animate({opacity: 1.0}, fadeDuration);
			var timer = setInterval('nextSlide()',slideDuration);
		})

		function nextSlide(){
	
				nextIndex =currentIndex+1;
				
				if(nextIndex > $('#slide li').length)
				{
					nextIndex =1;
					//alert(nextIndex);
					//alert(currentIndex);
				}
				
				$("#slide li:nth-child("+nextIndex+")").addClass('show').animate({opacity: 1.0}, fadeDuration);
				$("#slide li:nth-child("+currentIndex+")").animate({opacity: 0.0}, fadeDuration).removeClass('show');
				currentIndex = nextIndex;
				
		}
		
		var fadeDuration=2000;
		var slideDuration=4000;
		var article_currentIndex=1;
		var article_nextIndex=1;
		$(document).ready(function()
		{
			$('ul.slide_featured_article li img').corner();
			$('ul.slide_featured_article li').css({opacity: 0.0});
			//alert("'ul.slide_featured_article li:nth-child("+article_nextIndex+")'");
			$("'ul.slide_featured_article li:nth-child("+article_nextIndex+")'").addClass('show').animate({opacity: 1.0}, fadeDuration);
			var timer = setInterval('nextSlide1()',slideDuration);
		})

		function nextSlide1(){
					article_nextIndex =article_currentIndex+1;
				//alert($('#slide1 li').length);
				if(article_nextIndex > $('#slide1 li').length)
				{
					article_nextIndex =1;
					// alert(article_nextIndex);
					// alert(article_currentIndex);
				}
				
				$("#slide1 li:nth-child("+article_nextIndex+")").addClass('show').animate({opacity: 1.0}, fadeDuration);
				$("#slide1 li:nth-child("+article_currentIndex+")").animate({opacity: 0.0}, fadeDuration).removeClass('show');
				article_currentIndex = article_nextIndex;
			
		}
		

	</script>
</head>
