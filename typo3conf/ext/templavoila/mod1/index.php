<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2003-2006 Robert Lemke (robert@typo3.org)
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
 * Module 'Page' for the 'templavoila' extension.
 *
 * $Id: index.php 14867 2008-12-12 19:33:14Z dmitry $
 *
 * @author     Robert Lemke <robert@typo3.org>
 * @coauthor   Kasper Skaarhoj <kasperYYYY@typo3.com>
 * @coauthor   Dmitry Dulepov <dmitry@typo3.org>
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *  122: class tx_templavoila_module1 extends t3lib_SCbase
 *
 *              SECTION: Initialization functions
 *  162:     function init()
 *  213:     function menuConfig()
 *
 *              SECTION: Main functions
 *  271:     function main()
 *  451:     function printContent()
 *
 *              SECTION: Rendering functions
 *  471:     function render_editPageScreen()
 *
 *              SECTION: Framework rendering functions
 *  538:     function render_framework_allSheets($contentTreeArr, $languageKey='DEF', $parentPointer=array(), $parentDsMeta=array())
 *  576:     function render_framework_singleSheet($contentTreeArr, $languageKey, $sheet, $parentPointer=array(), $parentDsMeta=array())
 *  699:     function render_framework_subElements($elementContentTreeArr, $languageKey, $sheet)
 *
 *              SECTION: Rendering functions for certain subparts
 *  818:     function render_previewData($previewData, $elData, $ds_meta, $languageKey, $sheet)
 *  885:     function render_previewContent($row)
 *  971:     function render_localizationInfoTable($contentTreeArr, $parentPointer, $parentDsMeta=array())
 *
 *              SECTION: Outline rendering:
 * 1111:     function render_outline($contentTreeArr)
 * 1217:     function render_outline_element($contentTreeArr, &$entries, $indentLevel=0, $parentPointer=array(), $controls='')
 * 1319:     function render_outline_subElements($contentTreeArr, $sheet, &$entries, $indentLevel)
 * 1404:     function render_outline_localizations($contentTreeArr, &$entries, $indentLevel)
 *
 *              SECTION: Link functions (protected)
 * 1466:     function link_edit($label, $table, $uid, $forced=FALSE)
 * 1487:     function link_new($label, $parentPointer)
 * 1505:     function link_unlink($label, $unlinkPointer, $realDelete=FALSE)
 * 1525:     function link_makeLocal($label, $makeLocalPointer)
 * 1537:     function link_getParameters()
 *
 *              SECTION: Processing and structure functions (protected)
 * 1565:     function handleIncomingCommands()
 *
 *              SECTION: Miscelleaneous helper functions (protected)
 * 1689:     function getAvailableLanguages($id=0, $onlyIsoCoded=true, $setDefault=true, $setMulti=false)
 * 1763:     function hooks_prepareObjectsArray ($hookName)
 * 1780:     function alternativeLanguagesDefined()
 * 1790:     function displayElement($subElementArr)
 *
 * TOTAL FUNCTIONS: 25
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

	// Initialize module
unset($MCONF);
require ('conf.php');
require ($BACK_PATH.'init.php');
require ($BACK_PATH.'template.php');
$LANG->includeLLFile('EXT:templavoila/mod1/locallang.xml');
require_once (PATH_t3lib.'class.t3lib_scbase.php');
$BE_USER->modAccess($MCONF,1);    								// This checks permissions and exits if the users has no permission for entry.

t3lib_extMgm::isLoaded('cms',1);

	// We need the TCE forms functions
require_once (PATH_t3lib.'class.t3lib_loaddbgroup.php');
require_once (PATH_t3lib.'class.t3lib_tcemain.php');
require_once (PATH_t3lib.'class.t3lib_clipboard.php');

	// Include TemplaVoila API
require_once (t3lib_extMgm::extPath('templavoila').'class.tx_templavoila_api.php');

	// Include class for rendering the side bar and wizards:
require_once (t3lib_extMgm::extPath('templavoila').'mod1/class.tx_templavoila_mod1_sidebar.php');
require_once (t3lib_extMgm::extPath('templavoila').'mod1/class.tx_templavoila_mod1_wizards.php');
require_once (t3lib_extMgm::extPath('templavoila').'mod1/class.tx_templavoila_mod1_clipboard.php');
require_once (t3lib_extMgm::extPath('templavoila').'mod1/class.tx_templavoila_mod1_localization.php');
require_once (t3lib_extMgm::extPath('templavoila').'mod1/class.tx_templavoila_mod1_records.php');
require_once (t3lib_extMgm::extPath('templavoila').'mod1/class.tx_templavoila_mod1_specialdoktypes.php');

/**
 * Module 'Page' for the 'templavoila' extension.
 *
 * @author		Robert Lemke <robert@typo3.org>
 * @coauthor	Kasper Skaarhoj <kasperYYYY@typo3.com>
 * @package		TYPO3
 * @subpackage	tx_templavoila
 */
class tx_templavoila_module1 extends t3lib_SCbase {

	var $modTSconfig;								// This module's TSconfig
	var $modSharedTSconfig;							// TSconfig from mod.SHARED
	var $extKey = 'templavoila';					// Extension key of this module

	var $global_tt_content_elementRegister=array(); // Contains a list of all content elements which are used on the page currently being displayed (with version, sheet and language currently set). Mainly used for showing "unused elements" in sidebar.
	var $global_localization_status=array(); 		// Contains structure telling the localization status of each element

	var $altRoot = array();							// Keys: "table", "uid" - thats all to define another "rootTable" than "pages" (using default field "tx_templavoila_flex" for flex form content)
	var $versionId = 0;								// Versioning: The current version id

	var $currentLanguageKey;						// Contains the currently selected language key (Example: DEF or DE)
	var $currentLanguageUid;						// Contains the currently selected language uid (Example: -1, 0, 1, 2, ...)
	var $allAvailableLanguages = array();			// Contains records of all available languages (not hidden, with ISOcode), including the default language and multiple languages. Used for displaying the flags for content elements, set in init().
	var $translatedLanguagesArr = array();			// Select language for which there is a page translation
	var $translatedLanguagesArr_isoCodes = array();	// ISO codes (for l/v pairs) of translated languages.
	var $translatorMode = FALSE;					// If this is set, the whole page module scales down functionality so that a translator only needs  to look for and click the "Flags" in the interface to localize the page! This flag is set if a user does not have access to the default language; then translator mode is assumed.
	var $calcPerms;									// Permissions for the parrent record (normally page). Used for hiding icons.

	var $doc;										// Instance of template doc class
	var $sideBarObj;								// Instance of sidebar class
	var $wizardsObj;								// Instance of wizards class
	var $clipboardObj;								// Instance of clipboard class
	var $recordsObj;								// Instance of records class
	/**
	 * @var tx_templavoila_api
	 */
	var $apiObj;									// Instance of tx_templavoila_api
	var $sortableContainers = array();				// Contains the containers for drag and drop


	/*******************************************
	 *
	 * Initialization functions
	 *
	 *******************************************/

	/**
	 * Initialisation of this backend module
	 *
	 * @return	void
	 * @access public
	 */
	function init()    {
		parent::init();

		$this->MOD_SETTINGS = t3lib_BEfunc::getModuleData($this->MOD_MENU, t3lib_div::_GP('SET'), $this->MCONF['name']);

		$this->altRoot = t3lib_div::_GP('altRoot');
		$this->versionId = t3lib_div::_GP('versionId');

			// Fill array allAvailableLanguages and currently selected language (from language selector or from outside)
		$this->allAvailableLanguages = $this->getAvailableLanguages(0, true, true, true);
		$this->currentLanguageKey = $this->allAvailableLanguages[$this->MOD_SETTINGS['language']]['ISOcode'];
		$this->currentLanguageUid = $this->allAvailableLanguages[$this->MOD_SETTINGS['language']]['uid'];

			// If no translations exist for this page, set the current language to default (as there won't be a language selector)
		$this->translatedLanguagesArr = $this->getAvailableLanguages($this->id);
		if (count($this->translatedLanguagesArr) == 1) {	// Only default language exists
			$this->currentLanguageKey = 'DEF';
		}

			// Set translator mode if the default langauge is not accessible for the user:
		if (!$GLOBALS['BE_USER']->checkLanguageAccess(0) && !$GLOBALS['BE_USER']->isAdmin())	{
			$this->translatorMode = TRUE;
		}

			// Initialize side bar and wizards:
		$this->sideBarObj =& t3lib_div::getUserObj ('&tx_templavoila_mod1_sidebar','');
		$this->sideBarObj->init($this);
		$this->sideBarObj->position = isset($this->modTSconfig['properties']['sideBarPosition']) ? $this->modTSconfig['properties']['sideBarPosition'] : 'toptabs';

		$this->wizardsObj = t3lib_div::getUserObj('&tx_templavoila_mod1_wizards','');
		$this->wizardsObj->init($this);

			// Initialize TemplaVoila API class:
		$apiClassName = t3lib_div::makeInstanceClassName('tx_templavoila_api');
		$this->apiObj = new $apiClassName ($this->altRoot ? $this->altRoot : 'pages');

			// Initialize the clipboard
		$this->clipboardObj =& t3lib_div::getUserObj ('&tx_templavoila_mod1_clipboard','');
		$this->clipboardObj->init($this);

			// Initialize the record module
		$this->recordsObj =& t3lib_div::getUserObj ('&tx_templavoila_mod1_records','');
		$this->recordsObj->init($this);
	}

	/**
	 * Preparing menu content and initializing clipboard and module TSconfig
	 *
	 * @return	void
	 * @access public
	 */
	function menuConfig()	{
		global $TYPO3_CONF_VARS;

			// Prepare array of sys_language uids for available translations:
		$this->translatedLanguagesArr = $this->getAvailableLanguages($this->id);
		$translatedLanguagesUids = array();
		foreach ($this->translatedLanguagesArr as $languageRecord) {
			$translatedLanguagesUids[$languageRecord['uid']] = $languageRecord['title'];
		}

		$this->MOD_MENU = array(
			'tt_content_showHidden' => 1,
			'showOutline' => 1,
			'language' => $translatedLanguagesUids,
			'clip_parentPos' => '',
			'clip' => '',
			'langDisplayMode' => '',
			'recordsView_table' => '',
			'recordsView_start' => ''
		);

			// Hook: menuConfig_preProcessModMenu
		$menuHooks = $this->hooks_prepareObjectsArray('menuConfigClass');
		foreach ($menuHooks as $hookObj) {
			if (method_exists ($hookObj, 'menuConfig_preProcessModMenu')) {
				$hookObj->menuConfig_preProcessModMenu ($this->MOD_MENU, $this);
			}
		}

			// page/be_user TSconfig settings and blinding of menu-items
		$this->modTSconfig = t3lib_BEfunc::getModTSconfig($this->id,'mod.'.$this->MCONF['name']);
		$this->MOD_MENU['view'] = t3lib_BEfunc::unsetMenuItems($this->modTSconfig['properties'],$this->MOD_MENU['view'],'menu.function');

		if (!isset($this->modTSconfig['properties']['sideBarEnable'])) $this->modTSconfig['properties']['sideBarEnable'] = 1;
		$this->modSharedTSconfig = t3lib_BEfunc::getModTSconfig($this->id, 'mod.SHARED');

			// CLEANSE SETTINGS
		$this->MOD_SETTINGS = t3lib_BEfunc::getModuleData($this->MOD_MENU, t3lib_div::_GP('SET'), $this->MCONF['name']);
	}





	/*******************************************
	 *
	 * Main functions
	 *
	 *******************************************/

