<?php

function book_saved_data($post_id){

  require_once(ROOT_PATH."utils/is_int.php");

  if(!isInt($post_id)) return null;

  global $wpdb;
  $book_table = $wpdb->prefix . "rokomari_books";
  $author_table = $wpdb->prefix . "rokomari_author_book";
  $category_table = $wpdb->prefix . "rokomari_category_book";


  $query = "
  WITH category_list AS (

    SELECT
      book_id,
        JSON_ARRAYAGG(category.category_id) AS categories 
    FROM $category_table AS category
    WHERE category.book_id = $post_id
    GROUP BY category.book_id
  ),
  author_list AS (

    SELECT
      main_list.book_id AS book_id,
      main_list.main_author AS main_author,
      other_list.other_autors AS other_autors
    FROM (
      SELECT
        book_id,
        author_id AS main_author
      FROM $author_table
      WHERE book_id = $post_id AND is_main = TRUE
    ) AS main_list
    LEFT JOIN (
      SELECT
        book_id,
        json_arrayagg(author_id) AS other_autors
      FROM $author_table
      WHERE book_id = $post_id AND is_main = FALSE
      GROUP BY book_id
    ) AS other_list ON main_list.book_id = other_list.book_id

  )

  SELECT

    books.post_id AS book_id,
    books.price AS price,
    books.discount AS discount,
    books.language AS language,
    books.release_date AS release_date,
    author_list.main_author AS main_author,
    author_list.other_autors AS other_autors,
    category_list.categories AS categories
      
  FROM $book_table AS books
  LEFT JOIN author_list ON author_list.book_id = books.post_id
  LEFT JOIN category_list ON category_list.book_id = books.post_id
  WHERE books.post_id = $post_id;
  ";


  $query_data = $wpdb->get_results($query);

  if(!isset($query_data) || count($query_data) !== 1) {
    return null;
  }

  $book_data = $query_data[0];


  if($book_data->other_autors) {
    $book_data->other_autors = json_decode($book_data->other_autors);
  }

  if($book_data->categories) {
    $book_data->categories = json_decode($book_data->categories);
  }

  return $book_data;

}