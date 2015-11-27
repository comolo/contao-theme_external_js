<?php

/**
 * Contao Open Source CMS.
 *
 * Copyright (C) 2005-2013 Leo Feyer
 *
 * @author    Hendrik Obermayer - Comolo GmbH
 * @license   LGPL
 * @copyright Hendrik Obermayer - Comolo GmbH
 */

/**
 * Table tl_layout.
 */

// Add external_js + external_scss
$GLOBALS['TL_DCA']['tl_layout']['palettes']['default'] = str_replace(
    array(',script;', 'stylesheet,external;', 'stylesheet,external,'),
    array(',script,external_js;', 'stylesheet,external,external_scss;', 'stylesheet,external,external_scss,'),
    $GLOBALS['TL_DCA']['tl_layout']['palettes']['default']
);

/* Fields */
$GLOBALS['TL_DCA']['tl_layout']['fields']['external_js'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_layout']['external_js'],
    'exclude' => true,
    'inputType' => 'fileTree',
    'eval' => array(
                                    'multiple' => true,
                                    'orderField' => 'external_js_order',
                                    'fieldType' => 'checkbox',
                                    'filesOnly' => true,
                                    'extensions' => 'js,coffee',
                                ),
    'sql' => 'blob NULL',
);

$GLOBALS['TL_DCA']['tl_layout']['fields']['external_scss'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_layout']['external_scss'],
    'exclude' => true,
    'inputType' => 'fileTree',
    'eval' => array(
                                    'multiple' => true,
                                    'orderField' => 'external_scss_order',
                                    'fieldType' => 'checkbox',
                                    'filesOnly' => true,
                                    'extensions' => 'scss',
                                ),
    'sql' => 'blob NULL',
);

/* Order */
$GLOBALS['TL_DCA']['tl_layout']['fields']['external_js_order'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_layout']['orderExt'],
    'sql' => 'blob NULL',
);

$GLOBALS['TL_DCA']['tl_layout']['fields']['external_scss_order'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_layout']['orderExt'],
    'sql' => 'blob NULL',
);