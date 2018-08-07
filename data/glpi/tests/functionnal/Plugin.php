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

namespace tests\units;

use \DbTestCase;

/* Test for inc/plugin.class.php */

class Plugin extends DbTestCase {
   public function testGetGlpiVersion() {
      $plugin = new \Plugin();
      $this->string($plugin->getGlpiVersion())->isIdenticalTo(GLPI_VERSION);
   }

   public function testGetGlpiPrever() {
      $plugin = new \Plugin();
      if (defined('GLPI_PREVER')) {
         $this->string($plugin->getGlpiPrever())->isIdenticalTo(GLPI_PREVER);
      } else {
         $this->when(
            function () use ($plugin) {
               $plugin->getGlpiPrever();
            }
         )->error
            ->exists();
      }
   }

   public function testIsGlpiPrever() {
      $plugin = new \Plugin();
      if (defined('GLPI_PREVER')) {
         $this->boolean($plugin->isGlpiPrever())->isTrue();
      } else {
         $this->boolean($plugin->isGlpiPrever())->isFalse();
      }
   }


   public function testcheckGlpiVersion() {
      //$this->constant->GLPI_VERSION = '9.1';
      $plugin = new \mock\Plugin();

      // Test min compatibility
      $infos = ['min' => '0.90'];

      $this->calling($plugin)->isGlpiPrever = false;
      $this->calling($plugin)->getGlpiVersion = '9.2';
      $this->boolean($plugin->checkGlpiVersion($infos))->isTrue();

      $this->calling($plugin)->isGlpiPrever = true;
      $this->calling($plugin)->getGlpiPrever = '9.2';
      $this->calling($plugin)->getGlpiVersion = '9.2-dev';
      $this->boolean($plugin->checkGlpiVersion($infos))->isTrue();

      $this->calling($plugin)->isGlpiPrever = false;
      $this->calling($plugin)->getGlpiVersion = '0.89';
      $this->output(
         function () use ($plugin, $infos) {
            $this->boolean($plugin->checkGlpiVersion($infos))->isFalse();
         }
      )->isIdenticalTo('This plugin requires GLPI > 0.90.');

      $this->calling($plugin)->isGlpiPrever = true;
      $this->calling($plugin)->getGlpiPrever = '0.89';
      $this->calling($plugin)->getGlpiVersion = '0.89-dev';
      $this->output(
         function () use ($plugin, $infos) {
            $this->boolean($plugin->checkGlpiVersion($infos))->isFalse();
         }
      )->isIdenticalTo('This plugin requires GLPI > 0.90.');

      // Test max compatibility
      $infos = ['max' => '9.3'];

      $this->calling($plugin)->isGlpiPrever = false;
      $this->calling($plugin)->getGlpiVersion = '9.2';
      $this->boolean($plugin->checkGlpiVersion($infos))->isTrue();

      $this->calling($plugin)->isGlpiPrever = true;
      $this->calling($plugin)->getGlpiPrever = '9.2';
      $this->calling($plugin)->getGlpiVersion = '9.2-dev';
      $this->boolean($plugin->checkGlpiVersion($infos))->isTrue();

      $this->calling($plugin)->isGlpiPrever = false;
      $this->calling($plugin)->getGlpiVersion = '9.3';
      $this->output(
         function () use ($plugin, $infos) {
            $this->boolean($plugin->checkGlpiVersion($infos))->isFalse();
         }
      )->isIdenticalTo('This plugin requires GLPI < 9.3.');

      $this->calling($plugin)->isGlpiPrever = true;
      $this->calling($plugin)->getGlpiPrever = '9.3';
      $this->calling($plugin)->getGlpiVersion = '9.3-dev';
      $this->output(
         function () use ($plugin, $infos) {
            $this->boolean($plugin->checkGlpiVersion($infos))->isFalse();
         }
      )->isIdenticalTo('This plugin requires GLPI < 9.3.');

      // Test min and max compatibility
      $infos = ['min' => '0.90', 'max' => '9.3'];

      $this->calling($plugin)->isGlpiPrever = false;
      $this->calling($plugin)->getGlpiVersion = '9.2';
      $this->boolean($plugin->checkGlpiVersion($infos))->isTrue();

      $this->calling($plugin)->isGlpiPrever = true;
      $this->calling($plugin)->getGlpiPrever = '9.2';
      $this->calling($plugin)->getGlpiVersion = '9.2-dev';
      $this->boolean($plugin->checkGlpiVersion($infos))->isTrue();

      $this->calling($plugin)->isGlpiPrever = false;
      $this->calling($plugin)->getGlpiVersion = '0.89';
      $this->output(
         function () use ($plugin, $infos) {
            $this->boolean($plugin->checkGlpiVersion($infos, true))->isFalse();
         }
      )->isIdenticalTo('This plugin requires GLPI > 0.90 and < 9.3');

      $this->calling($plugin)->isGlpiPrever = true;
      $this->calling($plugin)->getGlpiPrever = '0.89';
      $this->calling($plugin)->getGlpiVersion = '0.89-dev';
      $this->output(
         function () use ($plugin, $infos) {
            $this->boolean($plugin->checkGlpiVersion($infos))->isFalse();
         }
      )->isIdenticalTo('This plugin requires GLPI > 0.90 and < 9.3');

      $this->calling($plugin)->isGlpiPrever = false;
      $this->calling($plugin)->getGlpiVersion = '9.3';
      $this->output(
         function () use ($plugin, $infos) {
            $this->boolean($plugin->checkGlpiVersion($infos))->isFalse();
         }
      )->isIdenticalTo('This plugin requires GLPI > 0.90 and < 9.3');

      $this->calling($plugin)->isGlpiPrever = true;
      $this->calling($plugin)->getGlpiPrever = '9.3';
      $this->calling($plugin)->getGlpiVersion = '9.3-dev';
      $this->output(
         function () use ($plugin, $infos) {
            $this->boolean($plugin->checkGlpiVersion($infos))->isFalse();
         }
      )->isIdenticalTo('This plugin requires GLPI > 0.90 and < 9.3');
   }