	/**
	 * Main function of the module.
	 *
	 * @return	void
	 * @access public
	 */
	function main()    {
		global $BE_USER,$LANG,$BACK_PATH;

		if (!is_callable(array('t3lib_div', 'int_from_ver')) || t3lib_div::int_from_ver(TYPO3_version) < 4000000) {
			$this->content = 'Fatal error:This version of TemplaVoila does not work with TYPO3 versions lower than 4.0.0! Please upgrade your TYPO3 core installation.';
			return;
		}

			// Access check! The page will show only if there is a valid page and if this page may be viewed by the user
		if (is_array($this->altRoot))	{
			$access = true;
		} else {
			$pageInfoArr = t3lib_BEfunc::readPageAccess($this->id, $this->perms_clause);
			$access = (intval($pageInfoArr['uid'] > 0));
		}

		if ($access)    {

			// calls from drag and drop
			if (t3lib_div::_GP('ajaxPasteRecord') == 'cut') {
				$sourcePointer = $this->apiObj->flexform_getPointerFromString(t3lib_div::_GP('source'));
				$destinationPointer = $this->apiObj->flexform_getPointerFromString(t3lib_div::_GP('destination'));
				$this->apiObj->moveElement($sourcePointer, $destinationPointer);
				exit;
			}

			if (t3lib_div::_GP('ajaxUnlinkRecord')) {
				$unlinkDestinationPointer = $this->apiObj->flexform_getPointerFromString(t3lib_div::_GP('ajaxUnlinkRecord'));
				$this->apiObj->unlinkElement($unlinkDestinationPointer);
				exit;
			}

			$this->calcPerms = $GLOBALS['BE_USER']->calcPerms($pageInfoArr);

				// Define the root element record:
			$this->rootElementTable = is_array($this->altRoot) ? $this->altRoot['table'] : 'pages';
			$this->rootElementUid = is_array($this->altRoot) ? $this->altRoot['uid'] : $this->id;
			$this->rootElementRecord = t3lib_BEfunc::getRecordWSOL($this->rootElementTable, $this->rootElementUid, '*');
			$this->rootElementUid_pidForContent = $this->rootElementRecord['t3ver_swapmode']==0 && $this->rootElementRecord['_ORIG_uid'] ? $this->rootElementRecord['_ORIG_uid'] : $this->rootElementRecord['uid'];

				// Check if we have to update the pagetree:
			if (t3lib_div::_GP('updatePageTree')) {
				t3lib_BEfunc::getSetUpdateSignal('updatePageTree');
			}

				// Draw the header.
			$this->doc = t3lib_div::makeInstance('noDoc');
			$this->doc->docType= 'xhtml_trans';
			$this->doc->backPath = $BACK_PATH;
			$this->doc->divClass = '';
			$this->doc->form='<form action="'.htmlspecialchars('index.php?'.$this->link_getParameters()).'" method="post" autocomplete="off">';

				// Adding classic jumpToUrl function, needed for the function menu. Also, the id in the parent frameset is configured.
			$this->doc->JScode = $this->doc->wrapScriptTags('
				function jumpToUrl(URL)	{ //
					document.location = URL;
					return false;
				}
				if (top.fsMod) top.fsMod.recentIds["web"] = '.intval($this->id).';
			' . $this->doc->redirectUrls() . '
							function jumpToUrl(URL)	{	//
								window.location.href = URL;
								return false;
							}
							function jumpExt(URL,anchor)	{	//
								var anc = anchor?anchor:"";
								window.location.href = URL+(T3_THIS_LOCATION?"&returnUrl="+T3_THIS_LOCATION:"")+anc;
								return false;
							}
							function jumpSelf(URL)	{	//
								window.location.href = URL+(T3_RETURN_URL?"&returnUrl="+T3_RETURN_URL:"");
								return false;
							}

							function setHighlight(id)	{	//
								top.fsMod.recentIds["web"]=id;
								top.fsMod.navFrameHighlightedID["web"]="pages"+id+"_"+top.fsMod.currentBank;	// For highlighting

								if (top.content && top.content.nav_frame && top.content.nav_frame.refresh_nav)	{
									top.content.nav_frame.refresh_nav();
								}
							}

							function editRecords(table,idList,addParams,CBflag)	{	//
								window.location.href="'.$BACK_PATH.'alt_doc.php?returnUrl='.rawurlencode(t3lib_div::getIndpEnv('REQUEST_URI')).
									'&edit["+table+"]["+idList+"]=edit"+addParams;
							}
							function editList(table,idList)	{	//
								var list="";

									// Checking how many is checked, how many is not
								var pointer=0;
								var pos = idList.indexOf(",");
								while (pos!=-1)	{
									if (cbValue(table+"|"+idList.substr(pointer,pos-pointer))) {
										list+=idList.substr(pointer,pos-pointer)+",";
									}
									pointer=pos+1;
									pos = idList.indexOf(",",pointer);
								}
								if (cbValue(table+"|"+idList.substr(pointer))) {
									list+=idList.substr(pointer)+",";
								}

								return list ? list : idList;
							}

							if (top.fsMod) top.fsMod.recentIds["web"] = '.intval($this->id).';
						'

			);

				//Prototype /Scriptaculous
			$this->doc->JScode .= '<script src="' . $this->doc->backPath . 'contrib/prototype/prototype.js" type="text/javascript"></script>';
			$this->doc->JScode .= '<script src="' . $this->doc->backPath . 'contrib/scriptaculous/scriptaculous.js?load=effects,dragdrop" type="text/javascript"></script>';
			$this->doc->JScode .= '<script src="' . t3lib_div::locationHeaderUrl('dragdrop-min.js') . '" type="text/javascript"></script>';

				// Set up JS for dynamic tab menu and side bar
			$this->doc->JScode .= $this->doc->getDynTabMenuJScode();
			$this->doc->JScode .= $this->modTSconfig['properties']['sideBarEnable'] ? $this->sideBarObj->getJScode() : '';

				// Setting up support for context menus (when clicking the items icon)
			$CMparts = $this->doc->getContextMenuCode();
			$this->doc->bodyTagAdditions = $CMparts[1];
			$this->doc->JScode.= $CMparts[0];
			$this->doc->postCode.= $CMparts[2];

			// CSS for drag and drop
			$this->doc->inDocStyles .= '
				table {position:relative;}
				.sortable_handle {cursor:move;}
			';

			if (t3lib_extMgm::isLoaded('t3skin')) {
				// Fix padding for t3skin in disabled tabs
				$this->doc->inDocStyles .= '
					table.typo3-dyntabmenu td.disabled, table.typo3-dyntabmenu td.disabled_over, table.typo3-dyntabmenu td.disabled:hover { padding-left: 10px; }
				';
			}

			$this->handleIncomingCommands();

				// Start creating HTML output
			$this->content .= $this->doc->startPage($LANG->getLL('title'));
			$render_editPageScreen = true;
				// Show message if the page is of a special doktype:
			if ($this->rootElementTable == 'pages') {

					// Initialize the special doktype class:
				$specialDoktypesObj =& t3lib_div::getUserObj ('&tx_templavoila_mod1_specialdoktypes','');
				$specialDoktypesObj->init($this);

				$methodName = 'renderDoktype_'.$this->rootElementRecord['doktype'];
				if (method_exists($specialDoktypesObj, $methodName)) {
					$result = $specialDoktypesObj->$methodName($this->rootElementRecord);
					if ($result !== FALSE) {
						$this->content .= $result;
						if ($GLOBALS['BE_USER']->isPSet($this->calcPerms, 'pages', 'edit')) {
							// Edit icon only if page can be modified by user
							$this->content .= '<br/><br/><strong>'.$this->link_edit('<img'.t3lib_iconWorks::skinImg($this->doc->backPath,'gfx/edit2.gif','').' title="'.htmlspecialchars($LANG->sL('LLL:EXT:lang/locallang_mod_web_list.xml:editPage')).'" alt="" style="border: none; vertical-align: middle" /> '.$LANG->sL('LLL:EXT:lang/locallang_mod_web_list.xml:editPage'),'pages',$this->id).'</strong>';
						}
						$render_editPageScreen = false; // Do not output editing code for special doctypes!
					}
				}
			}

			if ($render_editPageScreen) {
					// Render "edit current page" (important to do before calling ->sideBarObj->render() - otherwise the translation tab is not rendered!
				$editCurrentPageHTML = $this->render_editPageScreen();

					// Hook for adding new sidebars or removing existing
				$sideBarHooks = $this->hooks_prepareObjectsArray('sideBarClass');
				foreach ($sideBarHooks as $hookObj)	{
					if (method_exists($hookObj, 'main_alterSideBar')) {
						$hookObj->main_alterSideBar($this->sideBarObj, $this);
					}
				}

					// Show the "edit current page" screen along with the sidebar
				$shortCut = ($BE_USER->mayMakeShortcut() ? '<br /><br />'.$this->doc->makeShortcutIcon('id,altRoot',implode(',',array_keys($this->MOD_MENU)),$this->MCONF['name']) : '');
				if ($this->sideBarObj->position == 'left' && $this->modTSconfig['properties']['sideBarEnable']) {
					$this->content .= '
						<table cellspacing="0" cellpadding="0" style="width:100%; height:550px; padding:0; margin:0;">
							<tr>
								<td style="vertical-align:top;">'.$this->sideBarObj->render().'</td>
								<td style="vertical-align:top; padding-bottom:20px;" width="99%">'.$editCurrentPageHTML.$shortCut;'</td>
							</tr>
						</table>
					';
				} else {
					$sideBarTop = $this->modTSconfig['properties']['sideBarEnable']  && ($this->sideBarObj->position == 'toprows' || $this->sideBarObj->position == 'toptabs') ? $this->sideBarObj->render() : '';
					$this->content .= $sideBarTop.$editCurrentPageHTML.$shortCut;
				}

				// Create sortables
				if (is_array($this->sortableContainers)) {
					$this->content .= '<script type="text/javascript">' . chr(10) .
						'//<![CDATA[' . chr(10) .
						'var sortable_linkParameters = \'' . $this->link_getParameters() .
						'\';';
					$containment = '["' . implode('","', $this->sortableContainers) . '"]';
					$this->content .= 'Event.observe(window,"load",function(){';
					foreach ($this->sortableContainers as $s) {
						$this->content .= 'tv_createSortable(\'' . $s . '\',' . $containment . ');';
					}
					$this->content .= '});' . chr(10) . '//]]>' . chr(10) . '</script>';
				}
			}

		} else {	// No access or no current page uid:

			$this->doc = t3lib_div::makeInstance('mediumDoc');
			$this->doc->docType= 'xhtml_trans';
			$this->doc->backPath = $BACK_PATH;
			$this->content.=$this->doc->startPage($LANG->getLL('title'));

			$cmd = t3lib_div::_GP ('cmd');
			switch ($cmd) {

					// Create a new page
				case 'crPage' :
						// Output the page creation form
					$this->content .= $this->wizardsObj->renderWizard_createNewPage (t3lib_div::_GP ('positionPid'));
					break;

					// If no access or if ID == zero
				default:
					$this->content.=$this->doc->header($LANG->getLL('title'));
					$this->content.=$LANG->getLL('default_introduction');
			}
		}
		$this->content.=$this->doc->endPage();
	}

	/**
	 * Echoes the HTML output of this module
	 *
	 * @return	void
	 * @access public
	 */
	function printContent()    {
		echo $this->content;
	}





	/********************************************
	 *
	 * Rendering functions
	 *
	 ********************************************/

	/**
	 * Displays the default view of a page, showing the nested structure of elements.
	 *
	 * @return	string		The modules content
	 * @access protected
	 */
	function render_editPageScreen()    {
		global $LANG, $BE_USER, $TYPO3_CONF_VARS;

		$output = '';

			// Fetch the content structure of page:
		$contentTreeData = $this->apiObj->getContentTree($this->rootElementTable, $this->rootElementRecord); // TODO Dima: seems like it does not return <TCEForms> for elements inside sectiions. Thus titles are not visible for these elements!

			// Set internal variable which registers all used content elements:
		$this->global_tt_content_elementRegister = $contentTreeData['contentElementUsage'];

			// Setting localization mode for root element:
		$this->rootElementLangMode = $contentTreeData['tree']['ds_meta']['langDisable'] ? 'disable' : ($contentTreeData['tree']['ds_meta']['langChildren'] ? 'inheritance' : 'separate');
		$this->rootElementLangParadigm = ($this->modTSconfig['properties']['translationParadigm'] == 'free') ? 'free' : 'bound';

			// Create a back button if neccessary:
		if (is_array ($this->altRoot)) {
			$output .= '<div style="text-align:right; width:100%; margin-bottom:5px;"><a href="index.php?id='.$this->id.'"><img'.t3lib_iconWorks::skinImg($this->doc->backPath,'gfx/goback.gif','').' title="'.htmlspecialchars($LANG->getLL ('goback')).'" alt="" /></a></div>';
		}

			// Add the localization module if localization is enabled:
		if ($this->alternativeLanguagesDefined()) {
			$this->localizationObj =& t3lib_div::getUserObj ('&tx_templavoila_mod1_localization','');
			$this->localizationObj->init($this);
		}

			// Hook for content at the very top (fx. a toolbar):
		if (is_array ($TYPO3_CONF_VARS['EXTCONF']['templavoila']['mod1']['renderTopToolbar'])) {
			foreach ($TYPO3_CONF_VARS['EXTCONF']['templavoila']['mod1']['renderTopToolbar'] as $_funcRef) {
				$_params = array ();
				$output .= t3lib_div::callUserFunction($_funcRef, $_params, $this);
			}
		}

			// Display the content as outline or the nested page structure:
		if ($BE_USER->isAdmin() && $this->MOD_SETTINGS['showOutline'])	{
			$output.= $this->render_outline($contentTreeData['tree']);
		} else {
			$output.= $this->render_framework_allSheets($contentTreeData['tree'], $this->currentLanguageKey);
		}

			// See http://bugs.typo3.org/view.php?id=4821
		$renderHooks = $this->hooks_prepareObjectsArray('render_editPageScreen');
		foreach ($renderHooks as $hookObj)	{
			if (method_exists ($hookObj, 'render_editPageScreen_addContent')) {
				$output .= $hookObj->render_editPageScreen_addContent($this);
			}
		}

		$output .= t3lib_BEfunc::cshItem('_MOD_web_txtemplavoilaM1', '', $this->doc->backPath,'<hr/>|'.$LANG->getLL('csh_whatisthetemplavoilapagemodule', 1));

		return $output;
	}





