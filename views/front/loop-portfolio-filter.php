<?php

/* Portfolio filter */

if(isset($type) && $type == 'filtered' ){

	$allterms = get_terms( $taxonomy, array('hide_empty' => 1));
 			
 	echo '<ul class="veuse-portfolio-filter">';
	echo '<li class="active"><a href="#" class="showall" >'. __('All','ceon').'</a></li>';
	
	/* Loop through the terms */
		
 	foreach ( $allterms as $term ) {

 		if(!empty($categories))
 		{
			$needle = strpos($categories, $term ->slug);
			
			if($needle !== false)
			{
				echo '<li><a href="#" class="'. $term->slug .'">'. $term->name .'></a></li>';
			}
		} 
		else 
		{ 
        	echo '<li><a href="#" class="'. $term->slug .'">'.  $term->name .'</a></li>';
      
	    }
    } // end foreach 
    echo '</ul>';
 }
?>