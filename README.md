<h1># P2H-WP-Deployment</h1>
<p><em>Useful only for P2H employees.</em></p>

<p>Current Version: 1.0</p>

<p>Edit "include/config.inc" before using. You need to setup your SVN Credentials at first.</p>

<p>Important: use the script only for empty staging to avoid erasing any data.</p>

<strong>Capabilities:</strong>
- Downloading and installing latest WordPress version
- Downloading and installing ACF PRO from SVN (need credentials to be set in config.php)
- Downloading base theme from SVN or from the "include" folder of deployment ( in case you have own configured theme, theme folder should be named "base" ) and naming it like project is.
- Fast creating of default project pages with automatically generated lorem ipsum content.
- Automatically setting up of /%postname%/ permalinks and refreshing rewrite rules.
- <em>Deployment files are automatically deleting after script is done(if server has "staging restrictions" file). So be attentive and don't forget to copy generated credentials at the end.</em>
- Automated creation of Custom Post Types and Custom Taxonomies using singular name of entity.
- Automated adding of template files for pages
- Automatically downloading and installation of wordpress plugins just by selecting it in the list of downloadable plugins.
- Fast basical creating of ACF fields.
- Automated authentication to admin account after deployment is done.


<strong>Future Features:</strong>
- Creating Main Menu from selected pages.
- Uploading of homepage html template to parse it for further including to theme templates.
- Field to add a theme screenshot.
- Pack the WP-Deployment script to a single file.