	/*******************************************
	 *
	 * Framework rendering functions
	 *
	 *******************************************/

	/**
	 * Rendering the sheet tabs if applicable for the content Tree Array
	 *
	 * @param	array		$contentTreeArr: DataStructure info array (the whole tree)
	 * @param	string		$languageKey: Language key for the display
	 * @param	array		$parentPointer: Flexform Pointer to parent element
	 * @param	array		$parentDsMeta: Meta array from parent DS (passing information about parent containers localization mode)
	 * @return	string		HTML
	 * @access protected
	 * @see	render_framework_singleSheet()
	 */
	function render_framework_allSheets($contentTreeArr, $languageKey='DEF', $parentPointer=array(), $parentDsMeta=array()) {

			// If more than one sheet is available, render a dynamic sheet tab menu, otherwise just render the single sheet framework
		if (is_array($contentTreeArr['sub']) && (count($contentTreeArr['sub'])>1 || !isset($contentTreeArr['sub']['sDEF'])))	{
			$parts = array();
			foreach(array_keys($contentTreeArr['sub']) as $sheetKey)	{

				$this->containedElementsPointer++;
				$this->containedElements[$this->containedElementsPointer] = 0;
				$frContent = $this->render_framework_singleSheet($contentTreeArr, $languageKey, $sheetKey, $parentPointer, $parentDsMeta);

				$parts[] = array(
					'label' => ($contentTreeArr['meta'][$sheetKey]['title'] ? $contentTreeArr['meta'][$sheetKey]['title'] : $sheetKey),	#.' ['.$this->containedElements[$this->containedElementsPointer].']',
					'description' => $contentTreeArr['meta'][$sheetKey]['description'],
					'linkTitle' => $contentTreeArr['meta'][$sheetKey]['short'],
					'content' => $frContent,
				);

				$this->containedElementsPointer--;
			}
			return $this->doc->getDynTabMenu($parts,'TEMPLAVOILA:pagemodule:'.$this->apiObj->flexform_getStringFromPointer($parentPointer));
		} else {
			return $this->render_framework_singleSheet($contentTreeArr, $languageKey, 'sDEF', $parentPointer, $parentDsMeta);
		}
	}

	/**
	 * Renders the display framework of a single sheet. Calls itself recursively
	 *
	 * @param	array		$contentTreeArr: DataStructure info array (the whole tree)
	 * @param	string		$languageKey: Language key for the display
	 * @param	string		$sheet: The sheet key of the sheet which should be rendered
	 * @param	array		$parentPointer: Flexform pointer to parent element
	 * @param	array		$parentDsMeta: Meta array from parent DS (passing information about parent containers localization mode)
	 * @return	string		HTML
	 * @access protected
	 * @see	render_framework_singleSheet()
	 */
	function render_framework_singleSheet($contentTreeArr, $languageKey, $sheet, $parentPointer=array(), $parentDsMeta=array()) {
		global $LANG, $TYPO3_CONF_VARS;

		$elementBelongsToCurrentPage = $contentTreeArr['el']['table'] == 'pages' || $contentTreeArr['el']['pid'] == $this->rootElementUid_pidForContent;

		$canEditPage = $GLOBALS['BE_USER']->isPSet($this->calcPerms, 'pages', 'edit');
		$canEditContent = $GLOBALS['BE_USER']->isPSet($this->calcPerms, 'pages', 'editcontent');
		$canCreateNew = $GLOBALS['BE_USER']->isPSet($this->calcPerms, 'pages', 'new');

		// Prepare the record icon including a content sensitive menu link wrapped around it:
		$recordIcon = '<img'.t3lib_iconWorks::skinImg($this->doc->backPath,$contentTreeArr['el']['icon'],'').' style="text-align: center; vertical-align: middle;" width="18" height="16" border="0" title="'.htmlspecialchars('['.$contentTreeArr['el']['table'].':'.$contentTreeArr['el']['uid'].']').'" alt="" />';
		$menuCommands = array();
		if ($GLOBALS['BE_USER']->isPSet($this->calcPerms, 'pages', 'new')) {
			$menuCommands[] = 'new';
		}
		if ($canEditContent) {
			$menuCommands[] = 'copy,cut,pasteinto,pasteafter,delete';
		}
		else {
			$menuCommands[] = 'copy';
		}

		$titleBarLeftButtons = $this->translatorMode ? $recordIcon : (count($menuCommands) == 0 ? $recordIcon : $this->doc->wrapClickMenuOnIcon($recordIcon,$contentTreeArr['el']['table'], $contentTreeArr['el']['uid'], 1,'&amp;callingScriptId='.rawurlencode($this->doc->scriptID), implode(',', $menuCommands)));
		$titleBarLeftButtons.= $this->getRecordStatHookValue($contentTreeArr['el']['table'],$contentTreeArr['el']['uid']);
		unset($menuCommands);

			// Prepare table specific settings:
		switch ($contentTreeArr['el']['table']) {

			case 'pages' :

				$titleBarLeftButtons .= $this->translatorMode || !$canEditPage ? '' : $this->link_edit('<img'.t3lib_iconWorks::skinImg($this->doc->backPath,'gfx/edit2.gif','').' title="'.htmlspecialchars($LANG->sL('LLL:EXT:lang/locallang_mod_web_list.xml:editPage')).'" alt="" style="text-align: center; vertical-align: middle; border:0;" />',$contentTreeArr['el']['table'],$contentTreeArr['el']['uid']);
				$titleBarRightButtons = '';

				$addGetVars = ($this->currentLanguageUid?'&L='.$this->currentLanguageUid:'');
				$viewPageOnClick = 'onclick= "'.htmlspecialchars(t3lib_BEfunc::viewOnClick($contentTreeArr['el']['uid'], $this->doc->backPath, t3lib_BEfunc::BEgetRootLine($contentTreeArr['el']['uid']),'','',$addGetVars)).'"';
				$viewPageIcon = '<img'.t3lib_iconWorks::skinImg($this->doc->backPath,'gfx/zoom.gif','width="12" height="12"').' title="'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.showPage',1).'" hspace="3" alt="" style="text-align: center; vertical-align: middle;" />';
				$titleBarLeftButtons .= '<a href="#" '.$viewPageOnClick.'>'.$viewPageIcon.'</a>';
			break;

			case 'tt_content' :

				$elementTitlebarColor = ($elementBelongsToCurrentPage ? $this->doc->bgColor5 : $this->doc->bgColor6);
				$elementTitlebarStyle = 'background-color: '.$elementTitlebarColor;

				$languageUid = $contentTreeArr['el']['sys_language_uid'];

				if (!$this->translatorMode && $canEditContent) {
						// Create CE specific buttons:
					$linkMakeLocal = !$elementBelongsToCurrentPage ? $this->link_makeLocal('<img'.t3lib_iconWorks::skinImg($this->doc->backPath,t3lib_extMgm::extRelPath('templavoila').'mod1/makelocalcopy.gif','').' title="'.$LANG->getLL('makeLocal').'" border="0" alt="" />', $parentPointer) : '';
					$linkUnlink = $this->link_unlink('<img'.t3lib_iconWorks::skinImg($this->doc->backPath,'gfx/garbage.gif','').' title="'.$LANG->getLL('unlinkRecord').'" border="0" alt="" />', $parentPointer, FALSE);
					if ($GLOBALS['BE_USER']->recordEditAccessInternals('tt_content', $contentTreeArr['previewData']['fullRow'])) {
						$linkEdit = ($elementBelongsToCurrentPage ? $this->link_edit('<img'.t3lib_iconWorks::skinImg($this->doc->backPath,'gfx/edit2.gif','').' title="'.$LANG->getLL('editrecord').'" border="0" alt="" />',$contentTreeArr['el']['table'],$contentTreeArr['el']['uid']) : '');
					}
					else {
						$linkEdit = '';
					}
					$titleBarRightButtons = $linkEdit . $this->clipboardObj->element_getSelectButtons($parentPointer) . $linkMakeLocal . $linkUnlink;
				}
				else {
					$titleBarRightButtons = $this->clipboardObj->element_getSelectButtons($parentPointer, 'copy');
				}
			break;
		}

			// Prepare the language icon:
		$languageLabel = htmlspecialchars ($this->allAvailableLanguages[$contentTreeArr['el']['sys_language_uid']]['title']);
		$languageIcon = $this->allAvailableLanguages[$languageUid]['flagIcon'] ? '<img src="'.$this->allAvailableLanguages[$languageUid]['flagIcon'].'" title="'.$languageLabel.'" alt="'.$languageLabel.'" style="text-align: center; vertical-align: middle;" />' : ($languageLabel && $languageUid ? '['.$languageLabel.']' : '');

			// If there was a language icon and the language was not default or [all] and if that langauge is accessible for the user, then wrap the  flag with an edit link (to support the "Click the flag!" principle for translators)
		if ($languageIcon && $languageUid>0 && $GLOBALS['BE_USER']->checkLanguageAccess($languageUid) && $contentTreeArr['el']['table']==='tt_content')	{
			$languageIcon = $this->link_edit($languageIcon, 'tt_content', $contentTreeArr['el']['uid'], TRUE);
		}

			// Create warning messages if neccessary:
		$warnings = '';
		if ($this->global_tt_content_elementRegister[$contentTreeArr['el']['uid']] > 1 && $this->rootElementLangParadigm !='free') {
			$warnings .= '<br/>'.$this->doc->icons(2).' <em>'.htmlspecialchars(sprintf($LANG->getLL('warning_elementusedmorethanonce',''), $this->global_tt_content_elementRegister[$contentTreeArr['el']['uid']], $contentTreeArr['el']['uid'])).'</em>';
		}

			// Displaying warning for container content (in default sheet - a limitation) elements if localization is enabled:
		$isContainerEl = count($contentTreeArr['sub']['sDEF']);
		if (!$this->modTSconfig['properties']['disableContainerElementLocalizationWarning'] && $this->rootElementLangParadigm !='free' && $isContainerEl && $contentTreeArr['el']['table'] === 'tt_content' && $contentTreeArr['el']['CType'] === 'templavoila_pi1' && !$contentTreeArr['ds_meta']['langDisable'])	{
			if ($contentTreeArr['ds_meta']['langChildren'])	{
				if (!$this->modTSconfig['properties']['disableContainerElementLocalizationWarning_warningOnly']) {
					$warnings .= '<br/>'.$this->doc->icons(2).' <b>'.$LANG->getLL('warning_containerInheritance').'</b>';
				}
			} else {
				$warnings .= '<br/>'.$this->doc->icons(3).' <b>'.$LANG->getLL('warning_containerSeparate').'</b>';
			}
		}

			// Preview made:
		$previewContent = $this->render_previewData($contentTreeArr['previewData'], $contentTreeArr['el'], $contentTreeArr['ds_meta'], $languageKey, $sheet);

			// Wrap workspace notification colors:
		if ($contentTreeArr['el']['_ORIG_uid'])	{
			$previewContent = '<div class="ver-element">'.($previewContent ? $previewContent : '<em>[New version]</em>').'</div>';
		}

			// Finally assemble the table:
		$finalContent =
			(!$this->translatorMode && $canCreateNew ? '<div class="sortableItem" id="' . $this->apiObj->flexform_getStringFromPointer($parentPointer) . '">' : '') . '
			<table cellpadding="0" cellspacing="0" style="width: 100%; border: 1px solid black; margin-bottom:5px;">
				<tr style="' . $elementTitlebarStyle . ';" class="sortable_handle">
					<td style="vertical-align:top;">' .
						'<span class="nobr">' .
						$languageIcon .
						$titleBarLeftButtons .
						($elementBelongsToCurrentPage ? '' : '<em>') . htmlspecialchars($contentTreeArr['el']['title']) . ($elementBelongsToCurrentPage ? '' : '</em>') .
						'</span>' .
						$warnings .
					'</td>
					<td nowrap="nowrap" style="text-align:right; vertical-align:top;">' .
						$titleBarRightButtons .
					'</td>
				</tr>
				<tr>
					<td colspan="2">' .
						$this->render_framework_subElements($contentTreeArr, $languageKey, $sheet) .
						$previewContent .
						$this->render_localizationInfoTable($contentTreeArr, $parentPointer, $parentDsMeta) .
					'</td>
				</tr>
			</table>
		';

		return $finalContent;
	}

