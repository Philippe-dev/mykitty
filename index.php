<?php
/**
 * This file is part of My Kitty, a module for Prestashop.
 *
 * @author    Philippe <philippe@dissitou.org>
 * @copyright 2018 Philippe Hénaff
 * @license   Licensed under the GPL version 2.0 license
 * PHP version 7
 */

header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');

header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

header('Location: ../');
exit;
