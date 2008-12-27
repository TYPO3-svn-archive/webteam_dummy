################################################### RealUrl ########################################################
# set baseURL to your Homepage root page
config.baseURL = http://localhost/typo3/webteam_dummy/
##################################################### MENU #########################################################
# Menu: default nested ul/li list
lib.mainMenu < menus.nested_ul

##################################################### Header #######################################################
lib.header = TEXT
lib.header.value = header

lib.footer = TEXT
lib.footer.value = footer

lib.contentAbove < menus.breadcrum
lib.contentAbove.wrap = <div class="breadcrum">|</div>