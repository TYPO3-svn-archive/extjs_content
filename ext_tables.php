<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

t3lib_extMgm::addStaticFile($_EXTKEY,'Configuration/TypoScript/', 'ExtjsContent');

if (!function_exists('tx_extjscontent_addPalette')) {
	function tx_extjscontent_addPalette($ctype, $pallette, $search = 'textlayout') {
		$showitem = $GLOBALS['TCA']['tt_content']['types'][$ctype]['showitem'];
		$searchString = ';'.$search.',';
		$palette = '--palette--;LLL:EXT:extjs_content/locallang_db.xml:tt_content.'.$pallette.';'.$pallette.',';
		$pos = strpos($showitem, $searchString) + strlen($searchString);
		$showitem = substr($showitem, 0, $pos) . $palette . substr($showitem, $pos);

		$GLOBALS['TCA']['tt_content']['types'][$ctype]['showitem'] = $showitem;
	}
}

t3lib_div::loadTCA('tt_content');

// Extends Image / Text With Image

$tempColumns = array (
	'tx_extjscontent_lightbox' => array (
		'exclude' => 1,
		'label' => 'LLL:EXT:extjs_content/locallang_db.xml:tt_content.tx_extjscontent_lightbox',
		'config' => array (
			'type' => 'check',
		)
	),
	'tx_extjscontent_mode' => array (
		'exclude' => 1,
		'label' => 'LLL:EXT:extjs_content/locallang_db.xml:tt_content.tx_extjscontent_mode',
		'config' => array (
			'type' => 'radio',
			'items' => array (
				array('LLL:EXT:extjs_content/locallang_db.xml:tt_content.tx_extjscontent_mode.I.0', '0'),
				array('LLL:EXT:extjs_content/locallang_db.xml:tt_content.tx_extjscontent_mode.I.1', '1'),
				array('LLL:EXT:extjs_content/locallang_db.xml:tt_content.tx_extjscontent_mode.I.2', '2'),
			),
			'default' => '0',
		)
	),
	'tx_extjscontent_selector' => array (
		'exclude' => 1,
		'label' => 'LLL:EXT:extjs_content/locallang_db.xml:tt_content.tx_extjscontent_selector',
		'config' => array (
			'type' => 'radio',
			'items' => array (
				array('LLL:EXT:extjs_content/locallang_db.xml:tt_content.tx_extjscontent_selector.I.0', '0'),
				array('LLL:EXT:extjs_content/locallang_db.xml:tt_content.tx_extjscontent_selector.I.1', '1'),
			),
			'default' => '0',
		)
	),
	'tx_extjscontent_description' => array (
		'exclude' => 1,
		'label' => 'LLL:EXT:extjs_content/locallang_db.xml:tt_content.tx_extjscontent_description',
		'config' => array (
			'type' => 'check',
		)
	),
	'tx_extjscontent_interval' => array (
        'exclude' => 1,
        'label' => 'LLL:EXT:extjs_content/locallang_db.xml:tt_content.tx_extjscontent_interval',
        'config' => array (
            'type' => 'input',
            'size' => '30',
        )
    ),
	'tx_extjscontent_duration' => array (
        'exclude' => 1,
        'label' => 'LLL:EXT:extjs_content/locallang_db.xml:tt_content.tx_extjscontent_duration',
        'config' => array (
            'type' => 'input',
            'size' => '30',
        )
    ),
);

t3lib_extMgm::addTCAcolumns('tt_content',$tempColumns);

$pallette = 'tx_extjscontent_palette1';

$GLOBALS['TCA']['tt_content']['palettes'][$pallette] = array(
	'canNotCollapse' => 1,
	'showitem' => 'tx_extjscontent_mode;LLL:EXT:extjs_content/locallang_db.xml:tt_content.tx_extjscontent_mode, tx_extjscontent_selector;LLL:EXT:extjs_content/locallang_db.xml:tt_content.tx_extjscontent_selector, tx_extjscontent_lightbox;LLL:EXT:extjs_content/locallang_db.xml:tt_content.tx_extjscontent_lightbox, tx_extjscontent_description;LLL:EXT:extjs_content/locallang_db.xml:tt_content.tx_extjscontent_description'
);

tx_extjscontent_addPalette('image', $pallette);
tx_extjscontent_addPalette('textpic', $pallette);


$pallette = 'tx_extjscontent_palette2';

$GLOBALS['TCA']['tt_content']['palettes'][$pallette] = array(
	'canNotCollapse' => 1,
	'showitem' => 'tx_extjscontent_interval;LLL:EXT:extjs_content/locallang_db.xml:tt_content.tx_extjscontent_interval, tx_extjscontent_duration;LLL:EXT:extjs_content/locallang_db.xml:tt_content.tx_extjscontent_duration'
);

tx_extjscontent_addPalette('image', $pallette, 'tx_extjscontent_palette1');
tx_extjscontent_addPalette('textpic', $pallette, 'tx_extjscontent_palette1');

// Add New CType -> Copy From TextWithImage
t3lib_extMgm::addPlugin(array(
    'LLL:EXT:extjs_content/locallang_db.xml:tt_content.CType',
    $_EXTKEY . '_pi1',
    t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'CType');

$GLOBALS['TCA']['tt_content']['types']['extjs_content_pi1']['showitem'] = $GLOBALS['TCA']['tt_content']['types']['textpic']['showitem'];

// Remove Unused Fields

//$GLOBALS['TCA']['tt_content']['types']['extjs_content_pi1']['showitem'] =

// Add New Field 'Link'

$tempColumns = array (
    'tx_extjscontent_link' => array (
        'exclude' => 0,
        'label' => 'LLL:EXT:extjs_content/locallang_db.xml:tt_content.tx_extjscontent_link',
		'config' => array (
			'type'		=>	'input',
			'size'		=>	'15',
			'max'		=>	'255',
			'checkbox'	=>	'',
			'eval'		=>	'trim',
			'wizards'	=>	array(
				'_PADDING'	=> 2,
				'link'		=> array(
					'type'			=>	'popup',
					'title'			=>	'Link',
					'icon'			=>	'link_popup.gif',
					'script'		=>	'browse_links.php?mode=wizard',
					'JSopenParams'	=>	'height=300,width=500,status=0,menubar=0,scrollbars=1'
				)
			)
		)
    ),
);


t3lib_extMgm::addTCAcolumns('tt_content',$tempColumns,1);

t3lib_extMgm::addToAllTCAtypes('tt_content','tx_extjscontent_link','extjs_content_pi1','after:sys_language_uid');

?>