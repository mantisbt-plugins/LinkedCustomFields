# Linked Custom Fields plugin for MantisBT

Copyright (c) 2011 Robert Munteanu - robert@lmn.ro

Released under the [GNU General Public License version 2](http://opensource.org/licenses/GPL-2.0)

See the [Changelog](https://github.com/mantisbt-plugins/LinkedCustomFields/blob/master/CHANGELOG.md).


## Description

The LinkedCustomFields plugin allows you to link values between two custom
fields.


## Requirements

The plugin requires
- [MantisBT](https://mantisbt.org/) version 1.2.6 or later
- [jQuery plugin](https://github.com/mantisbt-plugins/jquery) version 1.8 or later


## Installation

1. Download or clone a copy of the 
   [plugin's code](https://github.com/mantisbt-plugins/LinkedCustomFields).
2. Copy the plugin (the `LinkedCustomFields/` directory) into your Mantis
   installation's `plugins/` directory, or create a link to it.
3. While logged into your Mantis installation as an administrator, go to
   *Manage ➔ Manage Plugins*.
4. In the *Available Plugins* list, you'll find the *LinkedCustomFields* plugin;
   click the **Install** link.


## Usage

### Configuring Linked Custom Field

The plugin's configuration page is accessed from the _Manage_ menu, where a new 
item named _Configure custom field links_ is present. 

**Note:** the plugin only supports ENUMERATION and MULTI_SELECT custom field types.

![Screenshot of LinkedCustomFields plugin's Configure custom field links page](doc/linked-custom-fields-overview.png)

1. Pick one of the fields in list, then click _Edit_.
2. In the _Configure custom field links_ section, 
   select the target field in the _Linked to_ list.
3. In the _Mapping_ section, each value from the source field can be associated
   to any number of values from the target field.  
   **Note:** Selecting no values means that they are all valid.

![Screenshot of LinkedCustomFields plugin's Configure custom field links edit page](doc/linked-custom-fields-edit.png)


### Using LinkedCustomFields

When editing an issue in a project where the linked custom fields are available,
once a value from the source custom field has been selected, only the configured
corresponding values will be available from the target custom field.  


## Support

The following support channels are available if you wish to file a
bug report or have questions related to use and installation:

  - Mantis Bug Tracker, in the 
    [Plugin - LinkedCustomFields](httpS://mantisbt.org/bugs/search.php?project_id=16&sticky_issues=1&sortby=last_updated&dir=DESC) project
  - MantisBT [Gitter chat room](https://gitter.im/mantisbt/mantisbt)
