page.headerData.50 = TEXT
page.headerData.50.value = <link rel="stylesheet" href="fileadmin/templates/tt_news/css/tt_news.css" type="text/css" media="screen" />

plugin.tt_news {
	# IDs of the folders containing the News
	pid_list = 24
	# define your own Template FILE
	templateFile = fileadmin/templates/tt_news/index.html
	
  displayLatest.image {
    file.maxW = 100
    file.maxH	= 100
	}
}