2013/05/08:
whole site:
  - define a graphical design
  - add missing/uncoded functions
  
viewer.php:
  - Add the counting of hashtag,
  - Add a cloud of tags
  - Add stats from ScreenName
  - Solve the problem during url checking
  - upgrade the download of tweets and don't try to download more than necessary
  - upgrade the download of tweets and implement multi-downloads (threads, ...)

current.php
  - Solve the problem during url checking
  
admin.php
  - before add a ScreenName, check if:
		- it exists
		- it's not protected
			- if it's protected, propose accounts to link to
  - before add an account, check username and credentials (don't store in clear, ...)