	/**
	 * Renders the sub elements of the given elementContentTree array. This function basically
	 * renders the "new" and "paste" buttons for the parent element and then traverses through
	 * the sub elements (if any exist). The sub element's (preview-) content will be rendered
	 * by render_framework_singleSheet().
	 *
	 * Calls render_framework_allSheets() and therefore generates a recursion.
	 *
	 * @param	array		$elementContentTreeArr: Content tree starting with the element which possibly has sub elements
	 * @param	string		$languageKey: Language key for current display
	 * @param	string		$sheet: Key of the sheet we want to render
	 * @return	string		HTML output (a table) of the sub elements and some "insert new" and "paste" buttons
	 * @access protected
	 * @see render_framework_allSheets(), render_framework_singleSheet()
	 */
	function render_framework_subElements($elementContentTreeArr, $languageKey, $sheet){
		global $LANG;

		$beTemplate = '';
		$flagRenderBeLayout = false;

			// Define l/v keys for current language:
		$langChildren = intval($elementContentTreeArr['ds_meta']['langChildren']);
		$langDisable = intval($elementContentTreeArr['ds_meta']['langDisable']);

		$lKey = $langDisable ? 'lDEF' : ($langChildren ? 'lDEF' : 'l'.$languageKey);
		$vKey = $langDisable ? 'vDEF' : ($langChildren ? 'v'.$languageKey : 'vDEF');

		if (!is_array($elementContentTreeArr['sub'][$sheet]) || !is_array($elementContentTreeArr['sub'][$sheet][$lKey])) return '';

		$output = '';
		$cells = array();
		$headerCells = array();

				// gets the layout
		$beTemplate = $elementContentTreeArr['ds_meta']['beLayout'];

				// no layout, no special rendering
		$flagRenderBeLayout = $beTemplate? TRUE : FALSE;

			// Traverse container fields:
		foreach($elementContentTreeArr['sub'][$sheet][$lKey] as $fieldID => $fieldValuesContent)	{
			if ($elementContentTreeArr['previewData']['sheets'][$sheet][$fieldID]['isMapped'] && is_array($fieldValuesContent[$vKey]))	{
				$fieldContent = $fieldValuesContent[$vKey];

				$cellContent = '';

					// Create flexform pointer pointing to "before the first sub element":
				$subElementPointer = array (
					'table' => $elementContentTreeArr['el']['table'],
					'uid' => $elementContentTreeArr['el']['uid'],
					'sheet' => $sheet,
					'sLang' => $lKey,
					'field' => $fieldID,
					'vLang' => $vKey,
					'position' => 0
				);

				$canCreateNew = $GLOBALS['BE_USER']->isPSet($this->calcPerms, 'pages', 'new');

				if (!$this->translatorMode && $canCreateNew)	{

						// "New" and "Paste" icon:
					$newIcon = '<img'.t3lib_iconWorks::skinImg($this->doc->backPath,'gfx/new_el.gif','').' style="text-align: center; vertical-align: middle;" vspace="5" hspace="1" border="0" title="'.$LANG->getLL ('createnewrecord').'" alt="" />';
					$cellContent .= $this->link_new($newIcon, $subElementPointer);
					$cellContent .= '<span class="sortablePaste">' . $this->clipboardObj->element_getPasteButtons ($subElementPointer) . '</span>';
				}

					// Render the list of elements (and possibly call itself recursively if needed):
				if (is_array($fieldContent['el_list']))	 {
					foreach($fieldContent['el_list'] as $position => $subElementKey)	{
						$subElementArr = $fieldContent['el'][$subElementKey];

						if ((!$subElementArr['el']['isHidden'] || $this->MOD_SETTINGS['tt_content_showHidden']) && $this->displayElement($subElementArr))	{

								// When "onlyLocalized" display mode is set and an alternative language gets displayed
							if (($this->MOD_SETTINGS['langDisplayMode'] == 'onlyLocalized') && $this->currentLanguageUid>0)	{

									// Default language element. Subsitute displayed element with localized element
								if (($subElementArr['el']['sys_language_uid']==0) && is_array($subElementArr['localizationInfo'][$this->currentLanguageUid]) && ($localizedUid = $subElementArr['localizationInfo'][$this->currentLanguageUid]['localization_uid']))	{
									$localizedRecord = t3lib_BEfunc::getRecordWSOL('tt_content', $localizedUid, '*');
									$tree = $this->apiObj->getContentTree('tt_content', $localizedRecord);
									$subElementArr = $tree['tree'];
								}
							}
							$this->containedElements[$this->containedElementsPointer]++;

								// Modify the flexform pointer so it points to the position of the curren sub element:
							$subElementPointer['position'] = $position;

							$cellContent .= $this->render_framework_allSheets($subElementArr, $languageKey, $subElementPointer, $elementContentTreeArr['ds_meta']);

							if (!$this->translatorMode && $canCreateNew) {
									// "New" and "Paste" icon:
								$newIcon = '<img'.t3lib_iconWorks::skinImg($this->doc->backPath,'gfx/new_el.gif','').' style="text-align: center; vertical-align: middle;" vspace="5" hspace="1" border="0" title="'.$LANG->getLL ('createnewrecord').'" alt="" />';
								$cellContent .= $this->link_new($newIcon, $subElementPointer);

								$cellContent .= '<span class="sortablePaste">' . $this->clipboardObj->element_getPasteButtons ($subElementPointer) . '</span></div>';
							}

						}
					}
				}

				$cellIdStr = '';
				if ($GLOBALS['BE_USER']->isPSet($this->calcPerms, 'pages', 'editcontent')) {
					$tmpArr = $subElementPointer;
					unset($tmpArr['position']);
					$cellId = $this->apiObj->flexform_getStringFromPointer($tmpArr);
					$cellIdStr = ' id="' . $cellId . '"';
					$this->sortableContainers[] = $cellId;
				}

					// Add cell content to registers:
				if ($flagRenderBeLayout==TRUE) {
					$beTemplateCell = '<table width="100%" class="beTemplateCell"><tr><td valign="top" style="background-color: '.$this->doc->bgColor4.'; padding-top:0; padding-bottom:0;">'.$LANG->sL($fieldContent['meta']['title'],1).'</td></tr><tr><td valign="top" style="padding: 5px;">'.$cellContent.'</td></tr></table>';
					$beTemplate = str_replace('###'.$fieldID.'###', $beTemplateCell, $beTemplate);
				} else {
							// Add cell content to registers:
					$headerCells[]='<td valign="top" width="'.round(100/count($elementContentTreeArr['sub'][$sheet][$lKey])).'%" style="background-color: '.$this->doc->bgColor4.'; padding-top:0; padding-bottom:0;">'.$LANG->sL($fieldContent['meta']['title'],1).'</td>';
					$cells[]='<td '.$cellIdStr.' valign="top" width="'.round(100/count($elementContentTreeArr['sub'][$sheet][$lKey])).'%" style="border: 1px dashed #000; padding: 5px 5px 5px 5px;">'.$cellContent.'</td>';
				}
			}
		}

		if ($flagRenderBeLayout) {
			// removes not used markers
			$beTemplate = preg_replace("/###field_.*?###/", '', $beTemplate);
			return $beTemplate;
		}

			// Compile the content area for the current element (basically what was put together above):
		if (count ($headerCells) || count ($cells)) {
			$output = '
				<table border="0" cellpadding="2" cellspacing="2" width="100%">
					<tr>'.(count($headerCells) ? implode('', $headerCells) : '<td>&nbsp;</td>').'</tr>
					<tr>'.(count($cells) ? implode('', $cells) : '<td>&nbsp;</td>').'</tr>
				</table>
			';
		}

		return $output;
	}





	/*******************************************
	 *
	 * Rendering functions for certain subparts
	 *
	 *******************************************/

	/**
	 * Rendering the preview of content for Page module.
	 *
	 * @param	array		$previewData: Array with data from which a preview can be rendered.
	 * @param	array		$elData: Element data
	 * @param	array		$ds_meta: Data Structure Meta data
	 * @param	string		$languageKey: Current language key (so localized content can be shown)
	 * @param	string		$sheet: Sheet key
	 * @return	string		HTML content
	 */
	function render_previewData($previewData, $elData, $ds_meta, $languageKey, $sheet)	{
		global $LANG;

			// General preview of the row:
		$previewContent = is_array($previewData['fullRow']) && $elData['table']=='tt_content' ? $this->render_previewContent($previewData['fullRow']) : '';

			// Preview of FlexForm content if any:
		if (is_array($previewData['sheets'][$sheet]))	{

				// Define l/v keys for current language:
			$langChildren = intval($ds_meta['langChildren']);
			$langDisable = intval($ds_meta['langDisable']);
			$lKey = $langDisable ? 'lDEF' : ($langChildren ? 'lDEF' : 'l'.$languageKey);
			$vKey = $langDisable ? 'vDEF' : ($langChildren ? 'v'.$languageKey : 'vDEF');

			foreach($previewData['sheets'][$sheet] as $fieldData)	{
				$TCEformsConfiguration = $fieldData['TCEforms']['config'];
				$TCEformsLabel = $this->localizedFFLabel($fieldData['TCEforms']['label'], 1);	// title for non-section elements

				if ($fieldData['type']=='array')	{	// Making preview for array/section parts of a FlexForm structure:
					if (is_array($fieldData['subElements'][$lKey])) {
						if ($fieldData['section']) {
							foreach($fieldData['subElements'][$lKey] as $sectionData) {
								if (is_array($sectionData))	{
									$sectionFieldKey = key($sectionData);
									if (is_array ($sectionData[$sectionFieldKey]['el'])) {
										$previewContent .= '<ul>';
										foreach ($sectionData[$sectionFieldKey]['el'] as $containerFieldKey => $containerData) {
											$previewContent .= '<li><strong>'.$containerFieldKey.'</strong> '.$this->link_edit(htmlspecialchars(t3lib_div::fixed_lgd_cs(strip_tags($containerData[$vKey]),200)), 'tt_content', $previewData['fullRow']['uid']).'</li>';
										}
										$previewContent .= '</ul>';
									}
								}
							}
						} else {
							foreach ($fieldData['subElements'][$lKey] as $containerKey => $containerData) {
								$previewContent .= '<strong>'.$containerKey.'</strong> '.$this->link_edit(htmlspecialchars(t3lib_div::fixed_lgd_cs(strip_tags($containerData[$vKey]),200)), 'tt_content', $previewData['fullRow']['uid']).'<br />';
							}
						}
					}
				} else {	// Preview of flexform fields on top-level:
					$fieldValue = $fieldData['data'][$lKey][$vKey];

					if ($TCEformsConfiguration['type'] == 'group') {
						if ($TCEformsConfiguration['internal_type'] == 'file')	{
							// Render preview for images:
							$thumbnail = t3lib_BEfunc::thumbCode (array('dummyFieldName'=> $fieldValue), '', 'dummyFieldName', $this->doc->backPath, '', $TCEformsConfiguration['uploadfolder']);
							$previewContent .= '<strong>'.$TCEformsLabel.'</strong> '.$thumbnail.'<br />';
						}
					} else if ($TCEformsConfiguration['type'] != '') {
						// Render for everything else:
						$previewContent .= '<strong>'.$TCEformsLabel.'</strong> '. (!$fieldValue ? '' : $this->link_edit(htmlspecialchars(t3lib_div::fixed_lgd_cs(strip_tags($fieldValue),200)), 'tt_content', $previewData['fullRow']['uid'])).'<br />';
					}
				}
			}
		}

		return $previewContent;
	}

