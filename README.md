# Tag Driver

![Screenshot](/images/screenshot.png)

## Usage

Tag Driver is a CiviCRM Utility for accomplishing tasks in bulk by adding a tag to one or more contact records. To use, search for a contact or a group of contacts, and add a Tag Driver tag to them. When the next Tag Driver job runs, the associated actions will be performed automagically. Actions that are currently supported are:

* Create a CMS user account: Adding the "Tag Driver: Create CMS Account" tag to a contact record will create a CMS user account for that contact on the next run of the Tag Driver job. Note: Contacts that have had a CMS user account created for them by the Tag Driver extension will have the "Tag Driver: User Account" tag assigned to them.
* Reset CMS Password: Adding the "Tag Driver: Reset CMS Password" will have a password reset email sent to the contact automatically on the next Tag Driver job run.

Configure the extension by navigating to [https://www.example.org/civicrm/tagdriver](https://www.example.org/civicrm/tagdriver) and setting preferences for a tag that will be used to break ties if multiple contacts have the same email address, and the pattern to be used for creating CMS user account names.

## Requirements

* PHP v5.4+
* CiviCRM v4.7+

## Installation

This extension has not yet been published for in-app installation. [General extension installation instructions](https://docs.civicrm.org/sysadmin/en/latest/customize/extensions/#installing-a-new-extension) are available in the CiviCRM System Administrator Guide.

## License

The extension is licensed under [AGPL-3.0](LICENSE.txt).