   public function testcheckPhpVersion() {
      //$this->constant->PHP_VERSION = '7.1';
      $plugin = new \mock\Plugin();

      $infos = ['min' => '5.6'];
      $this->boolean($plugin->checkPhpVersion($infos))->isTrue();

      $this->calling($plugin)->getPhpVersion = '5.4';
      $this->output(
         function () use ($plugin, $infos) {
            $this->boolean($plugin->checkPhpVersion($infos))->isFalse();
         }
      )->isIdenticalTo('This plugin requires PHP > 5.6.');

      $this->calling($plugin)->getPhpVersion = '7.1';
      $this->boolean($plugin->checkPhpVersion($infos))->isTrue();

      $this->output(
         function () use ($plugin) {
            $infos = ['min' => '5.6', 'max' => '7.0'];
            $this->boolean($plugin->checkPhpVersion($infos))->isFalse();
         }
      )->isIdenticalTo('This plugin requires PHP > 5.6 and < 7.0');

      $infos = ['min' => '5.6', 'max' => '7.2'];
      $this->boolean($plugin->checkPhpVersion($infos))->isTrue();
   }

   public function testCheckPhpExtensions() {
      $plugin = new \Plugin();

      $this->output(
         function () use ($plugin) {
            $exts = ['gd' => ['required' => true]];
            $this->boolean($plugin->checkPhpExtensions($exts))->isTrue();
         }
      )->isEmpty();

      $this->output(
         function () use ($plugin) {
            $exts = ['myext' => ['required' => true]];
            $this->boolean($plugin->checkPhpExtensions($exts))->isFalse();
         }
      )->isIdenticalTo('This plugin requires PHP extension myext<br/>');
   }

   public function testCheckGlpiParameters() {
      global $CFG_GLPI;

      $params = ['my_param'];

      $plugin = new \Plugin();

      $this->output(
         function () use ($plugin, $params) {
            $this->boolean($plugin->checkGlpiParameters($params))->isFalse();
         }
      )->isIdenticalTo('This plugin requires GLPI parameter my_param<br/>');

      $CFG_GLPI['my_param'] = '';
      $this->output(
         function () use ($plugin, $params) {
            $this->boolean($plugin->checkGlpiParameters($params))->isFalse();
         }
      )->isIdenticalTo('This plugin requires GLPI parameter my_param<br/>');

      $CFG_GLPI['my_param'] = '0';
      $this->output(
         function () use ($plugin, $params) {
            $this->boolean($plugin->checkGlpiParameters($params))->isFalse();
         }
      )->isIdenticalTo('This plugin requires GLPI parameter my_param<br/>');

      $CFG_GLPI['my_param'] = 'abc';
      $this->output(
         function () use ($plugin, $params) {
            $this->boolean($plugin->checkGlpiParameters($params))->isTrue();
         }
      )->isEmpty();
   }

   public function testCheckGlpiPlugins() {
      $plugin = new \mock\Plugin();

      $this->calling($plugin)->isInstalled = false;
      $this->calling($plugin)->isActivated = false;

      $this->output(
         function () use ($plugin) {
            $this->boolean($plugin->checkGlpiPlugins(['myplugin']))->isFalse();
         }
      )->isIdenticalTo('This plugin requires myplugin plugin<br/>');

      $this->calling($plugin)->isInstalled = true;

      $this->output(
         function () use ($plugin) {
            $this->boolean($plugin->checkGlpiPlugins(['myplugin']))->isFalse();
         }
      )->isIdenticalTo('This plugin requires myplugin plugin<br/>');

      $this->calling($plugin)->isInstalled = true;
      $this->calling($plugin)->isActivated = true;

      $this->output(
         function () use ($plugin) {
            $this->boolean($plugin->checkGlpiPlugins(['myplugin']))->isTrue();
         }
      )->isEmpty();

   }
}