	/**
	 * Returns an HTMLized preview of a certain content element. If you'd like to register a new content type, you can easily use the hook
	 * provided at the beginning of the function.
	 *
	 * @param	array		$row: The row of tt_content containing the content element record.
	 * @return	string		HTML preview content
	 * @access protected
	 * @see		getContentTree(), render_localizationInfoTable()
	 */
	function render_previewContent($row) {
		global $TYPO3_CONF_VARS, $LANG;

		$hookObjectsArr = $this->hooks_prepareObjectsArray ('renderPreviewContentClass');
		$alreadyRendered = FALSE;
		$output = '';

			// Hook: renderPreviewContent_preProcess. Set 'alreadyRendered' to true if you provided a preview content for the current cType !
		reset($hookObjectsArr);
		while (list(,$hookObj) = each($hookObjectsArr)) {
			if (method_exists ($hookObj, 'renderPreviewContent_preProcess')) {
				$output .= $hookObj->renderPreviewContent_preProcess ($row, 'tt_content', $alreadyRendered, $this);
			}
		}

		if (!$alreadyRendered) {
				// Preview content for non-flexible content elements:
			switch($row['CType'])	{
				case 'text':		//	Text
				case 'table':		//	Table
				case 'mailform':	//	Form
					$output = $this->link_edit('<strong>'.$LANG->sL(t3lib_BEfunc::getItemLabel('tt_content','bodytext'),1).'</strong> '.htmlspecialchars(t3lib_div::fixed_lgd_cs(trim(strip_tags($row['bodytext'])),2000)),'tt_content',$row['uid']).'<br />';
					break;
				case 'image':		//	Image
					$output = $this->link_edit('<strong>'.$LANG->sL(t3lib_BEfunc::getItemLabel('tt_content','image'),1).'</strong><br /> ', 'tt_content', $row['uid']).t3lib_BEfunc::thumbCode ($row, 'tt_content', 'image', $this->doc->backPath).'<br />';
					break;
				case 'textpic':		//	Text w/image
				case 'splash':		//	Textbox
					$thumbnail = '<strong>'.$LANG->sL(t3lib_BEfunc::getItemLabel('tt_content','image'),1).'</strong><br />';
					$thumbnail .= t3lib_BEfunc::thumbCode($row, 'tt_content', 'image', $this->doc->backPath);
					$text = $this->link_edit('<strong>'.$LANG->sL(t3lib_BEfunc::getItemLabel('tt_content','bodytext'),1).'</strong> '.htmlspecialchars(t3lib_div::fixed_lgd_cs(trim(strip_tags($row['bodytext'])),2000)),'tt_content',$row['uid']);
					$output='<table><tr><td valign="top">'.$text.'</td><td valign="top">'.$thumbnail.'</td></tr></table>'.'<br />';
					break;
				case 'bullets':		//	Bullets
					$htmlBullets = '';
					$bulletsArr = explode ("\n", t3lib_div::fixed_lgd_cs($row['bodytext'],2000));
					if (is_array ($bulletsArr)) {
						foreach ($bulletsArr as $listItem) {
							$htmlBullets .= htmlspecialchars(trim(strip_tags($listItem))).'<br />';
						}
					}
					$output = $this->link_edit('<strong>'.$LANG->sL(t3lib_BEfunc::getItemLabel('tt_content','bodytext'),1).'</strong><br />'.$htmlBullets, 'tt_content', $row['uid']).'<br />';
					break;
				case 'uploads':		//	Filelinks
					$output = $this->link_edit('<strong>'.$LANG->sL(t3lib_BEfunc::getItemLabel('tt_content','media'),1).'</strong><br />'.str_replace (',','<br />',htmlspecialchars(t3lib_div::fixed_lgd_cs(trim(strip_tags($row['media'])),2000))), 'tt_content', $row['uid']).'<br />';
					break;
				case 'multimedia':	//	Multimedia
					$output = $this->link_edit ('<strong>'.$LANG->sL(t3lib_BEfunc::getItemLabel('tt_content','multimedia'),1).'</strong><br />' . str_replace (',','<br />',htmlspecialchars(t3lib_div::fixed_lgd_cs(trim(strip_tags($row['multimedia'])),2000))), 'tt_content', $row['uid']).'<br />';
					break;
				case 'menu':		//	Menu / Sitemap
					$output = $this->link_edit ('<strong>'.$LANG->sL(t3lib_BEfunc::getItemLabel('tt_content','menu_type')).'</strong> '.$LANG->sL(t3lib_BEfunc::getLabelFromItemlist('tt_content','menu_type',$row['menu_type'])).'<br />'.
						'<strong>'.$LANG->sL(t3lib_BEfunc::getItemLabel('tt_content','pages')).'</strong> '.$row['pages'], 'tt_content', $row['uid']).'<br />';
					break;
				case 'list':		//	Insert Plugin
					$extraInfo = $this->render_previewContent_extraPluginInfo($row);
					$output = $this->link_edit('<strong>'.$LANG->sL(t3lib_BEfunc::getItemLabel('tt_content','list_type')).'</strong> ' . htmlspecialchars($LANG->sL(t3lib_BEfunc::getLabelFromItemlist('tt_content','list_type',$row['list_type']))).' &ndash; '.htmlspecialchars($extraInfo ? $extraInfo : $row['list_type']), 'tt_content', $row['uid']).'<br />';
					break;
				case 'html':		//	HTML
					$output = $this->link_edit('<strong>'.$LANG->sL(t3lib_BEfunc::getItemLabel('tt_content','bodytext'),1).'</strong> ' . htmlspecialchars(t3lib_div::fixed_lgd_cs(trim($row['bodytext']),2000)),'tt_content',$row['uid']).'<br />';
					break;
				case 'header': // Header
					$output = $this->link_edit('<strong>'.$LANG->sL(t3lib_BEfunc::getItemLabel('tt_content','header'),1).'</strong> ' . htmlspecialchars(t3lib_div::fixed_lgd_cs(trim(strip_tags($row['header'])),2000)),'tt_content',$row['uid']).'<br />';
					break;
				case 'search':			//	Search Box
				case 'login':			//	Login Box
				case 'shortcut':		//	Insert records
				case 'div':				//	Divider
				case 'templavoila_pi1': //	Flexible Content Element: Rendered directly in getContentTree*()
					break;
				default:
						// return CType name for unhandled CType
					$output='<strong>'.htmlspecialchars ($row['CType']).'</strong><br />';
			}
		}
		return $output;
	}


