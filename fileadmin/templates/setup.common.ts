#################################################### Common ########################################################
# disable cache if any typo3 user is logged in
[globalVar = TSFE:beUserLogin > 0]
	config.no_cache = 1
[global]

config {
	spamProtectEmailAddresses = -2
	removeDefaultJS = external
}

################################################### RealUrl ########################################################
config {
	simulateStaticDocuments = 0
	tx_realurl_enable = 1
	prefixLocalAnchors = all
}

################################################# Templa Voila #####################################################
# creates default Page Type rendered with templavoila
page = PAGE
page.typeNum = 0
page.10 = USER
page.10.userFunc = tx_templavoila_pi1->main_page

# set the Wrap if you use the "webteam_dummy" layout
lib.contentWrap = <div class="content contentMain" id="contentMain"><!--TYPO3SEARCH_begin--> | <!--TYPO3SEARCH_end--></div>

################################################ XHTML / LOG #######################################################
# sets default config for nice xhtml
config {
	doctype = xhtml_strict
	xmlprologue = none
	xhtml_cleaning = all
	disablePrefixComment = 1
}

################################################ Default Header ####################################################
# includes keywords and description into header if available
page.meta.keywords.field = keywords
page.meta.description.field = description

################################################### Language #######################################################
# values for languages in GERMAN
config {
	# only append the language value if not the default language
	linkVars = L(1)
	# prevent creation of double linkVars www.page.com/index.php?id=12&L=1&L=1 
	# is just a cosmetical fix
	uniqueLinkVars = 1
	sys_language_overlay = content_fallback
	language = de
	sys_language_uid = 0
	htmlTag_setParams = xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de"
}

# override some language states if english is selected
[globalVar = GP:L = 1]
config {
	language = en
	sys_language_uid = 1
	htmlTag_setParams = xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"
	locale_all = en_US.UTF-8
}
[global]

##################################################### MENU #########################################################
# Menu: default nested ul/li list
# <ul>
#   <li> Item 1 </li>
#   <li> Item 2 <ul>
#     <li> Item 2.1 </li>
#     <li> Item 2.2 </li>
#   </ul> </li>
#   <li> Item 3 </li>
# </ul>
   
menus.nested_ul = HMENU
menus.nested_ul {
	1 = TMENU
	1 {
		wrap = <ul>|</ul>
		noBlur = 1
		NO.wrapItemAndSub (
		  <li>|</li>
		)
		NO.ATagTitle.field = abstract // description // title
				
		ACT < .NO
		ACT = 1
		ACT.ATagParams = class="act"
		
		CUR < .NO
		CUR = 1
		CUR.ATagParams = class="cur"
		
		IFSUB < .NO
		IFSUB = 1
		IFSUB.ATagParams.wrap = class="ifsub"
		
		ACTIFSUB < .NO
		ACTIFSUB = 1
		ACTIFSUB.ATagParams.wrap = class="act ifsub"
		
		CURIFSUB < .NO
		CURIFSUB = 1
		CURIFSUB.ATagParams.wrap = class="cur ifsub"		
	}
	
	2 < .1
	3 < .1
}

################################################ Footer Menu #######################################################
# a common footer menu, looks like:   Impressum  |  Sitemap  |  AGB  |  Karriere
menus.footer = HMENU
menus.footer {
	special = directory
	# set to the footer page
	# special.value = 129
	1 = TMENU
	1 {
		noBlur = 1
		NO.allWrap = | &nbsp; &#124; &nbsp; |*||*| |
		NO.stdWrap.htmlSpecialChars = 1
	}
}

############################################### Breadcrum Menu #####################################################
# a common breadcrum menu, looks like:     home > Produkte > Internet > mein tolles Internet Product
# each item will be cropped after 40 chars; crops only after words
menus.breadcrum = HMENU
menus.breadcrum {
	special = rootline
	1 = TMENU
	1 {
		noBlur = 1
		NO.allWrap = | &thinsp; &gt; &thinsp; |*||*| |
		NO.stdWrap.htmlSpecialChars = 1
		NO.stdWrap.crop = 40 | ... | 1

		CUR < .NO
		CUR = 1
		CUR.ATagParams = class="cur"		
	}
}

############################################### FE DISPLAY #########################################################
# no Anchors in the FE
tt_content.stdWrap.dataWrap >

# DO NOT AUTOLINK EVERY STRING THAT STARTS WITH HTTP OR MAILTO
# such strings in the content are assumed to be intentional
# lib.parseFunc_RTE.makelinks >
# lib.parseFunc_RTE.externalBlocks.ul.stdWrap.parseFunc.makelinks = 0
# lib.parseFunc_RTE.externalBlocks.ol.stdWrap.parseFunc.makelinks = 0

# DEFINE DEFAULT HEADER
# lib.stdheader.10.1.fontTag = <h2>|</h2>

# NO csc-header
lib.stdheader.stdWrap.dataWrap >

# We want absolute no target attribute as it is not XHTML-strict
# compliant and I really don't like new windows (_blank target) !
lib.parseFunc.tags.link.typolink.target >
PAGE_TARGET =
styles.content.links.extTarget =
styles.content.links.target =
content.pageFrameObj =
styles.content.mailform.target =
styles.content.searchresult.resultTarget =
styles.content.searchresult.target =