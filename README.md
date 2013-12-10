Group Membership Extension
======
(ca.freeform.groupmembership)

Provides a panel for adding additional members to a membership at the time of membership purchase. Primarily designed for family memberships.

Setup
---------

Install and activate this module.

You will find the general settings at "Administer >> CiviMember >> Group Membership Settings" or go to
http://{yoursite.corg}/civicrm/groupmembership/settings?reset=1.

You need to choose:

1. The membership type to apply this extension to
2. The profile you want to use for adding additional family members
3. The membership page(s) to show the panel on

The membership type needs to have a relationship type selected on its settings page. The profile must:

* Not be reserved
* Include an email address field
* Include an Internal Contact ID field which is set to view only.

The settings page will warn you if your profile does not meet these requirements.

Usage
---------
When you have a membership type with that has inheritance by relationship activated you have the concept of Primary
member and Secondary members. The Primary member is the one that purchased the membership and is also the one who
should renew. Which member we are dealing with can only be identified to CiviCRM when they are logged in (so that we
know the contact id) or by looking them up via email address if they are anonymous.

The extension will cause a "Update family members" panel to be displayed on:
* The membership contribution page if the Primary member is renewing while logged in
* As soon as a family type membership is chosen from the membership options for a new membership being purchased as a logged in user.
* The thank you page for a membership purchase if either the contact is initially logged in or if the contribution page includes a create account option.

The extension will cause a "Warning" panel to be displayed on:
* The membership contribution page if the Secondary member is renewing while logged in
* As soon as a family type membership is chosen from the membership options for a new membership being purchased as a logged in user.
