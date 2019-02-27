<?php

/**
 * @file
 * Documentation for MegaMenu module APIs.
 */

/**
 * @addtogroup hooks
 * @{
 */

/**
 * Define the list of megamenu skins.
 */
function hook_megamenu_skins_info() {
  $skins = [];

  $skins['some_skin'] = [
    'name' => 'some_skin',
    'title' => t('Some Skin'),
    'library' => 'some_module/some_library',
    'classes' => 'skin-some-class',
  ];

  return $skins;
}

/**
 * Modify the list of available megamenu skins.
 *
 * This hook may be used to modify megamenu skins after they have been
 * specified by other modules.
 *
 * @param $skins
 *   An array of all the existing skin definitions, passed by reference.
 */
function hook_megamenu_skins_info_alter(array &$skins) {
  $skins['some_skin']['title'] = t('Some Skin Title');
}

/**
 * @} End of "addtogroup hooks".
 */
