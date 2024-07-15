<?php

/*
  modified fields:

    - main_author
    - other_author
    - price
    - discount
    - language
    - category

*/

function book_cpt_save_hanlder(int $post_id) {


  require_once(ROOT_PATH.'utils/is_int.php');
  require_once(ROOT_PATH.'func/book_cpt/saved_data.php');
  require_once(ROOT_PATH.'func/book_cpt/generate_category_insert_values.php');


  function print_msg(string $msg, $err = false) {

    $title = $err ? 'error': 'status';

    $post_data = json_encode(array("$title" => $msg));

    file_put_contents(ROOT_PATH . 'output.json', $post_data);
  }


  // $post_data = json_encode($_POST);

  // file_put_contents(ROOT_PATH.'output.json', $post_data);

  // return null;



  // check for post type
  if(
    $_SERVER['REQUEST_METHOD'] !== 'POST' || 
    !isset($_POST['action'])){
    return null;
  }


  // check for input data
  $price_input = $_POST['rokomari_book_price'];
  $discount_input = $_POST['rokomari_book_discount'];
  $language_input = $_POST['rokomari_book_language'];
  $main_author_input = $_POST['rokomari_book_main_author'];
  $other_author_input = $_POST['rokomari_book_other_author'];
  $category_input = $_POST['rokomari_book_category'];
  $modified_fields = $_POST['rokomari_book_modified_fields'];

  // ===============================================================================
  // ================================= Check inputs ================================

  // ============ if any input fields not modified
  if(
    !isset($modified_fields) || !is_array($modified_fields) || 
    count($modified_fields) < 1 
  ) {

    print_msg('not modified', true);

    return null;
  }

  // ============ check price input
  if(!isInt($price_input) || (int)$price_input < 0) {

    print_msg('invalid price', true);

    return null;
  }

  // ============ check discount input
  if(!isInt($discount_input) || (int)$discount_input < 0 || 
    (int)$discount_input > 100
  ) {

    print_msg('invalid discount', true);

    return null;
  }

  // ============ check main author input
  if(!isInt($main_author_input) || (int)$main_author_input < 1) {

    print_msg('invalid main author', true);

    return null;
  }

  // ============ check language input
  if(!isInt($language_input) || (int)$language_input < 1) {

    print_msg('invalid language', true);

    return null;
  }

  // ============ check other author input
  $is_other_author_input_valid = true;

  if(isset($other_author_input)) {

    if(!is_array($other_author_input)) {
      $is_other_author_input_valid = false;
    }

    foreach($other_author_input as $input_item) {

      if(!isInt($input_item) || (int)$input_item < 1) {
        
        $is_other_author_input_valid = false;

        break;

      }

    }

    unset($input_item);

  }

  if(!$is_other_author_input_valid) {

    print_msg('invalid other author', true);

    return null;
  }

  // ============ check category input
  if(!isset($category_input) || !is_array($category_input) || 
    count($category_input) === 0
  ) {

    print_msg('category is missing', true);

    return null;
  }

  $is_category_input_valid = true;

  foreach($category_input as $input_item) {

    if(!isInt($input_item) || (int)$input_item < 1) {
      
      $is_category_input_valid = false;

      break;

    }

  }

  unset($input_item);

  if(!$is_category_input_valid) {

    print_msg('invalid category', true);

    return null;
  }


  // print_msg('all data is clear');
  // return null;


  // $post_data = json_encode($_POST);

  // file_put_contents(ROOT_PATH.'output.json', $post_data);

  // return null;

  $price_input = (int)$price_input;
  $discount_input = (int)$discount_input;
  $main_author_input = (int)$main_author_input;
  $language_input = (int)$language_input;


  // ============================================================================
  // ================================= db staff =================================
  global $wpdb;
  $book_table = $wpdb->prefix . "rokomari_books";
  $author_table = $wpdb->prefix . "rokomari_author_book";
  $category_table = $wpdb->prefix . "rokomari_category_book";
  // $term_taxonomy_table = $wpdb->prefix . "term_taxonomy";

  // ========= check if data already exist =========

  $saved_data = book_saved_data($post_id);
  
  // ============= if book already exist =============
  if($saved_data) {

    // ============= update price, discount, language
    if(
      ($saved_data->price != $price_input) ||
      ($saved_data->discount != $discount_input) ||
      ($saved_data->language != $language_input)
    ) {

      print_msg('price, discount, lang updated');

      $query = $wpdb->prepare(
        "
          UPDATE $book_table
          SET
            price = '%s',
            discount = '%s',
            language = '%s'
          WHERE post_id='%s'
        ",
        array($price_input, $discount_input, $language_input, $post_id)
      );
  
      $wpdb->get_results($query);

      unset($query);

    }

    // ============= update main author
    if($saved_data->main_author != $main_author_input) {

      // replace as main author
      $query = $wpdb->prepare(
        "
          UPDATE $author_table
          SET
            author_id = $main_author_input
          WHERE book_id=$post_id AND is_main=TRUE;
        ",
        array($main_author_input, $post_id)
      );
      $wpdb->get_results($query);
      unset($query);
      
      
      // remove author
      $query = $wpdb->prepare(
        "
          DELETE FROM $author_table
          WHERE book_id=$post_id AND 
          author_id=$main_author_input AND is_main=FALSE;
        ",
        array($post_id, $main_author_input)
      );
      $wpdb->get_results($query);
      unset($query);
    }

    // ============= update category

    $is_cat_modified = false;

    if(!isset($saved_data->categories)) {
      $is_cat_modified = true;
    }
    else if(count($saved_data->categories) !== count($category_input)) {
      $is_cat_modified = true;
    }
    else {

      foreach($saved_data->categories as $saved_cat) {
    
        if(!in_array($saved_cat, $category_input)) {
          $is_cat_modified = true;
          break;
        }
      
      }

    }

    // if category modified
    if($is_cat_modified) {


      // remove categories
      $query = $wpdb->prepare(
        "
          DELETE FROM $category_table
          WHERE book_id=$post_id;
        ",
        array($post_id)
      );
      $wpdb->get_results($query);
      unset($query);


      // create categories
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

    }




  }
  // ============= create new book data =============
  else {

    // ==== insert book
    $current_date = date('Y-m-d H:i:s');
    $wpdb->insert($book_table, array(
      'post_id' => $post_id,
      'price' => $price_input,
      'discount' => $discount_input,
      'language' => $language_input,
      'release_date' => $current_date
    ));

    unset($query);

    // ==== insert author
    $query = $wpdb->prepare(
      "
        INSERT INTO $author_table(book_id, author_id, is_main)
        VALUES
          ($post_id, $main_author_input, TRUE);
      "
    );

    $wpdb->get_results($query);

    unset($query);


    // ======== insert category ========

    $cat_values_query = 
    generate_category_insert_values($category_input, $post_id);

    $query = $wpdb->prepare(
      "
        INSERT INTO $category_table(book_id, category_id)
        VALUES
          $cat_values_query;
      "
    );

    $wpdb->get_results($query);

    unset($cat_values_query);
    unset($query);

  }
  

}