	/**
	 * Renders additional information about plugins (if available)
	 *
	 * @param	array	$row	Row from database
	 * @return	string	Information
	 */
	function render_previewContent_extraPluginInfo($row) {
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['list_type_Info'][$row['list_type']]))	{
			$hookArr = $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['list_type_Info'][$row['list_type']];
		} elseif (is_array($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['list_type_Info']['_DEFAULT']))	{
			$hookArr = $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['list_type_Info']['_DEFAULT'];
		}
		$hookOut = '';
		if (count($hookArr) > 0)	{
			$_params = array('pObj' => &$this, 'row' => $row, 'infoArr' => $infoArr);
			foreach ($hookArr as $_funcRef)	{
				$hookOut .= t3lib_div::callUserFunction($_funcRef, $_params, $this);
			}
		}
		return $hookOut;
	}

	/**
	 * Renders a little table containing previews of translated version of the current content element.
	 *
	 * @param	array		$contentTreeArr: Part of the contentTreeArr for the element
	 * @param	string		$parentPointer: Flexform pointer pointing to the current element (from the parent's perspective)
	 * @param	array		$parentDsMeta: Meta array from parent DS (passing information about parent containers localization mode)
	 * @return	string		HTML
	 * @access protected
	 * @see 	render_framework_singleSheet()
	 */
	function render_localizationInfoTable($contentTreeArr, $parentPointer, $parentDsMeta=array()) {
		global $LANG, $BE_USER;

				// LOCALIZATION information for content elements (non Flexible Content Elements)
		$output = '';
		if ($contentTreeArr['el']['table']=='tt_content' && $contentTreeArr['el']['sys_language_uid']<=0)	{

				// Traverse the available languages of the page (not default and [All])
			$tRows=array();
			foreach($this->translatedLanguagesArr as $sys_language_uid => $sLInfo)	{
				if ($this->MOD_SETTINGS['langDisplayMode'] && ($this->currentLanguageUid != $sys_language_uid)) continue;
				if ($sys_language_uid > 0)	{
					$l10nInfo = '';
					$flagLink_begin = $flagLink_end = '';

					switch((string)$contentTreeArr['localizationInfo'][$sys_language_uid]['mode'])	{
						case 'exists':
							$olrow = t3lib_BEfunc::getRecordWSOL('tt_content',$contentTreeArr['localizationInfo'][$sys_language_uid]['localization_uid']);

							$localizedRecordInfo = array(
								'uid' => $olrow['uid'],
								'row' => $olrow,
								'content' => $this->render_previewContent($olrow)
							);

								// Put together the records icon including content sensitive menu link wrapped around it:
							$recordIcon_l10n = t3lib_iconWorks::getIconImage('tt_content',$localizedRecordInfo['row'],$this->doc->backPath,'class="absmiddle" title="'.htmlspecialchars('[tt_content:'.$localizedRecordInfo['uid'].']').'"');
							if (!$this->translatorMode)	{
								$recordIcon_l10n = $this->doc->wrapClickMenuOnIcon($recordIcon_l10n,'tt_content',$localizedRecordInfo['uid'],1,'&amp;callingScriptId='.rawurlencode($this->doc->scriptID), 'new,copy,cut,pasteinto,pasteafter');
							}
							$l10nInfo =
								$this->getRecordStatHookValue('tt_content', $localizedRecordInfo['row']['uid']).
								$recordIcon_l10n .
								htmlspecialchars(t3lib_div::fixed_lgd_cs(strip_tags(t3lib_BEfunc::getRecordTitle('tt_content', $localizedRecordInfo['row'])), 50));

							$l10nInfo.= '<br/>'.$localizedRecordInfo['content'];

							list($flagLink_begin, $flagLink_end) = explode('|*|', $this->link_edit('|*|', 'tt_content', $localizedRecordInfo['uid'], TRUE));
							if ($this->translatorMode)	{
								$l10nInfo.= '<br/>'.$flagLink_begin.'<em>'.$LANG->getLL('clickToEditTranslation').'</em>'.$flagLink_end;
							}

								// Wrap workspace notification colors:
							if ($olrow['_ORIG_uid'])	{
								$l10nInfo = '<div class="ver-element">'.$l10nInfo.'</div>';
							}

							$this->global_localization_status[$sys_language_uid][]=array(
								'status' => 'exist',
								'parent_uid' => $contentTreeArr['el']['uid'],
								'localized_uid' => $localizedRecordInfo['row']['uid'],
								'sys_language' => $contentTreeArr['el']['sys_language_uid']
							);
						break;
						case 'localize':

							if ($this->rootElementLangParadigm =='free')	{
								$showLocalizationLinks = !$parentDsMeta['langDisable'];	// For this paradigm, show localization links only if localization is enabled for DS (regardless of Inheritance and Separate)
							} else {
								$showLocalizationLinks = ($parentDsMeta['langDisable'] || $parentDsMeta['langChildren']);	// Adding $parentDsMeta['langDisable'] here means that the "Create a copy for translation" link is shown only if the parent container element has localization mode set to "Disabled" or "Inheritance" - and not "Separate"!
							}
								// Assuming that only elements which have the default language set are candidates for localization. In case the language is [ALL] then it is assumed that the element should stay "international".
							if ((int)$contentTreeArr['el']['sys_language_uid']===0 && $showLocalizationLinks)	{

									// Copy for language:
								if ($this->rootElementLangParadigm =='free')	{
									$sourcePointerString = $this->apiObj->flexform_getStringFromPointer($parentPointer);
									$onClick = "document.location='index.php?".$this->link_getParameters().'&source='.rawurlencode($sourcePointerString).'&localizeElement='.$sLInfo['ISOcode']."'; return false;";
								} else {
									$params='&cmd[tt_content]['.$contentTreeArr['el']['uid'].'][localize]='.$sys_language_uid;
									$onClick = "document.location='".$GLOBALS['SOBE']->doc->issueCommand($params)."'; return false;";
								}

								$linkLabel = $LANG->getLL('createcopyfortranslation',1).' ('.htmlspecialchars($sLInfo['title']).')';
								$localizeIcon = '<img'.t3lib_iconWorks::skinImg($this->doc->backPath,'gfx/clip_copy.gif','width="12" height="12"').' class="bottom" title="'.$linkLabel.'" alt="" />';

								$l10nInfo = '<a href="#" onclick="'.htmlspecialchars($onClick).'">'.$localizeIcon.'</a>';
								$l10nInfo .= ' <em><a href="#" onclick="'.htmlspecialchars($onClick).'">'.$linkLabel.'</a></em>';
								$flagLink_begin = '<a href="#" onclick="'.htmlspecialchars($onClick).'">';
								$flagLink_end = '</a>';

								$this->global_localization_status[$sys_language_uid][]=array(
									'status' => 'localize',
									'parent_uid' => $contentTreeArr['el']['uid'],
									'sys_language' => $contentTreeArr['el']['sys_language_uid']
								);
							}
						break;
						case 'localizedFlexform':
								// Here we want to show the "Localized FlexForm" information (and link to edit record) _only_ if there are other fields than group-fields for content elements: It only makes sense for a translator to deal with the record if that is the case.
								// Change of strategy (27/11): Because there does not have to be content fields; could be in sections or arrays and if thats the case you still want to localize them! There has to be another way...
							// if (count($contentTreeArr['contentFields']['sDEF']))	{
								list($flagLink_begin, $flagLink_end) = explode('|*|', $this->link_edit('|*|', 'tt_content', $contentTreeArr['el']['uid'], TRUE));
								$l10nInfo = $flagLink_begin.'<em>[Click to translate FlexForm]</em>'.$flagLink_end;
								$this->global_localization_status[$sys_language_uid][]=array(
									'status' => 'flex',
									'parent_uid' => $contentTreeArr['el']['uid'],
									'sys_language' => $contentTreeArr['el']['sys_language_uid']
								);
							// }
						break;
					}

					if ($l10nInfo && $BE_USER->checkLanguageAccess($sys_language_uid))	{
						$tRows[]='
							<tr class="bgColor4">
								<td width="1%">'.$flagLink_begin.($sLInfo['flagIcon'] ? '<img src="'.$sLInfo['flagIcon'].'" alt="'.htmlspecialchars($sLInfo['title']).'" title="'.htmlspecialchars($sLInfo['title']).'" />' : $sLInfo['title']).$flagLink_end.'</td>
								<td width="99%">'.$l10nInfo.'</td>
							</tr>';
					}
				}
			}

			$output = count($tRows) ? '
				<table border="0" cellpadding="0" cellspacing="1" width="100%" class="lrPadding">
					<tr class="bgColor4-20">
						<td colspan="2">'.$LANG->getLL('element_localizations',1).':</td>
					</tr>
					'.implode('',$tRows).'
				</table>
			' : '';
		}
		return $output;
	}






	/*******************************************
	 *
	 * Outline rendering:
	 *
	 *******************************************/

	/**
	 * Rendering the outline display of the page structure
	 *
	 * @param	array		$contentTreeArr: DataStructure info array (the whole tree)
	 * @return	string		HTML
	 */
	function render_outline($contentTreeArr)	{
		global $LANG;

			// Load possible website languages:
		$this->translatedLanguagesArr_isoCodes = array();
		foreach($this->translatedLanguagesArr as $langInfo)	{
			if ($langInfo['ISOcode'])	{
				$this->translatedLanguagesArr_isoCodes['all_lKeys'][]='l'.$langInfo['ISOcode'];
				$this->translatedLanguagesArr_isoCodes['all_vKeys'][]='v'.$langInfo['ISOcode'];
			}
		}

			// Rendering the entries:
		$entries = array();
		$this->render_outline_element($contentTreeArr,$entries);

			// Header of table:
		$output='';
		$output.='<tr class="bgColor5 tableheader">
				<td class="nobr">'.$LANG->getLL('outline_header_title',1).'</td>
				<td class="nobr">'.$LANG->getLL('outline_header_controls',1).'</td>
				<td class="nobr">'.$LANG->getLL('outline_header_status',1).'</td>
				<td class="nobr">'.$LANG->getLL('outline_header_element',1).'</td>
			</tr>';

			// Render all entries:
		$xmlCleanCandidates = FALSE;
		foreach($entries as $entry)	{

				// Create indentation code:
			$indent = '';
			for($a=0;$a<$entry['indentLevel'];$a++)	{
				$indent.='&nbsp;&nbsp;&nbsp;&nbsp;';
			}

				// Create status for FlexForm XML:
				// WARNING: Also this section contains cleaning of XML which is sort of mixing functionality but a quick and easy solution for now.
				// @Robert: How would you like this implementation better? Please advice and I will change it according to your wish!
			$status = '';
			if ($entry['table'] && $entry['uid'])	{
				$flexObj = t3lib_div::makeInstance('t3lib_flexformtools');
				$recRow = t3lib_BEfunc::getRecord($entry['table'], $entry['uid']);
				if ($recRow['tx_templavoila_flex'])	{

						// Clean XML:
					$newXML = $flexObj->cleanFlexFormXML($entry['table'],'tx_templavoila_flex',$recRow);

						// If the clean-all command is sent AND there is a difference in current/clean XML, save the clean:
					if (t3lib_div::_POST('_CLEAN_XML_ALL') && md5($recRow['tx_templavoila_flex'])!=md5($newXML)) {
						$dataArr = array();
						$dataArr[$entry['table']][$entry['uid']]['tx_templavoila_flex'] = $newXML;

							// Init TCEmain object and store:
						$tce = t3lib_div::makeInstance('t3lib_TCEmain');
						$tce->stripslashes_values=0;
						$tce->start($dataArr,array());
						$tce->process_datamap();

							// Re-fetch record:
						$recRow = t3lib_BEfunc::getRecord($entry['table'], $entry['uid']);
					}

						// Render status:
					$xmlUrl = '../cm2/index.php?viewRec[table]='.$entry['table'].'&viewRec[uid]='.$entry['uid'].'&viewRec[field_flex]=tx_templavoila_flex';
					if (md5($recRow['tx_templavoila_flex'])!=md5($newXML))	{
						$status = $this->doc->icons(1).'<a href="'.htmlspecialchars($xmlUrl).'">'.$LANG->getLL('outline_status_dirty',1).'</a><br/>';
						$xmlCleanCandidates = TRUE;
					} else {
						$status = $this->doc->icons(-1).'<a href="'.htmlspecialchars($xmlUrl).'">'.$LANG->getLL('outline_status_clean',1).'</a><br/>';
					}
				}
			}

				// Compile table row:
			$output.='<tr class="'.($entry['isNewVersion']?'bgColor5':'bgColor4').'" style="'.$entry['elementTitlebarStyle'].'">
					<td class="nobr">'.$indent.$entry['icon'].$entry['flag'].$entry['title'].'</td>
					<td class="nobr">'.$entry['controls'].'</td>
					<td>'.$status.$entry['warnings'].($entry['isNewVersion']?$this->doc->icons(1).'New version!':'').'</td>
					<td class="nobr">'.htmlspecialchars($entry['id'] ? $entry['id'] : $entry['table'].':'.$entry['uid']).'</td>
				</tr>';
		}
		$output = '<table border="0" cellpadding="1" cellspacing="1">'.$output.'</table>';

			// Show link for cleaning all XML structures:
		if ($xmlCleanCandidates)	{
			$output.= '<br/>
				'. t3lib_BEfunc::cshItem('_MOD_web_txtemplavoilaM1', 'outline_status_cleanall', $this->doc->backPath).'
				<input type="submit" value="'.$LANG->getLL('outline_status_cleanAll',1).'" name="_CLEAN_XML_ALL" /><br/><br/>
			';
		}

		return $output;
	}

	/**
	 * Rendering a single element in outline:
	 *
	 * @param	array		$contentTreeArr: DataStructure info array (the whole tree)
	 * @param	array		$entries: Entries accumulated in this array (passed by reference)
	 * @param	integer		$indentLevel: Indentation level
	 * @param	array		$parentPointer: Element position in structure
	 * @param	string		$controls: HTML for controls to add for this element
	 * @return	void
	 * @access protected
	 * @see	render_outline_allSheets()
	 */
	function render_outline_element($contentTreeArr, &$entries, $indentLevel=0, $parentPointer=array(), $controls='') {
		global $LANG, $TYPO3_CONF_VARS;

			// Get record of element:
		$elementBelongsToCurrentPage = $contentTreeArr['el']['table'] == 'pages' || $contentTreeArr['el']['pid'] == $this->rootElementUid_pidForContent;

			// Prepare the record icon including a context sensitive menu link wrapped around it:
		$recordIcon = '<img'.t3lib_iconWorks::skinImg($this->doc->backPath,$contentTreeArr['el']['icon'],'').' style="text-align: center; vertical-align: middle;" width="18" height="16" border="0" title="'.htmlspecialchars('['.$contentTreeArr['el']['table'].':'.$contentTreeArr['el']['uid'].']').'" alt="" />';
		$titleBarLeftButtons = $this->translatorMode ? $recordIcon : $this->doc->wrapClickMenuOnIcon($recordIcon,$contentTreeArr['el']['table'], $contentTreeArr['el']['uid'], 1,'&amp;callingScriptId='.rawurlencode($this->doc->scriptID), 'new,copy,cut,pasteinto,pasteafter,delete');
		$titleBarLeftButtons.= $this->getRecordStatHookValue($contentTreeArr['el']['table'],$contentTreeArr['el']['uid']);

			// Prepare table specific settings:
		switch ($contentTreeArr['el']['table']) {
			case 'pages' :
				$titleBarLeftButtons .= $this->translatorMode ? '' : $this->link_edit('<img'.t3lib_iconWorks::skinImg($this->doc->backPath,'gfx/edit2.gif','').' title="'.htmlspecialchars($LANG->sL('LLL:EXT:lang/locallang_mod_web_list.xml:editPage')).'" alt="" style="text-align: center; vertical-align: middle; border:0;" />',$contentTreeArr['el']['table'],$contentTreeArr['el']['uid']);
				$titleBarRightButtons = '';

				$addGetVars = ($this->currentLanguageUid?'&L='.$this->currentLanguageUid:'');
				$viewPageOnClick = 'onclick= "'.htmlspecialchars(t3lib_BEfunc::viewOnClick($contentTreeArr['el']['uid'], $this->doc->backPath, t3lib_BEfunc::BEgetRootLine($contentTreeArr['el']['uid']),'','',$addGetVars)).'"';
				$viewPageIcon = '<img'.t3lib_iconWorks::skinImg($this->doc->backPath,'gfx/zoom.gif','width="12" height="12"').' title="'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.showPage',1).'" hspace="3" alt="" style="text-align: center; vertical-align: middle;" />';
				$titleBarLeftButtons .= '<a href="#" '.$viewPageOnClick.'>'.$viewPageIcon.'</a>';
			break;
			case 'tt_content' :
				$languageUid = $contentTreeArr['el']['sys_language_uid'];

				if ($this->translatorMode)	{
					$titleBarRightButtons = '';
				} else {
						// Create CE specific buttons:
					$linkMakeLocal = !$elementBelongsToCurrentPage ? $this->link_makeLocal('<img'.t3lib_iconWorks::skinImg($this->doc->backPath,t3lib_extMgm::extRelPath('templavoila').'mod1/makelocalcopy.gif','').' title="'.$LANG->getLL('makeLocal').'" border="0" alt="" />', $parentPointer) : '';
					$linkUnlink = $this->link_unlink('<img'.t3lib_iconWorks::skinImg($this->doc->backPath,'gfx/garbage.gif','').' title="'.$LANG->getLL('unlinkRecord').'" border="0" alt="" />', $parentPointer, FALSE);
					$linkEdit = ($elementBelongsToCurrentPage ? $this->link_edit('<img'.t3lib_iconWorks::skinImg($this->doc->backPath,'gfx/edit2.gif','').' title="'.$LANG->getLL ('editrecord').'" border="0" alt="" />',$contentTreeArr['el']['table'],$contentTreeArr['el']['uid']) : '');

					$titleBarRightButtons = $linkEdit . $this->clipboardObj->element_getSelectButtons ($parentPointer) . $linkMakeLocal . $linkUnlink;
				}
			break;
		}

			// Prepare the language icon:
		$languageLabel = htmlspecialchars ($this->allAvailableLanguages[$contentTreeArr['el']['sys_language_uid']]['title']);
		$languageIcon = $this->allAvailableLanguages[$languageUid]['flagIcon'] ? '<img src="'.$this->allAvailableLanguages[$languageUid]['flagIcon'].'" title="'.$languageLabel.'" alt="'.$languageLabel.'" style="text-align: center; vertical-align: middle;" />' : ($languageLabel && $languageUid ? '['.$languageLabel.']' : '');

			// If there was a langauge icon and the language was not default or [all] and if that langauge is accessible for the user, then wrap the flag with an edit link (to support the "Click the flag!" principle for translators)
		if ($languageIcon && $languageUid>0 && $GLOBALS['BE_USER']->checkLanguageAccess($languageUid) && $contentTreeArr['el']['table']==='tt_content')	{
			$languageIcon = $this->link_edit($languageIcon, 'tt_content', $contentTreeArr['el']['uid'], TRUE);
		}

			// Create warning messages if neccessary:
		$warnings = '';
		if ($this->global_tt_content_elementRegister[$contentTreeArr['el']['uid']] > 1 && $this->rootElementLangParadigm !='free') {
			$warnings .= '<br/>'.$this->doc->icons(2).' <em>'.htmlspecialchars(sprintf($LANG->getLL('warning_elementusedmorethanonce',''), $this->global_tt_content_elementRegister[$contentTreeArr['el']['uid']], $contentTreeArr['el']['uid'])).'</em>';
		}

			// Displaying warning for container content (in default sheet - a limitation) elements if localization is enabled:
		$isContainerEl = count($contentTreeArr['sub']['sDEF']);
		if (!$this->modTSconfig['properties']['disableContainerElementLocalizationWarning'] && $this->rootElementLangParadigm !='free' && $isContainerEl && $contentTreeArr['el']['table'] === 'tt_content' && $contentTreeArr['el']['CType'] === 'templavoila_pi1' && !$contentTreeArr['ds_meta']['langDisable'])	{
			if ($contentTreeArr['ds_meta']['langChildren'])	{
				if (!$this->modTSconfig['properties']['disableContainerElementLocalizationWarning_warningOnly']) {
					$warnings .= '<br/>'.$this->doc->icons(2).' <b>'.$LANG->getLL('warning_containerInheritance_short').'</b>';
				}
			} else {
				$warnings .= '<br/>'.$this->doc->icons(3).' <b>'.$LANG->getLL('warning_containerSeparate_short').'</b>';
			}
		}

			// Create entry for this element:
		$entries[] = array(
			'indentLevel' => $indentLevel,
			'icon' => $titleBarLeftButtons,
			'title' => ($elementBelongsToCurrentPage?'':'<em>').htmlspecialchars($contentTreeArr['el']['title']).($elementBelongsToCurrentPage ? '' : '</em>'),
			'warnings' => $warnings,
			'controls' => $titleBarRightButtons.$controls,
			'table' => $contentTreeArr['el']['table'],
			'uid' =>  $contentTreeArr['el']['uid'],
			'flag' => $languageIcon,
			'isNewVersion' => $contentTreeArr['el']['_ORIG_uid'] ? TRUE : FALSE,
			'elementTitlebarStyle' => (!$elementBelongsToCurrentPage ? 'background-color: '.$this->doc->bgColor6 : '')
		);


			// Create entry for localizaitons...
		$this->render_outline_localizations($contentTreeArr, $entries, $indentLevel+1);

			// Create entries for sub-elements in all sheets:
		if ($contentTreeArr['sub'])	{
			foreach($contentTreeArr['sub'] as $sheetKey => $sheetInfo)	{
				if (is_array($sheetInfo))	{
					$this->render_outline_subElements($contentTreeArr, $sheetKey, $entries, $indentLevel+1);
				}
			}
		}
	}

	/**
	 * Rendering outline for child-elements
	 *
	 * @param	array		$contentTreeArr: DataStructure info array (the whole tree)
	 * @param	string		$sheet: Which sheet to display
	 * @param	array		$entries: Entries accumulated in this array (passed by reference)
	 * @param	integer		$indentLevel: Indentation level
	 * @return	void
	 * @access protected
	 */
	function render_outline_subElements($contentTreeArr, $sheet, &$entries, $indentLevel)	{
		global $LANG;

			// Define l/v keys for current language:
		$langChildren = intval($contentTreeArr['ds_meta']['langChildren']);
		$langDisable = intval($contentTreeArr['ds_meta']['langDisable']);
		$lKeys = $langDisable ? array('lDEF') : ($langChildren ? array('lDEF') : $this->translatedLanguagesArr_isoCodes['all_lKeys']);
		$vKeys = $langDisable ? array('vDEF') : ($langChildren ? $this->translatedLanguagesArr_isoCodes['all_vKeys'] : array('vDEF'));

			// Traverse container fields:
		foreach($lKeys as $lKey)	{

				// Traverse fields:
			if (is_array($contentTreeArr['sub'][$sheet][$lKey]))	{
				foreach($contentTreeArr['sub'][$sheet][$lKey] as $fieldID => $fieldValuesContent)	{
					foreach($vKeys as $vKey)	{

						if (is_array($fieldValuesContent[$vKey]))	{
							$fieldContent = $fieldValuesContent[$vKey];

								// Create flexform pointer pointing to "before the first sub element":
							$subElementPointer = array (
								'table' => $contentTreeArr['el']['table'],
								'uid' => $contentTreeArr['el']['uid'],
								'sheet' => $sheet,
								'sLang' => $lKey,
								'field' => $fieldID,
								'vLang' => $vKey,
								'position' => 0
							);

							if (!$this->translatorMode)	{
									// "New" and "Paste" icon:
								$newIcon = '<img'.t3lib_iconWorks::skinImg($this->doc->backPath,'gfx/new_el.gif','').' style="text-align: center; vertical-align: middle;" vspace="5" hspace="1" border="0" title="'.$LANG->getLL ('createnewrecord').'" alt="" />';
								$controls = $this->link_new($newIcon, $subElementPointer);
								$controls .= $this->clipboardObj->element_getPasteButtons($subElementPointer);
							}

								// Add entry for lKey level:
							$specialPath = ($sheet!='sDEF'?'<'.$sheet.'>':'').($lKey!='lDEF'?'<'.$lKey.'>':'').($vKey!='vDEF'?'<'.$vKey.'>':'');
							$entries[] = array(
								'indentLevel' => $indentLevel,
								'icon' => '',
								'title' => '<b>'.$LANG->sL($fieldContent['meta']['title'],1).'</b>'.($specialPath ? ' <em>'.htmlspecialchars($specialPath).'</em>' : ''),
								'id' => '<'.$sheet.'><'.$lKey.'><'.$fieldID.'><'.$vKey.'>',
								'controls' => $controls
							);

								// Render the list of elements (and possibly call itself recursively if needed):
							if (is_array($fieldContent['el_list']))	 {
								foreach($fieldContent['el_list'] as $position => $subElementKey)	{
									$subElementArr = $fieldContent['el'][$subElementKey];
									if (!$subElementArr['el']['isHidden'] || $this->MOD_SETTINGS['tt_content_showHidden'])	{

											// Modify the flexform pointer so it points to the position of the curren sub element:
										$subElementPointer['position'] = $position;

										if (!$this->translatorMode)	{
												// "New" and "Paste" icon:
											$newIcon = '<img'.t3lib_iconWorks::skinImg($this->doc->backPath,'gfx/new_el.gif','').' style="text-align: center; vertical-align: middle;" vspace="5" hspace="1" border="0" title="'.$LANG->getLL ('createnewrecord').'" alt="" />';
											$controls = $this->link_new($newIcon, $subElementPointer);
											$controls .= $this->clipboardObj->element_getPasteButtons ($subElementPointer);
										}

										$this->render_outline_element($subElementArr, $entries, $indentLevel+1, $subElementPointer, $controls);
									}
								}
							}
						}
					}
				}
			}
		}
	}

	/**
	 * Renders localized elements of a record
	 *
	 * @param	array		$contentTreeArr: Part of the contentTreeArr for the element
	 * @param	array		$entries: Entries accumulated in this array (passed by reference)
	 * @param	integer		$indentLevel: Indentation level
	 * @return	string		HTML
	 * @access protected
	 * @see 	render_framework_singleSheet()
	 */
	function render_outline_localizations($contentTreeArr, &$entries, $indentLevel) {
		global $LANG, $BE_USER;

		if ($contentTreeArr['el']['table']=='tt_content' && $contentTreeArr['el']['sys_language_uid']<=0)	{

				// Traverse the available languages of the page (not default and [All])
			foreach($this->translatedLanguagesArr as $sys_language_uid => $sLInfo)	{
				if ($sys_language_uid > 0 && $BE_USER->checkLanguageAccess($sys_language_uid))	{
					switch((string)$contentTreeArr['localizationInfo'][$sys_language_uid]['mode'])	{
						case 'exists':

								// Get localized record:
							$olrow = t3lib_BEfunc::getRecordWSOL('tt_content',$contentTreeArr['localizationInfo'][$sys_language_uid]['localization_uid']);

								// Put together the records icon including content sensitive menu link wrapped around it:
							$recordIcon_l10n = $this->getRecordStatHookValue('tt_content', $olrow['uid']).
								t3lib_iconWorks::getIconImage('tt_content',$olrow,$this->doc->backPath,'class="absmiddle" title="'.htmlspecialchars('[tt_content:'.$olrow['uid'].']').'"');
							if (!$this->translatorMode)	{
								$recordIcon_l10n = $this->doc->wrapClickMenuOnIcon($recordIcon_l10n,'tt_content',$olrow['uid'],1,'&amp;callingScriptId='.rawurlencode($this->doc->scriptID), 'new,copy,cut,pasteinto,pasteafter');
							}

							list($flagLink_begin, $flagLink_end) = explode('|*|', $this->link_edit('|*|', 'tt_content', $olrow['uid'], TRUE));

								// Create entry for this element:
							$entries[] = array(
								'indentLevel' => $indentLevel,
								'icon' => $recordIcon_l10n,
								'title' => t3lib_BEfunc::getRecordTitle('tt_content', $olrow),
								'table' => 'tt_content',
								'uid' =>  $olrow['uid'],
								'flag' => $flagLink_begin.($sLInfo['flagIcon'] ? '<img src="'.$sLInfo['flagIcon'].'" style="text-align: center; vertical-align: middle;" alt="'.htmlspecialchars($sLInfo['title']).'" title="'.htmlspecialchars($sLInfo['title']).'" />' : $sLInfo['title']).$flagLink_end,
								'isNewVersion' => $olrow['_ORIG_uid'] ? TRUE : FALSE,
							);
						break;
					}
				}
			}
		}
	}








	/*******************************************
	 *
	 * Link functions (protected)
	 *
	 *******************************************/

	/**
	 * Returns an HTML link for editing
	 *
	 * @param	string		$label: The label (or image)
	 * @param	string		$table: The table, fx. 'tt_content'
	 * @param	integer		$uid: The uid of the element to be edited
	 * @param	boolean		$forced: By default the link is not shown if translatorMode is set, but with this boolean it can be forced anyway.
	 * @return	string		HTML anchor tag containing the label and the correct link
	 * @access protected
	 */
	function link_edit($label, $table, $uid, $forced=FALSE)	{
		if ($label) {
			if (($table == 'pages' && ($this->calcPerms & 2) ||
				$table != 'pages' && ($this->calcPerms & 16)) &&
				(!$this->translatorMode || $forced))	{
					if($table == "pages" &&	 $this->currentLanguageUid) {
						return '<a href="index.php?'.$this->link_getParameters().'&amp;editPageLanguageOverlay='.$this->currentLanguageUid.'">'.$label.'</a>';
					} else {
						$onClick = t3lib_BEfunc::editOnClick('&edit['.$table.']['.$uid.']=edit', $this->doc->backPath);
						return '<a style="text-decoration: none;" href="#" onclick="'.htmlspecialchars($onClick).'">'.$label.'</a>';
					}
				} else {
					return $label;
				}
		}
		return '';
	}

	/**
	 * Returns an HTML link for creating a new record
	 *
	 * @param	string		$label: The label (or image)
	 * @param	array		$parentPointer: Flexform pointer defining the parent element of the new record
	 * @return	string		HTML anchor tag containing the label and the correct link
	 * @access protected
	 */
	function link_new($label, $parentPointer)	{

		$parameters =
			$this->link_getParameters().
			'&amp;parentRecord='.rawurlencode($this->apiObj->flexform_getStringFromPointer($parentPointer));
		return '<a href="'.'db_new_content_el.php?'.$parameters.'">'.$label.'</a>';
	}

	/**
	 * Returns an HTML link for unlinking a content element. Unlinking means that the record still exists but
	 * is not connected to any other content element or page.
	 *
	 * @param	string		$label: The label
	 * @param	array		$unlinkPointer: Flexform pointer pointing to the element to be unlinked
	 * @param	boolean		$realDelete: If set, the record is not just unlinked but deleted!
	 * @return	string		HTML anchor tag containing the label and the unlink-link
	 * @access protected
	 */
	function link_unlink($label, $unlinkPointer, $realDelete=FALSE)	{

		$unlinkPointerString = rawurlencode($this->apiObj->flexform_getStringFromPointer ($unlinkPointer));

		if ($realDelete)	{
			return '<a href="index.php?' . $this->link_getParameters() . '&amp;deleteRecord=' . $unlinkPointerString . '" onclick="' . htmlspecialchars('return confirm(' . $GLOBALS['LANG']->JScharCode($GLOBALS['LANG']->getLL('deleteRecordMsg')) . ');') . '">' . $label . '</a>';
		} else {
			return '<a href="javascript:'.htmlspecialchars('if (confirm(' . $GLOBALS['LANG']->JScharCode($GLOBALS['LANG']->getLL('unlinkRecordMsg')) . '))') . 'sortable_unlinkRecord(\'' . $unlinkPointerString . '\');">' . $label . '</a>';
		}
	}

	/**
	 * Returns an HTML link for making a reference content element local to the page (copying it).
	 *
	 * @param	string		$label: The label
	 * @param	array		$makeLocalPointer: Flexform pointer pointing to the element which shall be copied
	 * @return	string		HTML anchor tag containing the label and the unlink-link
	 * @access protected
	 */
	function link_makeLocal($label, $makeLocalPointer)	{
		global $LANG;

		return '<a href="index.php?'.$this->link_getParameters().'&amp;makeLocalRecord='.rawurlencode($this->apiObj->flexform_getStringFromPointer($makeLocalPointer)).'" onclick="'.htmlspecialchars('return confirm('.$LANG->JScharCode($LANG->getLL('makeLocalMsg')).');').'">'.$label.'</a>';
	}

	/**
	 * Creates additional parameters which are used for linking to the current page while editing it
	 *
	 * @return	string		parameters
	 * @access public
	 */
	function link_getParameters()	{
		$output =
			'id='.$this->id.
			(is_array($this->altRoot) ? t3lib_div::implodeArrayForUrl('altRoot',$this->altRoot) : '') .
			($this->versionId ? '&amp;versionId='.rawurlencode($this->versionId) : '');
		return $output;
	}





	/*************************************************
	 *
	 * Processing and structure functions (protected)
	 *
	 *************************************************/

	/**
	 * Checks various GET / POST parameters for submitted commands and handles them accordingly.
	 * All commands will trigger a redirect by sending a location header after they work is done.
	 *
	 * Currently supported commands: 'createNewRecord', 'unlinkRecord', 'deleteRecord','pasteRecord',
	 * 'makeLocalRecord', 'localizeElement', 'createNewPageTranslation' and 'editPageLanguageOverlay'
	 *
	 * @return	void
	 * @access protected
	 */
	function handleIncomingCommands() {

		$possibleCommands = array ('createNewRecord', 'unlinkRecord', 'deleteRecord','pasteRecord', 'makeLocalRecord', 'localizeElement', 'createNewPageTranslation', 'editPageLanguageOverlay');

		foreach ($possibleCommands as $command) {
			if (($commandParameters = t3lib_div::_GP($command)) != '') {

				$redirectLocation = 'index.php?'.$this->link_getParameters();

				switch ($command) {

					case 'createNewRecord':
							// Historically "defVals" has been used for submitting the preset row data for the new element, so we still support it here:
						$defVals = t3lib_div::_GP('defVals');
						$newRow = is_array ($defVals['tt_content']) ? $defVals['tt_content'] : array();

							// Create new record and open it for editing
						$destinationPointer = $this->apiObj->flexform_getPointerFromString($commandParameters);
						$newUid = $this->apiObj->insertElement($destinationPointer, $newRow);
						// TODO If $newUid==0, than we could create new element. Need to handle it...
						$redirectLocation = $GLOBALS['BACK_PATH'].'alt_doc.php?edit[tt_content]['.$newUid.']=edit&returnUrl='.rawurlencode(t3lib_extMgm::extRelPath('templavoila').'mod1/index.php?'.$this->link_getParameters());
					break;

					case 'unlinkRecord':
						$unlinkDestinationPointer = $this->apiObj->flexform_getPointerFromString($commandParameters);
						$this->apiObj->unlinkElement($unlinkDestinationPointer);
					break;

					case 'deleteRecord':
						$deleteDestinationPointer = $this->apiObj->flexform_getPointerFromString($commandParameters);
						$this->apiObj->deleteElement($deleteDestinationPointer);
					break;

					case 'pasteRecord':
						$sourcePointer = $this->apiObj->flexform_getPointerFromString (t3lib_div::_GP('source'));
						$destinationPointer = $this->apiObj->flexform_getPointerFromString (t3lib_div::_GP('destination'));
						switch ($commandParameters) {
							case 'copy' :	$this->apiObj->copyElement ($sourcePointer, $destinationPointer); break;
							case 'copyref':	$this->apiObj->copyElement ($sourcePointer, $destinationPointer, FALSE); break;
							case 'cut':		$this->apiObj->moveElement ($sourcePointer, $destinationPointer); break;
							case 'ref':		list(,$uid) = explode(':', t3lib_div::_GP('source'));
											$this->apiObj->referenceElementByUid ($uid, $destinationPointer);
							break;

						}
					break;

					case 'makeLocalRecord':
						$sourcePointer = $this->apiObj->flexform_getPointerFromString ($commandParameters);
						$this->apiObj->copyElement ($sourcePointer, $sourcePointer);
						$this->apiObj->unlinkElement ($sourcePointer);
					break;

					case 'localizeElement':
						$sourcePointer = $this->apiObj->flexform_getPointerFromString (t3lib_div::_GP('source'));
						$this->apiObj->localizeElement ($sourcePointer, $commandParameters);
					break;

					case 'createNewPageTranslation':
							// Create parameters and finally run the classic page module for creating a new page translation
						$params = '&edit[pages_language_overlay]['.intval (t3lib_div::_GP('pid')).']=new&overrideVals[pages_language_overlay][sys_language_uid]='.intval($commandParameters);
						$returnUrl = '&returnUrl='.rawurlencode(t3lib_extMgm::extRelPath('templavoila').'mod1/index.php?'.$this->link_getParameters());
						$redirectLocation = $GLOBALS['BACK_PATH'].'alt_doc.php?'.$params.$returnUrl;
					break;

					case 'editPageLanguageOverlay':
							// Look for pages language overlay record for language:
						$sys_language_uid = intval($commandParameters);
						$params = '';
						if ($sys_language_uid != 0) {
							// Edit overlay record
							list($pLOrecord) = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
									'*',
									'pages_language_overlay',
									'pid='.intval($this->id).' AND sys_language_uid='.$sys_language_uid.
										t3lib_BEfunc::deleteClause('pages_language_overlay').
										t3lib_BEfunc::versioningPlaceholderClause('pages_language_overlay')
								);
							if ($pLOrecord) {
								t3lib_beFunc::workspaceOL('pages_language_overlay', $pLOrecord);
								if (is_array($pLOrecord))	{
									$params = '&edit[pages_language_overlay]['.$pLOrecord['uid'].']=edit';
								}
							}
						}
						else {
							// Edit default language (page properties)
							// No workspace overlay because we already on this page
							$params = '&edit[pages]['.intval($this->id).']=edit';
						}
						if ($params) {
							$returnUrl = '&returnUrl='.rawurlencode(t3lib_extMgm::extRelPath('templavoila').'mod1/index.php?'.$this->link_getParameters());
							$redirectLocation = $GLOBALS['BACK_PATH'].'alt_doc.php?'.$params.$returnUrl;	//.'&localizationMode=text';
						}
					break;
				}
			}
		}

		if (isset ($redirectLocation)) {
			header('Location: '.t3lib_div::locationHeaderUrl($redirectLocation));
		}
	}






	/***********************************************
	 *
	 * Miscelleaneous helper functions (protected)
	 *
	 ***********************************************/

	/**
	 * Returns an array of available languages (to use for FlexForms)
	 *
	 * @param	integer		$id: If zero, the query will select all sys_language records from root level. If set to another value, the query will select all sys_language records that has a pages_language_overlay record on that page (and is not hidden, unless you are admin user)
	 * @param	boolean		$onlyIsoCoded: If set, only languages which are paired with a static_info_table / static_language record will be returned.
	 * @param	boolean		$setDefault: If set, an array entry for a default language is set.
	 * @param	boolean		$setMulti: If set, an array entry for "multiple languages" is added (uid -1)
	 * @return	array
	 * @access protected
	 */
	function getAvailableLanguages($id=0, $onlyIsoCoded=true, $setDefault=true, $setMulti=false)	{
		global $LANG, $TYPO3_DB, $BE_USER, $TCA, $BACK_PATH;

		t3lib_div::loadTCA ('sys_language');
		$flagAbsPath = t3lib_div::getFileAbsFileName($TCA['sys_language']['columns']['flag']['config']['fileFolder']);
		$flagIconPath = $BACK_PATH.'../'.substr($flagAbsPath, strlen(PATH_site));

		$output = array();
		$excludeHidden = $BE_USER->isAdmin() ? '1=1' : 'sys_language.hidden=0';

		if ($id) {
			$excludeHidden .= ' AND pages_language_overlay.deleted=0';
			$res = $TYPO3_DB->exec_SELECTquery(
				'DISTINCT sys_language.*',
				'pages_language_overlay,sys_language',
				'pages_language_overlay.sys_language_uid=sys_language.uid AND pages_language_overlay.pid='.intval($id).' AND '.$excludeHidden,
				'',
				'sys_language.title'
			);
		} else {
			$res = $TYPO3_DB->exec_SELECTquery(
				'sys_language.*',
				'sys_language',
				$excludeHidden,
				'',
				'sys_language.title'
			);
		}

		if ($setDefault) {
			$output[0]=array(
				'uid' => 0,
				'title' => strlen ($this->modSharedTSconfig['properties']['defaultLanguageLabel']) ? $this->modSharedTSconfig['properties']['defaultLanguageLabel'] : $LANG->getLL('defaultLanguage'),
				'ISOcode' => 'DEF',
				'flagIcon' => strlen($this->modSharedTSconfig['properties']['defaultLanguageFlag']) && @is_file($flagAbsPath.$this->modSharedTSconfig['properties']['defaultLanguageFlag']) ? $flagIconPath.$this->modSharedTSconfig['properties']['defaultLanguageFlag'] : null,
			);
		}

		if ($setMulti) {
			$output[-1]=array(
				'uid' => -1,
				'title' => $LANG->getLL ('multipleLanguages'),
				'ISOcode' => 'DEF',
				'flagIcon' => $flagIconPath.'multi-language.gif',
			);
		}

		while(TRUE == ($row = $TYPO3_DB->sql_fetch_assoc($res)))	{
			t3lib_BEfunc::workspaceOL('sys_language', $row);
			$output[$row['uid']]=$row;

			if ($row['static_lang_isocode'])	{
				$staticLangRow = t3lib_BEfunc::getRecord('static_languages',$row['static_lang_isocode'],'lg_iso_2');
				if ($staticLangRow['lg_iso_2']) {
					$output[$row['uid']]['ISOcode'] = $staticLangRow['lg_iso_2'];
				}
			}
			if (strlen($row['flag'])) {
				$output[$row['uid']]['flagIcon'] = @is_file($flagAbsPath.$row['flag']) ? $flagIconPath.$row['flag'] : '';
			}

			if ($onlyIsoCoded && !$output[$row['uid']]['ISOcode']) unset($output[$row['uid']]);
		}

		return $output;
	}

	/**
	 * Returns an array of registered instantiated classes for a certain hook.
	 *
	 * @param	string		$hookName: Name of the hook
	 * @return	array		Array of object references
	 * @access protected
	 */
	function hooks_prepareObjectsArray ($hookName) {
		global $TYPO3_CONF_VARS;

		$hookObjectsArr = array();
		if (is_array ($TYPO3_CONF_VARS['EXTCONF']['templavoila']['mod1'][$hookName])) {
			foreach ($TYPO3_CONF_VARS['EXTCONF']['templavoila']['mod1'][$hookName] as $classRef) {
				$hookObjectsArr[] = &t3lib_div::getUserObj ($classRef);
			}
		}
		return $hookObjectsArr;
	}

	/**
	 * Checks if translation to alternative languages can be applied to this page.
	 *
	 * @return	boolean		<code>true</code> if alternative languages exist
	 */
	function alternativeLanguagesDefined() {
		return count($this->allAvailableLanguages) > 2;
	}

	/**
	 * Defines if an element is to be displayed in the TV page module (could be filtered out by language settings)
	 *
	 * @param	array		Sub element array
	 * @return	boolean		Display or not
	 */
	function displayElement($subElementArr)	{
			// Don't display when "selectedLanguage" is choosen
		$displayElement = !$this->MOD_SETTINGS['langDisplayMode'];
			// Set to true when current language is not an alteranative (in this case display all elements)
		$displayElement |= ($this->currentLanguageUid<=0);
			// When language of CE is ALL or default display it.
		$displayElement |= ($subElementArr['el']['sys_language_uid']<=0);
			// Display elements which have their language set to the currently displayed language.
		$displayElement |= ($this->currentLanguageUid==$subElementArr['el']['sys_language_uid']);

		return $displayElement;
	}

	/**
	 * Returns label, localized and converted to current charset. Label must be from FlexForm (= always in UTF-8).
	 *
	 * @param	string	$label	Label
	 * @param	boolean	$hsc	<code>true</code> if HSC required
	 * @return	string	Converted label
	 */
	function localizedFFLabel($label, $hsc) {
		global	$LANG, $TYPO3_CONF_VARS;

		$charset = $LANG->origCharSet;
		if ($LANG->origCharSet != $TYPO3_CONF_VARS['BE']['forceCharset']) {
			$LANG->origCharSet = $TYPO3_CONF_VARS['BE']['forceCharset'];
		}
		$result = $LANG->hscAndCharConv($label, $hsc);
		$LANG->origCharSet = $charset;
		return $result;
	}

	function getRecordStatHookValue($table,$id)	{
			// Call stats information hook
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['GLOBAL']['recStatInfoHooks']))	{
			$stat='';
			$_params = array($table,$id);
			foreach ($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['GLOBAL']['recStatInfoHooks'] as $_funcRef)	{
				$stat.=t3lib_div::callUserFunction($_funcRef,$_params,$this);
			}
			return $stat;
		}
	}
/*
	function hasFCEAccess($row) {
		$params = array(
			'table' => 'tt_content',
			'row' => $row
		);
		$ref = null;
		return t3lib_div::callUserFunction('EXT:templavoila/class.tx_templavoila_access.php:&tx_templavoila_access->recordEditAccessInternals', $params, $ref);
	}
*/
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/templavoila/mod1/index.php'])    {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/templavoila/mod1/index.php']);
}

	// Make instance:
$SOBE = t3lib_div::makeInstance('tx_templavoila_module1');
$SOBE->init();
$SOBE->main();
$SOBE->printContent();
?>