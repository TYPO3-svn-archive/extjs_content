<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

t3lib_extMgm::addPItoST43($_EXTKEY, 'pi1/class.tx_extjscontent_pi1.php', '_pi1', 'includeLib', 1);

t3lib_extMgm::addPageTSConfig('
	mod.wizards.newContentElement.wizardItems.common.elements.extjs_content {
		icon = ../../typo3conf/ext/extjs_content/Resources/Public/Images/tt_content_extjs_content.gif
		title = LLL:EXT:extjs_content/locallang_db.xml:wizard.title
		description = LLL:EXT:extjs_content/locallang_db.xml:wizard.description
		tt_content_defValues {
			CType = extjs_content_pi1
		}
	}

	mod.wizards.newContentElement.wizardItems.common.show := addToList(extjs_content)
');

?>