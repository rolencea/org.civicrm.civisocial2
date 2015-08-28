<?php
/*
+--------------------------------------------------------------------+
| CiviCRM version 4.6                                                |
+--------------------------------------------------------------------+
| Copyright CiviCRM LLC (c) 2004-2015                                |
+--------------------------------------------------------------------+
| This file is a part of CiviCRM.                                    |
|                                                                    |
| CiviCRM is free software; you can copy, modify, and distribute it  |
| under the terms of the GNU Affero General Public License           |
| Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
|                                                                    |
| CiviCRM is distributed in the hope that it will be useful, but     |
| WITHOUT ANY WARRANTY; without even the implied warranty of         |
| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
| See the GNU Affero General Public License for more details.        |
|                                                                    |
| You should have received a copy of the GNU Affero General Public   |
| License and the CiviCRM Licensing Exception along                  |
| with this program; if not, contact CiviCRM LLC                     |
| at info[AT]civicrm[DOT]org. If you have questions about the        |
| GNU Affero General Public License or the licensing of CiviCRM,     |
| see the CiviCRM license FAQ at http://civicrm.org/licensing        |
+--------------------------------------------------------------------+
*/
/**
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2015
 * author: Lorence  Jr
 *
 */


class CRM_Civisocial_BAO_CivisocialFacebooksettings extends CRM_Civisocial_DAO_CivisocialFacebooksettings {

/*
 *
 *
 *
 *fuction to add or update CivsocialFacebooksettings
 *
 *
 *
 */

    public static function get_data($params) {
        $result = array();
        $social = new CRM_Civisocial_BAO_CivisocialFacebooksettings();
        if (!empty($params)) {
            $fields = self::fields();
            foreach ($params as $key => $value) {
                if (isset($fields[$key])) {
                    $social->$key = $value;
                }
            }
        }
        $social->find();
        while ($social->fetch()) {
            $row = array();
            self::storeValues($social, $row);
            $result[$row['App_ID']] = $row;
        }
        return $result;
    }


    /*
     *function to add or update Civisocial Facebook settings
     *
     *
     */



    public static function add($params) {
        $result = array();
        if (empty($params)) {
            throw new Exception('Params can not be empty when adding or updating this record');
        }
        $social = new CRM_Civisocial_BAO_CivisocialFacebooksettings();
        $fields = self::fields();
        foreach ($params as $key => $value) {
            if (isset($fields[$key])) {
                $social->$key = $value;
            }
        }
        $social->save();
        self::storeValues($social, $result);
        return $result;
    }


    /*
     * function to delete a record from the Civisocial Facebook Table
     *
     */



    public static function delete_by_id($App_id) {
        if (empty($App_id)) {
            throw new Exception('App_id can not be empty when attempting to delete a record');
        }
        $social = new CRM_Civisocial_BAO_CivisocialFacebooksettings();
        $social->App_ID = $App_id;
        $social->delete();
        return;
    }
}