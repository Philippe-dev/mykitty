<?php
/**
* This file is part of My Kitty, a module for Prestashop.
*
* @author Philippe Hénaff
* @copyright  Philippe Hénaff
* @license   Licensed under the GPL version 2.0
*/

header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');

header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

header('Location: ../');
exit;
