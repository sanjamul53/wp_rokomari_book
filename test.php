<?php

// require_once(ROOT_PATH.'func/book_cpt/saved_data.php');

// $saved_data = book_saved_data(19);
// var_dump($saved_data);


// die('');

global $wpdb;
$category_table = $wpdb->prefix . "rokomari_category_book";

require_once(ROOT_PATH.'func/book_cpt/generate_category_insert_values.php');

$post_id = 57;

// $wpdb->query(
//   "
//     DELETE FROM $category_table
//     WHERE book_id=$post_id;
//   "
// );
// die('');

$category_input = [10, 15, 11, 23];

$cat_values_query = 
generate_category_insert_values($category_input, $post_id);

$wpdb->query(
  "
    INSERT INTO $category_table(book_id, category_id)
    VALUES
      $cat_values_query;
  "
);
unset($cat_values_query);

