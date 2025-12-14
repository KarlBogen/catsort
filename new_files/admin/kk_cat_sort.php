<?php
/* --------------------------------------------------------------
   $Id: pdfbill_del.php Modified 2.0 r.9281 2016-02-23 11:30:45Z Ralph_84 $

   modified eCommerce Shopsoftware
   http://www.modified-shop.org

   Copyright (c) 2009 - 2016 [www.modified-shop.org]
   --------------------------------------------------------------
   Released under the GNU General Public License
   --------------------------------------------------------------*/

require('includes/application_top.php');

function update_categorie_sort($set_sort_array = '')
{
  foreach ($set_sort_array as $k => $v) {
    xtc_db_query("UPDATE " . TABLE_CATEGORIES . "
                     SET sort_order = '" . ((int)$k + 1) * 10 . "'
                   WHERE categories_id = '" . (int)$v . "'");
  }
  return true;
}

function get_categories($categories_array = '', $parent_id = '0', $include_sub = true, $indent = 1, $space = 1)
{

  if (!is_array($categories_array)) $categories_array = array();

  $join = '';
  $conditions = '';
  if (!defined('RUN_MODE_ADMIN')) {
    $join = " AND trim(cd.categories_name) != '' ";
    $conditions .= " AND c.categories_status = 1 ";
    $conditions .= CATEGORIES_CONDITIONS_C;
  }

  $categories_query = xtDBquery("SELECT c.categories_id,
                                          c.sort_order,
                                          cd.categories_name
                                     FROM " . TABLE_CATEGORIES . " c
                                     JOIN " . TABLE_CATEGORIES_DESCRIPTION . " cd
                                          ON c.categories_id = cd.categories_id
                                             AND cd.language_id = '" . (int)$_SESSION['languages_id'] . "'
                                             " . $join . "
                                    WHERE c.parent_id = " . (int)$parent_id . "
                                          " . $conditions . "
                                 ORDER BY c.sort_order, cd.categories_name");

  $count = xtc_db_num_rows($categories_query, true);
  if ($count > 0) {
    $isnew = 1;
    while ($categories = xtc_db_fetch_array($categories_query, true)) {
      if ($isnew) {
        $key = count($categories_array);
        if ($key > 1) $categories_array[$key - 1]['count'] = $count;
        $isnew = 0;
      }
      $categories_array[] = array(
        'level' => $indent,
        'sort' => $categories['sort_order'],
        'id' => $categories['categories_id'],
        'text' => $categories['categories_name']
      );
      if ($include_sub === true && $categories['categories_id'] != $parent_id) {
        $categories_array = get_categories($categories_array, $categories['categories_id'], $include_sub, $indent + $space, $space);
      }
    }
  }

  return $categories_array;
}


if (!empty($_POST['data'])) {
  $set_sort_array = json_decode($_POST['data'], true);
  $saved = update_categorie_sort($set_sort_array);
  if ($saved !== true) {
    $message = array('danger', MODULE_KK_CAT_SORT_SAVE_ERR);
  } else {
    $message = array('success', MODULE_KK_CAT_SORT_SAVE_OK);
  }
}

$categories = get_categories();

require(DIR_WS_INCLUDES . 'head.php');
?>
<link type="text/css" href="includes/css/kk_bootstrap.min.css" rel="stylesheet">
<style>
  *,
  ::before,
  ::after {
    -webkit-box-sizing: content-box;
    -moz-box-sizing: content-box;
    box-sizing: content-box;
  }

  .container {
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
  }

  #catList {
    --custom-bg-opacity: 0.1;
  }

  .level-1,
  .level-3,
  .level-5 {
    background-color: rgba(var(--bs-secondary-rgb), var(--custom-bg-opacity, 0.1));
  }

  #catList .cat-link:not(.collapsed) .icon::before {
    content: "-";
  }

  #catList .cat-link .icon::before {
    content: "+";
  }

  #catList .cat-link .icon {
    display: inline-block;
    font-size: 1.25rem;
    line-height: 1;
    text-rendering: auto;
  }

  #catList .cat-link {
    font-weight: bold;
    padding: 3px 10px;
  }

  .space {
    display: inline-block;
    width: 100px;
  }

  #catList .data {
    cursor: grab;
    cursor: -moz-grab;
    cursor: -webkit-grab;
  }

  #catList .data.sortable-chosen {
    cursor: grabbing;
    cursor: -moz-grabbing;
    cursor: -webkit-grabbing;
  }
</style>
</head>

