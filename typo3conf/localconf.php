<?php
$TYPO3_CONF_VARS['SYS']['sitename'] = 'webteam_dummy';

	// Default password is "joh316" :
$TYPO3_CONF_VARS['BE']['installToolPassword'] = 'bacb98acf97e0b6112b1d1b650b84971';

$TYPO3_CONF_VARS['EXT']['extList'] = 'tsconfig_help,context_help,extra_page_cm_options,impexp,sys_note,tstemplate,tstemplate_ceditor,tstemplate_info,tstemplate_objbrowser,tstemplate_analyzer,func_wizards,wizard_crpages,wizard_sortpages,lowlevel,install,belog,beuser,aboutmodules,setup,taskcenter,info_pagetsconfig,viewpage,rtehtmlarea,css_styled_content,t3skin';

$typo_db_extTableDef_script = 'extTables.php';

/****************     default values for webteam_dummy    ********************************/
	$TYPO3_CONF_VARS['SYS']['curlUse'] = '1';
	
	// activates pageNotFound Handling so bots clear deleted pages from the search index
	$TYPO3_CONF_VARS['FE']['pageNotFound_handling'] = '1';
	$TYPO3_CONF_VARS['FE']['pageNotFound_handling_statheader'] = 
	   'HTTP/1.1 404 Not Found';

	// set session timeout to 1h, so this popup won't appear so often
	$TYPO3_CONF_VARS['BE']['sessionTimeout'] = 3600;
	
	// always use utf-8
	$TYPO3_CONF_VARS['BE']['forceCharset'] = 'utf-8';
	$TYPO3_CONF_VARS['SYS']['setDBinit'] = 'SET NAMES utf8;';
	$TYPO3_CONF_VARS['SYS']['UTF8filesystem'] = 'utf-8';
	$TYPO3_CONF_VARS['SYS']['t3lib_cs_convMethod'] = 'mbstring';
	$TYPO3_CONF_VARS['SYS']['t3lib_cs_utils'] = 'mbstring';

	// allow editing of *.ts files in the typo3 BE
	$TYPO3_CONF_VARS['SYS']['textfile_ext'] = 'txt,html,htm,css,inc,tmpl,js,sql,ts';
	
/****************     END default values for webteam_dummy    ********************************/

## INSTALL SCRIPT EDIT POINT TOKEN - all lines after this points may be changed by the install script!

$TYPO3_CONF_VARS['SYS']['encryptionKey'] = '13688fc48de7e243c564685dc4aa0938';	//  Modified or inserted by TYPO3 Install Tool.
$TYPO3_CONF_VARS['SYS']['compat_version'] = '4.2';	//  Modified or inserted by TYPO3 Install Tool.
// Updated by TYPO3 Install Tool 05-12-08 17:43:43
$TYPO3_CONF_VARS['EXT']['extList'] = 'css_styled_content,tsconfig_help,context_help,extra_page_cm_options,impexp,sys_note,tstemplate,tstemplate_ceditor,tstemplate_info,tstemplate_objbrowser,tstemplate_analyzer,func_wizards,wizard_crpages,wizard_sortpages,lowlevel,install,belog,beuser,aboutmodules,setup,taskcenter,info_pagetsconfig,viewpage,t3skin,static_info_tables,realurl,tinymce_rte,tt_news,recycler,templavoila';	// Modified or inserted by TYPO3 Extension Manager. 
$TYPO3_CONF_VARS['EXT']['extConf']['templavoila'] = 'a:1:{s:7:"enable.";a:1:{s:13:"oldPageModule";s:1:"0";}}';	//  Modified or inserted by TYPO3 Extension Manager.
$TYPO3_CONF_VARS['EXT']['extConf']['realurl'] = 'a:4:{s:10:"configFile";s:26:"typo3conf/realurl_conf.php";s:14:"enableAutoConf";s:1:"0";s:14:"autoConfFormat";s:1:"0";s:12:"enableDevLog";s:1:"0";}';	//  Modified or inserted by TYPO3 Extension Manager.
$TYPO3_CONF_VARS['EXT']['extConf']['tt_news'] = 'a:15:{s:13:"useStoragePid";s:1:"1";s:13:"noTabDividers";s:1:"0";s:25:"l10n_mode_prefixLangTitle";s:1:"1";s:22:"l10n_mode_imageExclude";s:1:"1";s:20:"hideNewLocalizations";s:1:"0";s:13:"prependAtCopy";s:1:"1";s:17:"requireCategories";s:1:"0";s:5:"label";s:5:"title";s:9:"label_alt";s:0:"";s:10:"label_alt2";s:0:"";s:15:"label_alt_force";s:1:"0";s:11:"treeOrderBy";s:3:"uid";s:21:"categorySelectedWidth";s:1:"0";s:17:"categoryTreeWidth";s:1:"0";s:18:"categoryTreeHeigth";s:1:"5";}';	//  Modified or inserted by TYPO3 Extension Manager.
// Updated by TYPO3 Extension Manager 09-12-08 09:56:49
?>