<?php

require_once(ROOT_PATH."func/book_cpt/metabox_data_list.php");
require_once(ROOT_PATH."func/book_cpt/saved_data.php");

$post_id = $post->ID;

$data_list = new Metabox_Data_List($post_id);

$saved_data = book_saved_data($post_id);
// $saved_data = null;

// author list 
$author_list = $data_list->get_author_list();

// language attribute 
$language_attr = $data_list->get_language_list();

// category list
$category_list = $data_list->get_category_list();



// =========== get current data ===========
$db_saved_data = array(
  'price' => null,
  'discount' => null,
  'language' => null,
  'main_author' => null,
  'other_author' => [],
  'categories' => []
);

if(isset($saved_data)) {

  $db_saved_data = array(
    'price' => $saved_data->price,
    'discount' => $saved_data->discount,
    'language' => $saved_data->language,
    'main_author' => $saved_data->main_author,
    'other_author' => isset($saved_data->other_autors) ?
                      $saved_data->other_autors: [],
    'categories' => isset($saved_data->categories) ?
                    $saved_data->categories: []
  );

}





?>

<table class="form-table rokomari_book_metabox">


  <!-- ================================= Price ================================= -->

  <tr>

    <th>
      <label for="rokomari_book_price"> Price </label>
    </th>

    <td>
      <input 
        type="number" 
        name="rokomari_book_price" 
        id="rokomari_book_price" 
        class="regular-text" 
        value="<?php echo $db_saved_data['price']?>"
        required
      >
    </td>

  </tr>

  <!-- ================================= Discount ================================= -->

  <tr>

    <th>
      <label for="rokomari_book_discount"> Discount </label>
    </th>

    <td>
      <input 
        type="number" 
        name="rokomari_book_discount" 
        id="rokomari_book_discount" 
        class="regular-text" 
        value="<?php echo $db_saved_data['discount']?>"
        min="0"
        max="100"
        required
        >
    </td>

  </tr>

  <!-- ================================= Author ================================= -->

  <tr>

    <th>
      <label for="rokomari_book_main_author"> Author </label>
    </th>

    <td>

      <select 
        name="rokomari_book_main_author" 
        id="rokomari_book_main_author"
        required
      >

      </select>

    </td>

  </tr>


  <!-- =============================== extra author =============================== -->

  <tr>

    <th>
      <label for="rokomari_book_other_author"> Other Author </label>
    </th>

    <td>

      <select multiple
        name="rokomari_book_other_author[]" 
        id="rokomari_book_other_author" 
      >


      </select>

    </td>

  </tr>

  <!-- ================================= Language ================================= -->

  <tr>

    <th>
      <label for="rokomari_book_language"> Language </label>
    </th>

    <td>

      <select 
        name="rokomari_book_language" 
        id="rokomari_book_language" 
      >

        <option value> Select a Language </option>

        <?php foreach($language_attr as $lang_item):?>

          <option 
            value="<?php echo $lang_item['term_id'] ?>"
            <?php selected( $db_saved_data['language'], $lang_item['term_id'] ); ?>
          > 
            <?php echo $lang_item['name'] ?> 
          </option>

        <?php endforeach;?>

      </select>

    </td>

  </tr>


    <!-- ============================= category list ============================= -->

    <tr>

    <th>
      <label> Category </label>
    </th>

      <td>


        <?php foreach($category_list as $cat_item):
          $parent_checked = 
          in_array($cat_item['term_id'], $db_saved_data['categories']);
        ?>

          <ul style="margin-bottom: 1rem;" >
            <li>

              <label>
                <input type="checkbox" class="rokomari_book_category" 
                  name="rokomari_book_category[]" 
                  value=<?php echo $cat_item['term_id']; ?>
                  <?php if($parent_checked){echo "checked"; } ?>
                > 
                <?php echo $cat_item['name']; ?>
              </label>

              <?php if(isset($cat_item['list'])):?>

                <ul style="margin: 0.5rem 0 0 1rem;" >
                
                  <?php foreach($cat_item['list'] as $subcat): 
                  
                    $child_checked = 
                    in_array($subcat['term_id'], $db_saved_data['categories']);
                  ?>

                    <li>
                      <label>
                        <input type="checkbox" class="rokomari_book_category" 
                          name="rokomari_book_category[]" 
                          value=<?php echo $subcat['term_id']; ?> 
                        <?php if($child_checked){echo "checked"; } ?>
                        > 
                        <?php echo $subcat['name']; ?>
                      </label>
                    </li>
                  
                  <?php endforeach; 
                    unset($child_checked);
                  ?>

                </ul>
              
              <?php endif; ?>
              


            </li>
          </ul>

        <?php endforeach;
          unset($parent_checked);
        ?>

      </td>

    </tr>


  <!-- ============================= modified fields ============================= -->

  <tr style="display: none;">


    <td>

      <select multiple
        name="rokomari_book_modified_fields[]" 
        id="rokomari_book_modified_fields"
        style="display: none;"
      >

      </select>

    </td>

  </tr>



</table>

<script>
  const author_list = <?php echo json_encode($author_list, JSON_HEX_TAG); ?>;
  const db_saved_data = <?php echo json_encode($db_saved_data, JSON_HEX_TAG); ?>;
</script>