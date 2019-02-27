
CONTENTS OF THIS FILE
---------------------

 * About Mega Menu for Drupal 8 module
 * Requirements
 * Features
 * Important notes and limitations
 * Installation
 * Updating
 * Uninstallation
 * Demos
 * Configuration
 * How to create and edit the entries
 * Credits
 * License

ABOUT MEGA MENU FOR DRUPAL 8 MODULE
-----------------------------------

Mega Menu is a module that is built on top of the Drupal 8 core menu system and inherits all of the core menu system’s
capabilities, allowing you to build flexible and beautiful menus.

You'll get full control of Drupal 8 menu so it will be very easy to create gorgeous menu layouts.

Mega Menu for Drupal 8 allows you to add any custom content, including Drupal blocks and HTML code, to the menu.

The module contains several predefined skins and allows you to extend it with custom skins and to customize with CSS in
order to integrate with your theme and to provide better user experience.

Built-in responsive styles and support for touch screens provide logic for collapsing menus at desired viewport width.

REQUIREMENTS
------------

Required modules and libraries:

- Drupal >=8.3
- FontAwesome library v4.2.0: http://fontawesome.io/

FEATURES
--------

- Integration with core Drupal 8 menu system
- Wide variety of menu item types
- Easy integration with your themes
- Extend look and feel with custom skins and CSS styles
- Built-in responsive styles and support for touch screens
- Fully accessible for screen-readers
- Add any custom content, including Drupal blocks and HTML code
- Use images and icons in menu items
- Smart handling of menu layouts
- Clean BEM/SMACSS CSS files and styles structure
- Built-in right-to-left support

IMPORTANT NOTES AND LIMITATIONS
-------------------------------

- Mega Menu for Drupal 8 needs to be styled manually to adopt the look and feel of custom Drupal themes.
- You'll not be able to use twig templates for changing menus with enabled mega menu because the module uses custom twig
  function with very complex logic for generating the menu's output. Please read the section “Developers notes” below for
  detailed information.
- Because module’s JavaScript logic uses menu’s ID property, the same menu cannot appear more than once on the same page.
- The Mega Menu module extends ServiceProvider class in order to handle saving third party options for menu items. If you
  use some other module that extends the same class, it’s possible that the saving third party settings feature of one of
  them will not work.
- Because of the way how Drupal 8 handles hooks, it’s not possible to define custom skins in themes, but in modules only.
- There’s a bug in core Drupal system that prevents uploading images in module-defined menu items (e.g. Home item in
  Main navigation or Login/Logout in User account menu), so we disabled the uploading image logic for these items until
  the issue is fixed. For more information visit https://www.drupal.org/node/2898335

INSTALLATION
------------

1) Copy /libraries/fontawesome directory to the libraries directory, e.g. /libraries (create it if it doesn’t exist).
2) Copy /modules/megamenu directory to the module directory, e.g. /modules.
3) Enable the module "Mega Menu" on the admin modules page.

UPDATING
--------

1) Back up your database and files
2) Put your site into Maintenance mode (Administration > Configuration > Development > Maintenance mode)
3) Replace the module code with the updated version
4) Run update.php
5) Turn off Maintenance mode

UNINSTALLATION
--------------

The module can be uninstalled at any time by using the Drupal Administration panel. Follow the steps described below to
remove the module from your Drupal website.

1) Firstly, go to the Administration > Manage > Extend
2) Go to the Uninstall tab of the Modules page. The Mega Menu module should be available to uninstall.
3) Select the Uninstall checkbox next to the module name and click the Uninstall button. Mega Menu for Drupal 8 will now
be removed from your site.

Note:
Please note that the uninstallation process removes the module along with all its database tables and settings. It does
not, however, remove the module files. If you want to completely remove the files from your server, you will need to
delete them manually from the /modules/megamenu directory.
Drupal 8 doesn't have the "Disable" logic anymore, but "Uninstall" feature only. So there's no way just to disable a
module without losing its saved data.

DEMOS
-----

Online demo
-----------

The online demo of the module is available at: http://demo.templago.com/d8/megamenu

Offline demo (provided with the module)
---------------------------------------

