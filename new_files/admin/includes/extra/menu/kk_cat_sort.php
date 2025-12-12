<?php
/* ------------------------------------------------------------
	Module "Kundenerinnerung Modified Shop 3.0.2 mit Opt-in" made by Karl

	Based on: Kundenerinnerung_Multilingual_advanced_modified-shop-1.06
	Based on: xt-module.de customers remind
	erste Anpassung von: Fishnet Services - Gemsjäger 30.03.2012
	Zusatzfunktionen eingefügt sowie Fehler beseitigt von Ralph_84
	Aufgearbeitet für die Modified 1.06 rev4356 von Ralph_84

	modified eCommerce Shopsoftware
	http://www.modified-shop.org

	Released under the GNU General Public License
-------------------------------------------------------------- */

defined( '_VALID_XTC' ) or die( 'Direct Access to this location is not allowed.' );

if (defined('MODULE_KK_CAT_SORT_STATUS') && MODULE_KK_CAT_SORT_STATUS == 'true') {

  //Sprachabhaengiger Menueeintrag, kann fuer weiter Sprachen ergaenzt werden
  switch ($_SESSION['language_code']) {
    case 'de':
      define('MENU_NAME_KK_CAT_SORT', 'Komfortable Kategoriesortierung');
      break;
    case 'en':
      define('MENU_NAME_KK_CAT_SORT', 'Comfortable category sorting');
      break;
    default:
      define('MENU_NAME_KK_CAT_SORT', 'Komfortable Kategoriesortierung');
      break;
  }

  // Listenpunkt unter 'Kunden'
	$add_contents[BOX_HEADING_PRODUCTS][] = array(
    	'admin_access_name' => 'kk_cat_sort',   //Eintrag fuer Adminrechte
    	'filename' => 'kk_cat_sort.php',	//Dateiname der neuen Admindatei
    	'boxname' => MENU_NAME_KK_CAT_SORT,     	//Anzeigename im Menue
    	'parameters' => '',                 	//zusaetzliche Parameter z.B. 'set=export'
    	'ssl' => '',                         	//SSL oder NONSSL, kein Eintrag = NONSSL
  	);

}