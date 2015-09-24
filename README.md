# INSTALLATION INSTRUCTIONS
(You need at least ILIAS 5.0.0)

******************************************************************************

## Plugin Installation

1. Copy the content of this folder in <ILIAS_directory>/Customizing/global/plugins/Services/EventHandling/EventHook/AutoGenerateUsername

2. Access to ILIAS and go to the administration page.

3. Select "Plugins" in the menu on the right.

5. Look for the AutoGenerateUsername plugin in the table and hit the "Action" button and select "Update".

6. When ILIAS update the plugin, hit the "Action" button and select "Activate" that will appear instead of the "Update" link.

7. Hit the "Action" button and select "Refresh Languages" to update the language-files.

8. Now, you can visit the username configuration at "Action" and "Configure"


******************************************************************************

**Warning:** Please test this Plugin carefully if you use ILIAS-Version 5.0.4

******************************************************************************

## Patch Installation

For a good experience you can apply a patch to get rid of the username field on the registration screen. This is useful because the username which is set by the user on the registration screen will be overwritten by this plugin. This can be confusing if the user wants to log in with his chosen username and doesn't use the one provided by this plugin via new account mail.
Make sure you checked "Registration" on the username configuration context or new created users will have no username.
Run this on your commandline top apply this patch:

	cd <ILIAS_directory>
	patch -p0 < Customizing/globalplugins/Services/EventHandling/EventHook/AutoGenerateUsername/patches/5_0_xagu_hide_username_patch.diff

To remove this patch run :

	cd <ILIAS_directory>
	patch -R -p0 < Customizing/global/plugins/Services/EventHandling/EventHook/AutoGenerateUsername/patches/5_0_xagu_hide_username_patch.diff
