################################################### Language #######################################################
# values for languages in ENGLISH
config {
	language = en
	htmlTag_setParams = xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"	
}

# override some language states if GERMAN is selected
[globalVar = GP:L = 1]
config {
	language = de
	htmlTag_setParams = xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de"
}
[global]