We provided fully working demo environment, just as you can see it on our demo site, so you’re able to research how we
created all those layouts and customizations. Below are instructions for recreation demo environment on your own machine.

1) Create database using your preferred database client and name it e.g. demo_d8_megamenu
2) Import into the database the demo_d8_megamenu.sql.zip file we provided together with the module, located in Demo folder.
3) Unzip the file megamenu.zip we provided together with the module, located in Demo folder. Please note that on Linux
environment you need to configure files and folders permissions, too. Read Drupal documentation about valid system
permissions for files and folders.
4) Open the file /sites/default/settings.php and update information related to database connection, e.g. database name,
username, password…
5) Open the demo site and login with username: admin / password: admin
6) Clear cache by navigating to Administration > Configuration > Development > Clear all caches
7) Go to Administration > Structure > Menus > Edit Main navigation. Here you can see how the menu is configured and how
the menu items are organized.
8) Click the “Edit” operation for any of the menu items in order to see how it’s configured.

CONFIGURATION
-------------

Note:
Some options are marked as experimental although they are fully working. All of these options are related to generating
inline CSS styles. However, Drupal 8 doesn’t allow inline CSS styles to be inserted into the <head> tag of the page in
desired order, so these options theoretically could affect performances on mobile sites. For more information about the
issue visit https://www.drupal.org/node/2391025.

Configure menu levels in a menu block
-------------------------------------

If you want to enable mega menu for a menu added via block, you need to configure the menu levels option in the block to
display desired number of levels to display.

1) Go to Administration > Structure > Block layout
2) Click the Configure link for a menu block you want to configure, e.g. Main Navigation
3) Configure the menu levels options. For example, you’ll probably want to set Number of levels to display option to
Unlimited in order to display all levels.
4) Save the settings.

Configure Mega Menu global options
----------------------------------

1) Go to Administration > Configuration > User interface > Mega Menu in order to configure global Mega Menu options
2) Configure Responsive & Mobile options
3) Configure Script Configuration options
4) Configure Custom Styles options
5) Configure Miscellaneous options

HOW TO CREATE AND EDIT THE ENTRIES
----------------------------------

In order to display mega menu you need both, to configure the menu and menu items.

Edit menus
----------

1) Navigate to Administration > Structure > Menus
2) Either click the Add menu button on top of the page, if you want to create new menu, or click the Edit menu link if
you want to edit existing menu.
3) Update the Title and Administrative summary fields if necessary
4) Set the Mega Menu status field to Enabled to enable mega menu for this particular menu.
5) Configure General options
6) Configure Position & Layout options
7) Configure Responsive & Mobile options
8) Configure Submenus options
9) Configure Descriptions options
10) Configure Images options
11) Configure Icons options
12) Save the menu and from now on, the menu will be processed and displayed as the Mega Menu.

Edit menu items
---------------

1) Choose Mega Menu item type for current menu item. Note that some sections are displayed and some are hidden depending
of the selected item type.
2) Configure General options
3) Configure Custom content options
4) Configure Layout options
5) Configure Responsive options
6) Configure Submenu options. Note that options related to submenu background image are not available for module-defined
menu items (e.g. Home in Main navigation) because of core Drupal 8 bug related to uploading images
(https://www.drupal.org/node/2898335)
7) Configure Image options. Note that this section is not available for module-defined menu items (e.g. Home in Main
navigation) because of core Drupal 8 bug related to uploading images (https://www.drupal.org/node/2898335)
8) Configure Icon options
9) Configure Drupal block options
10) Configure Row options
11) Configure Column layout options
12) Configure Tabs block options

CREDITS
-------

- FontAwesome library: http://fontawesome.io/
- Symfony PHP framework: https://symfony.com/
- Pixabay – free high quality images: http://pixabay.com
  https://pixabay.com/en/network-points-lines-interfaces-2496193
  https://pixabay.com/en/artificial-intelligence-robot-ai-ki-2167835
  https://pixabay.com/en/computer-internet-technology-laptop-1951964

LICENSE
-------

The module is published under GPL2 or later license (http://www.gnu.org/licenses/gpl-2.0.html)
