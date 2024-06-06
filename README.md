# AutoGenerateUsername

This ILIAS plugin provides an event hook to automatically create an username after registration or LDAP authentication.
The username will be consisting of informations like firstname, lastname, email, a sequential number etc.

**Minimum ILIAS Version:**
9.0

**Maximum ILIAS Version:**
9.99

**Responsible Developer:**
Christoph Ludolf ludolf@leifos.de

**Supported Languages:**
German, English

**Bug Tracker:**
[ILIAS MantisBT](http://www.ilias.de/mantis/search.php?project_id=3&category=AutoGenerateUsername)

### Quick Installation Guide
Get the plugin from github:
```shell
cd <ILIAS-ROOT>
mkdir -p Customizing/global/plugins/Services/EventHandling/EventHook/
cd Customizing/global/plugins/Services/EventHandling/EventHook/
git clone -b release_9 https://github.com/leifos-gmbh/AutoGenerateUsername.git AutoGenerateUsername
cd <ILIAS-ROOT>
# run composer to update the classmap
```
Finally navigate to the plugin menu in the Administration area and install as well as activate the AutoGenerateUsername-plugin.

### Configuration
In the plugin menu select to configure the AutoGenerateUsername plugin.

### Patch Installation

For a good experience you can apply a patch to get rid of the username field on the registration screen. This is useful because the username which is set by the user on the registration screen will be overwritten by this plugin. This can be confusing if the user wants to log in with his chosen username and doesn't use the one provided by this plugin via new account mail.
Make sure you checked "Registration" on the username configuration context or new created users will have no username.
Run this on your commandline top apply this patch:

`````` shell
	cd <ILIAS_directory>
	patch -l -p1 < Customizing/global/plugins/Services/EventHandling/EventHook/AutoGenerateUsername/patches/9_x_xagu_hide_username_patch.diff
``````

To remove this patch run :

`````` shell
	cd <ILIAS_directory>
`	patch -R -p1 < Customizing/global/plugins/Services/EventHandling/EventHook/AutoGenerateUsername/patches/9_x_xagu_hide_username_patch.diff
```````

### New in plugin version "5.3.1"

- New option to auto generate names for the current ILIAS users. When the user update his own profile the plugin will update the "login" name.
