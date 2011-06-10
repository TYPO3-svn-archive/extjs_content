<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Daniel Alder <daniel.alder@ymail.com>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */

require_once(PATH_tslib.'class.tslib_pibase.php');


/**
 * Plugin '' for the 'extjs_content' extension.
 *
 * @author	Daniel Alder <daniel.alder@ymail.com>
 * @package	TYPO3
 * @subpackage	tx_extjscontent
 */
class tx_extjscontent_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_exjsimage_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_exjsimage_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'exjs_image';	// The extension key.
	var $pi_checkCHash = true;

	/**
	 *
	 */
	private $pageRenderer;

	/**
	 *
	 */
	private $LanguageId;

	/**
	 *
	 */
	private $cssPath;

	/**
	 *
	 */
	private $jsPath;

	/**
	 *
	 */
	private $jsExtensionPath;

	/**
	 * @var array
	 */
	private static $Records = array();

	/**
	 * @var bool
	 */
	private $loadApplication = false;

	/**
	 * @var bool
	 */
	private $loadSlider = false;

	/**
	 * @var bool
	 */
	private $loadStatic = false;

	/**
	 * @var bool
	 */
	private $loadLightbox = false;

	/**
	 * @var bool
	 */
	private $loadClickable = false;

	/**
	 *
	 */
	private function init() {
		parent::tslib_pibase();

			// Pagerenderer
		$this->pageRenderer = $GLOBALS['TSFE']->getPageRenderer();


			// Language
		$this->LanguageId = $GLOBALS['TSFE']->sys_language_uid;

			// Paths
		$this->cssPath			= t3lib_extMgm::extRelPath('extjs_content') . 'Resources/Public/Css/';
		$this->jsPath 			= t3lib_extMgm::extRelPath('extjs_content') . 'Resources/Public/Js/';
		$this->jsExtensionPath	= $this->jsPath . 'Ux/';

			// Load Records
		$this->getRecords();
	}

	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content, $conf)	{

			// init plugin settings
		$this->conf = $conf;
		$this->init();

		$this->checkRecords();

			// Load Core
		if($this->loadApplication) {
			$this->pageRenderer->addCssFile($this->cssPath . 'extjs_content.css');
			$this->pageRenderer->loadExtCore();
		}

			// Load Extensions
		if($this->loadClickable) $this->pageRenderer->addJsFile($this->jsPath . 'ExtjsContentClickable.js');
		if($this->loadStatic || $this->loadSlider) $this->pageRenderer->addJsFile($this->jsPath . 'ExtjsContentGallery.js');
		if($this->loadLightbox) {
			$this->pageRenderer->addCssFile($this->cssPath . 'lightbox.css');
			$this->pageRenderer->addJsFile($this->jsExtensionPath . 'lightbox.js');
		}

			// Load Application
		if($this->loadApplication) {

				// Start
			$Name			= 'extjsContentInlineCode';
			$InlineCode		= 'Ext.onReady(function() {';

			foreach($this->Records[$GLOBALS['TSFE']->id] as $Record) {

				if($this->loadClickable) {
					$InlineCode .= $this->getClickable($Record);
				}

				if($this->loadLightbox && $Record['tx_extjscontent_lightbox'] == 1) {
					$InlineCode .= $this->getLightbox($Record);
				}

					// Get Slider
				if($this->loadStatic || $this->loadSlider) {
					switch($Record['tx_extjscontent_mode']) {
							// Static
						case 1:
							$InlineCode .= $this->getGallery($Record);
							break;
							// Slider
						case 2:
							$InlineCode .= $this->getGallery($Record, 'Slider');
							break;
					}
				}

			}

				// End
			$InlineCode .= '});';

			$this->pageRenderer->addJsInlineCode($Name, $InlineCode);

		}



	}

	private function checkRecords() {

		foreach ($this->Records[$GLOBALS['TSFE']->id] as $Record) {

				// Load Static
			if($Record['tx_extjscontent_mode'] == 1 && ($Record['CType'] == 'textpic' || $Record['CType'] == 'image')) $this->loadStatic = true;
				// Load Slider
			if($Record['tx_extjscontent_mode'] == 2 && ($Record['CType'] == 'textpic' || $Record['CType'] == 'image')) $this->loadSlider = true;
				// Load Lightbox
			if($Record['tx_extjscontent_lightbox'] == 1 && ($Record['CType'] == 'textpic' || $Record['CType'] == 'image')) $this->loadLightbox = true;
				// Load Clickable Box
			if($Record['CType'] == 'extjs_content_pi1') $this->loadClickable = true;


		}

			// Load Application
		if($this->loadStatic || $this->loadSlider || $this->loadLightbox || $this->loadClickable) $this->loadApplication = true;

	}

	private function getRecords() {

		if(!isset($this->Records[$GLOBALS['TSFE']->id])) {

			$this->Records[$GLOBALS['TSFE']->id] = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows (
				$select = '*',
				$from = 'tt_content',
				$where = 'pid=' . $GLOBALS['TSFE']->id . ' AND sys_language_uid=' . $this->LanguageId . ' AND ( CType=\'extjs_content_pi1\' OR CType=\'textpic\' OR CType=\'image\' )'  . $this->cObj->enableFields('tt_content'),
				$groupBy = '',
				$orderBy = '',
				$limit = ''
			);

		}
	}

	/**
	 *
	 */
	private function getGallery($Record, $Effect=false) {

		// TODO: prefix & rowclass -> could be changed via TS?
		// TODO: is there an other way to push params
		$Prefix			= 'c';
		$ImageRowClass	= 'csc-textpic-imagerow';
		$ImageWrapClassName = 'false';
		$ImageRowClassName = 'false';
		$CaptionClassName = 'false';

		$ItemId			=  $Prefix . $Record['uid'];

		$Effect			= !$Effect ? 'false' : '"'. $Effect . '"';
		$Selector		= $Record['tx_extjscontent_selector'] ? 'true' : 'false';
		$Description	= $Record['tx_extjscontent_description'] ? 'true' : 'false';
		$Lightbox		= $Record['tx_extjscontent_lightbox'] && $Record['image_zoom'] ? 'true' : 'false';
		$Interval		= is_numeric($Record['tx_extjscontent_interval']) ? intval($Record['tx_extjscontent_interval']) : 'false';
		$Duration		= is_numeric($Record['tx_extjscontent_duration']) ? intval($Record['tx_extjscontent_duration']) : 'false';

		$ExtjsCode = '
			new TYPO3.ExtjsContent.Gallery.Element(
				' . $Record['uid'] . ',
				Ext.get("' . $ItemId . '"),
				' . $Effect . ',
				' . $Selector . ',
				' . $Description . ',
				' . $Interval . ',
				' . $Duration . ',
				' . $ImageWrapClassName . ',
				' . $ImageRowClassName . ',
				' . $CaptionClassName . ',
				' . $Lightbox . '
			);
		';

			// Add Css To Prevend Flickering
		$Block = '#' . $ItemId . ' .' . $ImageRowClass . ' {display: none;}';
		$this->pageRenderer->addCssInlineBlock('extjsContentInlineCss', $Block);

		return $ExtjsCode;

	}

	/**
	 *
	 */
	private function getLightbox($Record) {

		$ExtjsCode = '
			Ext.ux.Lightbox.register("a.lightbox' . $Record['uid'] . '",true);
		';

		return $ExtjsCode;
	}

	/**
	 *
	 */
	private function getClickable($Record) {

		$Url = $Record['tx_extjscontent_link'];
		$ExtjsCode = '';

		if(!empty($Url)) {

			$Link = $this->createLink($Url);

			$ExtjsCode .= '
				new TYPO3.ExtjsContent.Clickable.Element(Ext.get("c" + ' . $Record['uid'] . '), "' . $Link['Url'] . '", "' . $Link['Target'] . '");
			';
		}

		return $ExtjsCode;

	}

	/**
	 *
	 */
	private function createLink($Url) {

			$Link['Url'] = $this->cObj->typolink (
				"Link",
				array(
					"parameter" => $Url,
					"returnLast" => "url"
				)
			);

				// TODO: quick'n dirty
			if(strstr($Link['Url'], 'http')) {
				$Link['Target'] = 'external';
			}
			else {
				$Link['Target'] = 'internal';

					// We Need Absolute URLs For IE
				$Link['Url'] = $this->cObj->typolink (
					"Link",
					array(
						"parameter" => $Url,
						"returnLast" => "url",
						"forceAbsoluteUrl" => '1'
					)
				);
			}

			return $Link;

	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/extjs_content/pi1/class.tx_extjscontent_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/extjs_content/pi1/class.tx_extjscontent_pi1.php']);
}

?>