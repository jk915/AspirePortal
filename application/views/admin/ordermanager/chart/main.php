	<body>
		<div class="left" id="chart_holder">
        
        	<div class="left" id="bar_chart_holder">
	        	<label for="years" class="left years">Choose a year: </label>
		      	<?php echo form_dropdown('years', query2array( $years, 'year', 'year' ), date('Y'),'id="years" class="left" style="width: 100px;"' ); ?>
		      	
		      	<div class="left" id="block_bar">
		      		<div id="bar_charts"></div>
		      	</div>
        	</div>
        	
        	<div class="left" id="pie_chart_holder">
        		<label for="pie_years" class="left years">Choose date: </label>
		      	<?php echo form_dropdown('pie_years', query2array( $years, 'year', 'year' ), date('Y'),'id="pie_years" class="left" style="width: 100px;"' ); ?>
		      	<?php echo form_dropdown('pie_months', $months, '-1','id="pie_months" class="left" style="width: 100px;margin-left: 5px;margin-right: 5px;"' ); ?>
		      	<input type="button" id="change_date" value="Go" class="button left"/>
		      	
		      	<div class="left" id="block_pie">
        			<div id="pie_charts"></div>
        		</div>
        	</div>
        	
        	<div class="left" id="product_bar_chart_holder">
        		<label for="product_years" class="left years">Choose date: </label>
		      	<?php echo form_dropdown('product_years', query2array( $years, 'year', 'year' ), date('Y'),'id="product_years" class="left" style="width: 100px;"' ); ?>
		      	<?php echo form_dropdown('product_months', $months, '-1','id="product_months" class="left" style="width: 100px;margin-left: 5px;margin-right: 5px;"' ); ?>
		      	<input type="button" id="change_product_date" value="Go" class="button left"/>
		      	
		      	<div class="left" id="block_product_bar">
        			<div id="product_bar_chart"></div>
        		</div>
        	</div> 
        	
        </div>
        <div class="clear"></div>

	</body>
</html>
         