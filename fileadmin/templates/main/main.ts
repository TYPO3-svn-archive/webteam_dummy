################################################### RealUrl ########################################################
# set baseURL to your Homepage root page
config.baseURL = http://localhost/typo3/000_webteamDummy/

##################################################### MENU #########################################################
# Menu: default nested ul/li list
lib.mainMenu < menus.nested_ul

##################################################### Header #######################################################
lib.header = TEXT
lib.header.value = header

lib.footer = TEXT
lib.footer.value = footer

lib.contentWrap = <div class="content contentMain" id="contentMain"><!--TYPO3SEARCH_begin--> | <!--TYPO3SEARCH_end--></div>
lib.contentAbove < menus.breadcrum
lib.contentAbove.wrap = <div class="breadcrum">|</div>