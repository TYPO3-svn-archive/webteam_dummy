################################################### RealUrl ########################################################
# set baseURL to your Homepage root page
config.baseURL = http://carla.webteam.at:88/typo3/webteam_dummy/

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

##################################################### NEWS #######################################################
[globalVar = TSFE:id = 23]
	<INCLUDE_TYPOSCRIPT: source="FILE: fileadmin/templates/common_tt_news.ts">
	
	page.headerData.50 = TEXT
	page.headerData.50.value = <link rel="stylesheet" href="fileadmin/templates/tt_news/css/tt_news.css" type="text/css" media="screen" />

	plugin.tt_news {
		# IDs of the folders containing the News
		pid_list = 24
		singlePid = 23
		backPid = 23
		archiveTypoLink.parameter = 23
		# define your own Template FILE
		templateFile = fileadmin/templates/tt_news/index.html
		
	  displayLatest.image {
	    file.maxW = 100
	    file.maxH	= 100
		}
	}

[global]
