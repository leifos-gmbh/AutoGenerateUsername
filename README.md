# AutoGenerateUsername
This ILIAS plugin provides an event hook to automatically create an username after registration or LDAP authentication.
The username will be consisting of informations like firstname, lastname, email, a sequential number etc.

**Minimum ILIAS Version:**
10.0

**Maximum ILIAS Version:**
10.99

**Responsible Developer:**
Christoph Ludolf ludolf@leifos.de

**Supported Languages:**
German, English

**Bug Tracker:**
[ILIAS MantisBT](http://www.ilias.de/mantis/search.php?project_id=3&category=AutoGenerateUsername)

## Quick Installation Guide
Navigate to your ILIAS root directory and execute the following commands.

Create the Plugin parent directory.
```shell
mkdir -p public/Customizing/global/plugins/Services/EventHandling/EventHook/
```
Navigate to the directory.
```shell
cd public/Customizing/global/plugins/Services/EventHandling/EventHook/
```
Download the plugin.
```shell
git clone -b release_10 https://github.com/leifos-gmbh/AutoGenerateUsername.git AutoGenerateUsername
```
Afterwards execute composer to build the classmaps.
Finally navigate activate and configure the plugin in the ILIAS Administration.

### Username Field Patch
The username which is set by the user on the registration screen will be overwritten by this plugin.
This may lead to confusion if the user wants to log in with his chosen username and doesn't use the one provided by this plugin via new account mail.
For a good experience a patch can be applied to remove the username input field from the registration form.
Make sure you checked "Registration" on the username configuration context or new created users will have no username.

Navigate to the ILIAS root directory and execute the following command to apply the patch:

`````` shell
	patch -l -p1 < public/Customizing/global/plugins/Services/EventHandling/EventHook/AutoGenerateUsername/patches/10_x_xagu_hide_username_patch.diff
``````

Navigate to the ILIAS root directory and execute the following command to remove the patch:
`````` shell
	patch -R -p1 < public/Customizing/global/plugins/Services/EventHandling/EventHook/AutoGenerateUsername/patches/10_x_xagu_hide_username_patch.diff
```````

### New in plugin version "5.3.1"
- New option to auto generate names for the current ILIAS users. When the user update his own profile the plugin will update the "login" name.
