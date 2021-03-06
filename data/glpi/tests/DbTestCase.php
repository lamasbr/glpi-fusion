<?php
/**
 * ---------------------------------------------------------------------
 * GLPI - Gestionnaire Libre de Parc Informatique
 * Copyright (C) 2015-2018 Teclib' and contributors.
 *
 * http://glpi-project.org
 *
 * based on GLPI - Gestionnaire Libre de Parc Informatique
 * Copyright (C) 2003-2014 by the INDEPNET Development Team.
 *
 * ---------------------------------------------------------------------
 *
 * LICENSE
 *
 * This file is part of GLPI.
 *
 * GLPI is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GLPI is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GLPI. If not, see <http://www.gnu.org/licenses/>.
 * ---------------------------------------------------------------------
 */

// Generic test classe, to be extended for CommonDBTM Object

class DbTestCase extends \GLPITestCase {

   public function setUp() {
      global $DB;

      // Need Innodb -- $DB->begin_transaction() -- workaround:
      $DB->objcreated = [];
      parent::setUp();
   }

   public function afterTestMethod($method) {
      global $DB;

      parent::afterTestMethod($method);

      // Need Innodb -- $DB->rollback()  -- workaround:
      foreach ($DB->objcreated as $table => $ids) {
         foreach ($ids as $id) {
            $DB->delete($table, ['id' => $id]);
         }
      }
      unset($DB->objcreated);
   }


   /**
    * Connect using the test user
    */
   protected function login($user_name = TU_USER, $user_pass = TU_PASS) {

      $auth = new Auth();
      if (!$auth->login($user_name, $user_pass, true)) {
         $this->markTestSkipped('No login');
      }
   }

   /**
    * change current entity
    */
   protected function setEntity($entityname, $subtree) {
      $res = Session::changeActiveEntities(getItemByTypeName('Entity', $entityname, true), $subtree);
      $this->boolean($res)->isTrue();
   }

   /**
    * Generic method to test if an added object is corretly inserted
    *
    * @param  Object $object The object to test
    * @param  int    $id     The id of added object
    * @param  array  $input  the input used for add object (optionnal)
    *
    * @return nothing (do tests)
    */
   protected function checkInput(CommonDBTM $object, $id = 0, $input = []) {
      $this->integer((int)$id)->isGreaterThan(0);
      $this->boolean($object->getFromDB($id))->isTrue();
      $this->variable($object->getField('id'))->isEqualTo($id);

      if (count($input)) {
         foreach ($input as $k => $v) {
            $this->variable($object->getField($k))->isEqualTo($v);
         }
      }
   }
}
