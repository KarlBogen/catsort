<?php
/* ------------------------------------------------------------
  Module "Kategoriesortierung" made by Karl

  modified eCommerce Shopsoftware
  http://www.modified-shop.org

  Released under the GNU General Public License
-------------------------------------------------------------- */


defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

class kk_cat_sort
{

  public $version;
  public $code;
  public $title;
  public $description;
  public $sort_order;
  public $enabled;
  public $properties;
  public $_check;
  public $keys;

  public function __construct()
  {
    $this->version = '1.0.0';
    $this->code = 'kk_cat_sort';
    $this->title = MODULE_KK_CAT_SORT_TEXT_TITLE . ' © by <a href="https://github.com/KarlBogen" target="_blank" style="color: #e67e22; font-weight: bold;">Karl</a> - Version: ' . $this->version;
    $this->description = '';
    $this->description .= '<a class="button btnbox but_red" style="text-align:center;" onclick="return confirmLink(\'' . MODULE_KK_CAT_SORT_DELETE_CONFIRM . '\', \'\' ,this);" href="' . xtc_href_link(FILENAME_MODULE_EXPORT, 'set=system&module=' . $this->code . '&action=custom') . '">' . MODULE_KK_CAT_SORT_DELETE_BUTTON . '</a><br />';
    $this->description .= MODULE_KK_CAT_SORT_TEXT_DESCRIPTION;
    $this->sort_order = defined('MODULE_KK_CAT_SORT_SORT_ORDER') ? MODULE_KK_CAT_SORT_SORT_ORDER : 0;
    $this->enabled = ((defined('MODULE_KK_CAT_SORT_STATUS') && MODULE_KK_CAT_SORT_STATUS == 'true') ? true : false);
    $this->properties = array('process_key' => false);
  }

  public function process($file) {}

  public function display()
  {
    return array('text' => '<br /><div align="center">' . xtc_button(BUTTON_SAVE) .
      xtc_button_link(BUTTON_CANCEL, xtc_href_link(FILENAME_MODULE_EXPORT, 'set=' . $_GET['set'] . '&module=kk_cat_sort')) . "</div>");
  }

  public function check()
  {
    if (!isset($this->_check)) {
      if (defined('MODULE_KK_CAT_SORT_STATUS')) {
        $this->_check = true;
      } else {
        $check_query = xtc_db_query("SELECT configuration_value
                                       FROM " . TABLE_CONFIGURATION . "
                                      WHERE configuration_key = 'MODULE_KK_CAT_SORT_STATUS'");
        $this->_check = xtc_db_num_rows($check_query);
      }
    }
    return $this->_check;
  }

  public function install()
  {
    xtc_db_query("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) VALUES ('MODULE_KK_CAT_SORT_STATUS', 'true',  '6', '1', 'xtc_cfg_select_option(array(\'true\', \'false\'), ', now())");

    // Einträge in admin_access
    $admin_access_kk_cat_sort_exists = xtc_db_num_rows(xtc_db_query("SHOW COLUMNS FROM " . TABLE_ADMIN_ACCESS . " WHERE Field='kk_cat_sort'"));
    if (!$admin_access_kk_cat_sort_exists) {
      xtc_db_query("ALTER TABLE " . TABLE_ADMIN_ACCESS . " ADD `kk_cat_sort` INT(1) NOT NULL DEFAULT 0");
    }
    xtc_db_query("UPDATE " . TABLE_ADMIN_ACCESS . " SET kk_cat_sort = '9' WHERE customers_id = 'groups' LIMIT 1");
    xtc_db_query("UPDATE " . TABLE_ADMIN_ACCESS . " SET kk_cat_sort = '1' WHERE customers_id = '1' LIMIT 1");
    if ($_SESSION['customer_id'] > 1) {
      xtc_db_query("UPDATE " . TABLE_ADMIN_ACCESS . " SET kk_cat_sort = '1' WHERE customers_id = '" . $_SESSION['customer_id'] . "' LIMIT 1");
    }
  }

  public function update() {}

  public function remove()
  {
    xtc_db_query("DELETE FROM " . TABLE_CONFIGURATION . " WHERE configuration_key in ('" . implode("', '", $this->keys()) . "')");
    $query = xtc_db_query("SHOW COLUMNS FROM " . TABLE_ADMIN_ACCESS . " LIKE 'kk_cat_sort'");
    $exist = xtc_db_num_rows($query);
    if ($exist > 0) {
      xtc_db_query("ALTER TABLE " . TABLE_ADMIN_ACCESS . " DROP `kk_cat_sort`");
    }
  }

  public function custom()
  {
    global $messageStack;

    // Systemmodule deinstallieren
    $this->remove();

    // Dateien definieren
    $shop_path = DIR_FS_CATALOG;
    $dirs_and_files = array();
    // admin
    $dirs_and_files[] = $shop_path . DIR_ADMIN . 'includes/css/kk_bootstrap.min.css';
    $dirs_and_files[] = $shop_path . DIR_ADMIN . 'includes/extra/filenames/kk_cat_sort.php';
    $dirs_and_files[] = $shop_path . DIR_ADMIN . 'includes/extra/menu/kk_cat_sort.php';
    $dirs_and_files[] = $shop_path . DIR_ADMIN . 'includes/javascript/kk_bootstrap.bundle.min.js';
    $dirs_and_files[] = $shop_path . DIR_ADMIN . 'includes/javascript/kk_sortable.js';
    $dirs_and_files[] = $shop_path . DIR_ADMIN . 'kk_cat_sort.php';
    // lang
    $dirs_and_files[] = $shop_path . 'lang/english/admin/kk_cat_sort.php';
    $dirs_and_files[] = $shop_path . 'lang/english/modules/system/kk_cat_sort.php';

    $dirs_and_files[] = $shop_path . 'lang/german/admin/kk_cat_sort.php';
    $dirs_and_files[] = $shop_path . 'lang/german/modules/system/kk_cat_sort.php';

    // Dateien löschen
    foreach ($dirs_and_files as $dir_or_file) {
      if (!$this->rrmdir($dir_or_file)) {
        $messageStack->add_session($dir_or_file . MODULE_KK_CAT_SORT_DELETE_ERR, 'error');
      }
    }
    // Datei selbst löschen
    unlink($shop_path . DIR_ADMIN . 'includes/modules/system/kk_cat_sort.php');
    $messageStack->add_session($this->title, 'success');
    xtc_redirect(xtc_href_link(FILENAME_MODULE_EXPORT, 'set=system'));
  }

  public function keys()
  {

    $key = array(
      'MODULE_KK_CAT_SORT_STATUS',
    );

    return $key;
  }

  protected function rrmdir($dir)
  {
    if (is_dir($dir)) {
      $objects = scandir($dir);
      foreach ($objects as $object) {
        if ($object != "." && $object != "..") {
          if (filetype($dir . "/" . $object) == "dir") $this->rrmdir($dir . "/" . $object);
          else unlink($dir . "/" . $object);
        }
      }
      reset($objects);
      rmdir($dir);
      return true;
    } elseif (is_file($dir)) {
      unlink($dir);
      return true;
    }
  }
}
