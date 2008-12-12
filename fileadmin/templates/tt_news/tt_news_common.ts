################################################# NEWS #############################################################
# Clear the code field
plugin.tt_news.code >
plugin.tt_news.code = LATEST

# disable indexing of the LATEST view
config.index_enable = 0

[globalVar = GP:tx_ttnews|swords = ]
# Do nothing (reverse hack)
[else]
# Set code to SEARCH if swords (search is done) exists
plugin.tt_news.code = SEARCH
[end]

[globalVar = GP:tx_ttnews|tt_news > 0]
# Set code to SINGLE, if the GETvar tx_ttnews[tt_news] exists
plugin.tt_news.code = SINGLE
# Enable indexing of the SINGLE view
config.index_enable = 1
[global]

plugin.tt_news {
	displayLatest.title_stdWrap.htmlSpecialChars = 1
	displaySingle {
		title_stdWrap.htmlSpecialChars = 1
		subheader_stdWrap.wrap >
	}
	
	dontUseBackPid = 1
	useHRDates = 1

	useMultiPageSingleView = 1
	pageBreakToken = <break>
	# maxWordsInSingleView = 300
	# useParagraphAsPagebreak = 1
	subheaderOnAllSViewPages = 0
	appendSViewPBtoContent = 0
	
	# set limits
	limit = 10
	latestLimit = 10
	
	displaySingle.age_stdWrap.age = Minuten | Stunden | Tag(e) | Monat(e) | Jahr(e)
	displaySingle.date_stdWrap.strftime = %A, %d. %B. %Y
	displayList.age_stdWrap.age = Minuten | Stunden | Tag(e) | Monat(e) | Jahr(e)
	displayList.date_stdWrap.strftime = %d.%m.%Y
	
	# change the more link to whatever you want (in every language)
	_LOCAL_LANG.de {
		more = [weiterlesen...]
	}
	_LOCAL_LANG.default {
		more = [more...]
	}	
	
}

# override some language states if english is selected
[globalVar = GP:L = 1]
plugin.tt_news {
	displaySingle.age_stdWrap.age = 1
	displaySingle.date_stdWrap.strftime = %d.%m.%y
	displayList.age_stdWrap.age = 1
	displayList.date_stdWrap.strftime = %A %d. of %B %Y
}
[global]