<body>
  <!-- header //-->
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
  <!-- header_eof //-->
  <!-- body //-->
  <div class="container">
    <div class="row">
      <?php //left_navigation
      if (USE_ADMIN_TOP_MENU == 'false') {
        echo '<div class="col-2" style="min-width: 200px;">' . PHP_EOL;
        require_once(DIR_WS_INCLUDES . 'column_left.php');
        echo '</div>' . PHP_EOL;
      }
      ?>
      <div class="col">
        <div class="row mb-3">
          <div class="col-4">
            <div class="pageHeadingImage"><?php echo xtc_image(DIR_WS_ICONS . 'heading/icon_content.png'); ?></div>
            <div class="pageHeading"><?php echo HEADING_TITLE; ?><br /></div>
            <div class="main pdg2 flt-l">Tools</div>
          </div>
          <div class="col">
            <div class="p-2 bg-info bg-opacity-10 border border-info border-start-0  border-end-0 mb-2"><?php echo MODULE_KK_CAT_SORT_INFO_TEXT; ?></div>
          </div>
        </div>
        <?php if (isset($message)) {
          echo '<div class="alert alert-' . $message[0] . '" role="alert">' . $message[1] . '</div>';
        }
        ?>
        <div id="message"></div>
        <?php echo xtc_draw_form('save_cat_oder', FILENAME_KK_CAT_SORT, '', 'post', 'id="myform"'); ?>
        <div class="d-flex justify-content-end pe-3 pb-3">
          <input class="btn btn-success" type="submit" value="<?php echo BUTTON_SAVE; ?>">
        </div>
        <?php
        $ausgabe = '<div id="catList" class="list-group col nested-sortable">' . PHP_EOL;
        $level_old = 0;
        $start = 1;
        foreach ($categories as $categorie) {
          $level_diff = 0;
          if ($level_old != $categorie['level'] && $start != 1) {
            $level_diff = $categorie['level'] - $level_old;
          }
          if ($level_diff > 0) {
            for ($i = 0; $i < $level_diff; $i++) {
              $ausgabe .= PHP_EOL . '<div id="cat_id-' . $old_id . '" class="collapse list-group nested-sortable mt-2">' . PHP_EOL;
            }
          }
          if ($level_old == $categorie['level']) {
            $ausgabe .= '</div>' . PHP_EOL;
          }
          if ($level_diff < 0) {
            for ($i = $level_diff; $i <= ($level_old - $categorie['level']); $i++) {
              $ausgabe .= '</div>' . PHP_EOL;
            }
            $level_old = $level_old + $level_diff;
          }
          if ($level_old <= $categorie['level']) {
            $start = 0;
            $old_id = '';
            $collapse = '';
            if (isset($categorie['count'])) {
              $collapse .= '<span class="cat-link bg-secondary-subtle align-middle collapsed" data-bs-toggle="collapse" data-bs-target="#cat_id-' . $categorie['id'] . '" role="button" aria-expanded="false" aria-controls="cat_id-' . $categorie['id'] . '">' . PHP_EOL;
              $collapse .= '<i class="icon pe-2"></i>(' . $categorie['count'] . ')' . PHP_EOL;
              $collapse .= '</span>' . PHP_EOL;
              $old_id = $categorie['id'];
            }
            $ausgabe .= '<div class="data list-group-item level-' . $categorie['level'] . ($categorie['sort'] == 0 ? ' sort-nr' : '') . '" data-id="' . $categorie['id'] . '"><span class="space">' . $collapse . '</span>' . $categorie['text'];
            $level_old = $categorie['level'];
            continue;
          }
        }
        for ($i = 0; $i < ($categorie['level'] * 2 - 1); $i++) {
          $ausgabe .= '</div>' . PHP_EOL;
          $a = $i;
        }
        $ausgabe .= '</div>' . PHP_EOL;
        echo $ausgabe;
        ?>
        <div class="d-flex justify-content-end pe-3 pt-3">
          <input class="btn btn-success" type="submit" value="<?php echo BUTTON_SAVE; ?>">
        </div>
        <input id="data" type="hidden" name="data" value="">
        </form>
        <br>
      </div>
    </div>
  </div>
  <!-- body_eof //-->
  <!-- footer //-->
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
  <!-- footer_eof //-->
  <br>
  <script src="includes/javascript/kk_bootstrap.bundle.min.js"></script>
  <script src="includes/javascript/kk_sortable.js"></script>
  <script>
    function initCats() {
      let nestedSortables = document.getElementsByClassName('nested-sortable');
      let sortables = [];
      for (var i = 0; i < nestedSortables.length; i++) {
        sortables[i] = new Sortable(nestedSortables[i], {
          animation: 150,
          fallbackOnBody: true,
          swapThreshold: 0.65,
          ghostClass: 'active'
        });
      }
      let elems = document.querySelectorAll(".sort-nr");
      if (elems.length) {
        document.getElementById("message").innerHTML = '<div class="alert alert-warning" role="alert"><?php echo MODULE_KK_CAT_SORT_INFO_SAVE; ?></div>';
      }
    }
    initCats();

    function logSubmit(event) {
      var ids = [];
      var children = document.querySelectorAll("#catList .data"); //get container element children.
      for (var i = 0, len = children.length; i < len; i++) {
        ids.push(children[i].getAttribute('data-id')); //get child id.
      }
      document.getElementById("data").value = JSON.stringify(ids);
      event.preventDefault();
      form.submit();
    }

    let form = document.getElementById("myform");
    form.addEventListener("submit", logSubmit);
  </script>
</body>

</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>