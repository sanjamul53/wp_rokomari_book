<?php

class Metabox_Data_List {

  public $post_id;

  function __construct($p_id){
    $this->post_id = $p_id;
  }


  // ==================================================================================
  // =================================== author list ==================================
  public function get_author_list() {

    $author_list = [];

    $author_query = new WP_Query(array(
      'post_type' => 'rokomari_authors',
      'post_status' => 'publish',
      'posts_per_page' => -1
    ));

    while ($author_query->have_posts()) {
      $author_query->the_post();

      $author_id = get_the_ID();

      $author_list["$author_id"] = get_the_title();

    }

    wp_reset_query();

    return $author_list;

  }

  // ==================================================================================
  // =================================== author list ==================================
  public function get_language_list() {

    $language_taxonomy = get_terms(
      'rokomari_books_language',
      array(
        'hide_empty' => 0
      )
    );

    $language_attr = [];

    foreach ($language_taxonomy as $tax_item) {

      $language_attr[] = array(
        'term_id' => $tax_item->term_id,
        'name' => $tax_item->name,
      );
    }

    return $language_attr;

  }


  // ==================================================================================
  // =================================== author list ==================================
  public function get_category_list() {

    global $wpdb;

    $term_taxonomy = $wpdb->term_taxonomy;
    $terms = $wpdb->terms;

    $query = "

      WITH cat_subcat_list AS (

        SELECT
          parent_cat.term_id AS term_id,
          IF(
            COUNT(child_cat.term_id) = 0, JSON_ARRAY(),
            JSON_ARRAYAGG(
              JSON_OBJECT(
                'term_id', child_cat.term_id,
                'name', terms.name
              )
            )
          ) AS subcat_list

        FROM $term_taxonomy AS parent_cat
        LEFT JOIN $term_taxonomy AS child_cat
        ON parent_cat.term_id = child_cat.parent
        LEFT JOIN $terms AS terms 
        ON terms.term_id = child_cat.term_id
        WHERE parent_cat.taxonomy = 'book-category' AND parent_cat.parent=0
        GROUP BY parent_cat.term_id

      )

      SELECT 
        cat_subcat_list.term_id AS term_id,
        cat_subcat_list.subcat_list AS list,
        terms.name AS name
      FROM cat_subcat_list
      INNER JOIN $terms AS terms 
      ON terms.term_id = cat_subcat_list.term_id;

    ";
      

    $query_data = $wpdb->get_results($query);

    $cat_list = [];

    foreach($query_data as $data_item) {

      $sub_cat_list = json_decode($data_item->list);

      if(count($sub_cat_list) === 0) {
        $sub_cat_list = null;
      }
      else {
        $sub_cat_list = json_decode(json_encode($sub_cat_list), true);;
      }

      $cat_list[] = array(
        'term_id' => $data_item->term_id,
        'name' => $data_item->name,
        'list' => $sub_cat_list
      );
    }
    unset($query_data);
    unset($data_item);

    return $cat_list;

  }









}