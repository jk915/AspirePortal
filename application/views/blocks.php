<?php
	$CI =& get_instance();
	
	$cache_key = "SIDEBAR_" . $foreign_id . "_" . $position;
	
	if(($use_cache) && (cache_exists($cache_key)))
	{
		echo cache_read($cache_key);				
	}
	else
	{
		// Load all blocks assigned to this article.
		$CI->load->model('blocks_model');
		
		$blocks = $CI->blocks_model->get_assigned_blocks($foreign_id, $assignment_type, $position);
		if(!$blocks)
		{
			$blocks = $CI->blocks_model->get_assigned_blocks(SIDEBAR_TEMPLATE_PAGEID, $assignment_type, $position);	
		}

		// If blocks were returned, loop through them and output the block contents.
		if($blocks)
		{
			$i = 1;
			
			$html = "";
			
			foreach($blocks->result() as $block)
			{
				if($i > 1)
				{
					$html .= '<div class="divider"></div>';
				}
				
				$block_html = $block->block_content; 
				$block_html = $CI->utilities->replaceTags($CI, $block_html);

				$html .= $block_html;
				$i++;
			}
			
			if($use_cache)
			{
				cache_write($cache_key, $html);
			}
			
			echo $html;			
		}		
	}             