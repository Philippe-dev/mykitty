{*
* This file is part of My Kitty, a module for Prestashop.
*
* @author Philippe Hénaff
* @copyright  Philippe Hénaff
* @license   Licensed under the GPL version 2.0 license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*
*}

<p class="h4 text-uppercase block-contact-title">{$mykitty_title_{$language.id}}</p>

{if $mykitty_href_{$language.id} != ""}<a href="{$mykitty_href_{$language.id}}">{/if}
     <div id="mykitty" class="block">
      <div id="logo"></div>
      <div id="somme">{$mykitty_total_{$language.id}}</div>
    </div>
{if $mykitty_href_{$language.id} != ""}</a>{/if}
