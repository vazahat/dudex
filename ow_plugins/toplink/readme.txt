TopLink Plugin

This plugin is developed in order to let admin put his own link on the top console area, beside the mailbox, notification, profile etc.

On the setting page, admin can
 - add a link
 - specify link icon OR upload the a new icon
 - specify where the link will load, a new window or using the current window.
 - update/remove saved links


1.4 change log
- update reset form action, to just use javascript to reset the form, native form reset function doesnt work
- add some style to update icon link
- add required for the important fields
- padding(white space) are removed if there is no icon submitted
- if submitted icon text field begin with "/", the file existance will be checked first before saving

1.4.1 change log
- added file exist checks before saving into db, just to make sure that the file is uploaded into the location or not.

1.4.2 change log
- update "check for uploaded icon file existance"
- update ow_userfiles/plugins/toplink folder mode to 0755 upon activation

1.4.4 change log
- change my manual coding to using oxwall supplied functions and methods for image upload

1.6 change log
- visibility options added. admin can check type(s) of user for a link to be visible to. if none is checked, then  the link will be set to only visible to admin.

1.7 change log
- added the capability to add children to menu item

2.2 change log
- bux fix: unable to show icon along side label for drop down menu
- bux fix: update failed to remove language folder, redirecting user to 500.html page
- child menu is removed too when deleting parent menu