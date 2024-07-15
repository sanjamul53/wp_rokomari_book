<?php

function generate_category_insert_values($cat_id_list, $post_id) {
  
  $value_query = '';

  for($i=0; $i<count($cat_id_list); $i++) {

    $cat_item = $cat_id_list[$i];

    $value_query = $value_query."($post_id, $cat_item)";

    if($i+1 < count($cat_id_list)) {
      $value_query = $value_query.", ";
    }
    else {
      $value_query = $value_query." ";
    }

  }

  return $value_query;

}