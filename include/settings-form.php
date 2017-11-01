<?php global $deployment_result; ?>
<!DOCTYPE html>
<html>
<head>
    <title>WP Deployment</title>
    <link href="https://fonts.googleapis.com/css?family=Questrial" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="include/css/fancybox.css">
    <link rel="stylesheet" type="text/css" href="include/css/styles.css">
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="include/js/scripts.js"></script>
    <link rel="icon" href="include/images/favicon.png">
    <?php if ( ! empty( $deployment_result ) ) : ?>
        <style type="text/css"><?php
        echo file_get_contents( 'https://fonts.googleapis.com/css?family=Questrial' );
        echo file_get_contents( 'css/style.css' );
        ?></style>
    <?php endif; ?>
</head>
<body>
    <?php $deployment_file_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>
    <header id="header">
        <h1 class="logo"><a href="<?php echo $deployment_file_url; ?>"><img src="include/images/logo.png" alt="Wordpress Deplyment P2H" /></a></h1>
    </header>
    <main id="main">
        <div class="main-settings">
            <?php if ( empty( $deployment_result ) ) : ?>
                <form action="./deployment.php" method="post" enctype="multipart/form-data" class="form-validation">
                  <div class="two-columns">
                      <div class="col">
                          <fieldset>
                            <h3>MySQL Settings</h3>
                            <label><input type="checkbox" class="show-hide" value="1" data-target="mysql"> Show full settings</label>
                            <div data-conditional="mysql">
                              <input type="text" name="host" value="localhost" class="optional" />
                              <input type="text" name="dbname" data-required="true" size="41" placeholder="DB name + user name ( they are the same )" />
                              <div class="fields-group">
                                  <input type="text" name="dbuser" placeholder="DB User" class="optional" />
                                  <input type="text" name="password" placeholder="Password" />
                              </div>
                            </div>
                          </fieldset>
                          <fieldset>
                            <h3>Wordpress Settings</h3>
                            <label><input type="checkbox" class="show-hide" value="1" data-target="wordpress"> Show full settings</label>
                            <div data-conditional="wordpress">
                                <input type="text" name="sitename" data-required="true" placeholder="Site Name" />
                                <input type="text" name="site_description" class="optional" placeholder="Site Description" />
                                <div class="optional">
                                    <div><label><input type="checkbox" name="delete_themes" value="1" checked="checked">Delete Default Themes</label></div>
                                </div>
                                <div class="field-holder">
                                    <strong class="title">Homepage Type</strong>
                                    <label><input type="radio" name="homepage" value="0" checked="checked">Custom Template </label>
                                    <label><input type="radio" name="homepage" value="1">Blog Posts</label>
                                    <label><input type="radio" name="homepage" value="2">Front Page</label>
                                    <label><a class="icon icon-plus add-acf-group" data-lightbox="ajax" data-src="<?php echo $deployment_file_url; ?>?acf-group=home" data-type="ajax" href="javascript:;" title='Add Field Group for "Homepage" (depends on selected type)'> </a></label>
                                </div>
                                <div class="field-holder">
                                    <strong class="title">ACF Options Page</strong>
                                    <label for="options-page-settings">Create Fields&nbsp;&nbsp;<a id="options-page-settings" class="icon icon-plus add-acf-group" data-lightbox="ajax" data-src="<?php echo $deployment_file_url; ?>?acf-group=options" data-type="ajax" href="javascript:;" title='Add Field Group for "Options Page"'></a></label>
                                </div>
                            </div>
                          </fieldset>
                          <fieldset>
                            <h3>Create CPTs</h3>
                            <div class="clone-elements" data-depth="1" data-subitems="taxonomies">
                              <a href="#" class="add-item">Add CPT</a>
                              <br /><br />
                              <div class="item">
                                <div class="field-holder">
                                    <input type="text" name="cpt[0][title]" placeholder="Title (Singular)">
                                    <a class="icon icon-plus add-acf-group" data-lightbox="ajax" data-src="<?php echo $deployment_file_url; ?>?acf-group=cpt[0]" data-type="ajax" href="javascript:;" title='Add Field Group'> </a>
                                </div>
                                <a href="#" class="add-subitem">Add Taxonomy</a>
                              </div>
                            </div>
                          </fieldset>
                          <fieldset>
                            <h3>Create Pages</h3>
                            <label><input type="checkbox" class="show-hide" value="1" data-target="pages-unification"> Unify Pages</label>
                            <div data-conditional="pages-unification">
                                <div class="optional">
                                    <a href="#" class="unify-pages">Highlight Identical</a> | <a href="#" class="unify-pages delete">Delete Identical</a>
                                    <br /><br />
                                </div>
                            </div>
                            <div class="clone-elements" data-subitems="subpages">
                              <a href="#" class="add-item">Add Page</a>
                              <br /><br />
                              <div class="item">
                                <div class="field-holder">
                                    <label class="template" title="Separate Unique Template"><input type="checkbox" name="pages[0][template]" onchange="this.parentNode.classList.toggle('active');"><em></em><span>Separate Template</span></label>
                                    <input type="text" name="pages[0][title]" placeholder="Page Title">
                                    <a class="icon icon-plus add-acf-group" data-lightbox="ajax" data-src="<?php echo $deployment_file_url; ?>?acf-group=pages[0]" data-type="ajax" href="javascript:;" title='Add Field Group'> </a>
                                </div>
                                <a href="#" class="add-subitem">Add Subpage</a>
                              </div>
                            </div>
                          </fieldset>
                      </div>
                      <div class="col">
                          <fieldset>
                            <h3>Install Plugins</h3>
                            <div class="plugin-item"><label><input type="checkbox"<?php if ( in_array( 'acf', $deployment_settings['install_plugins'] ) ) {echo ' checked="checked"';} ?> value="acf" name="plugins[]"> Advanced Custom Fields PRO (from SVN)</label></div>
                            <?php foreach ( $plugins as $slug => $plugin ) : ?>
                                <div class="plugin-item"><label><input type="checkbox"<?php if ( in_array( $slug, $deployment_settings['install_plugins'] ) ) {echo ' checked="checked"';} ?> value="<?php echo $slug; ?>" name="plugins[]"> <?php echo $plugin['label']; ?></label></div>
                            <?php endforeach; ?>
                          </fieldset>
                      </div>
                  </div>
                  <br>
                  <input type="hidden" name="deploy" value="true" />
                  <input type="submit" value="Execute" />
                </form>
            <?php else : ?>
                <div class="result">
                    <?php echo $deployment_result; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>
    <footer id="footer">
        <p class="process-time">
            <?php sleep(1);
            $time = number_format( ( microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"] ), 2 );
            echo "Process Time: {$time} seconds"; ?>
        </p>
        <p class="copyrights">&copy; Wordpress Deployment for P2H's Implementators. <?php echo date( 'Y' ); ?>. <?php if ( isset( $_COOKIE['skip_updater'] ) ) {echo '<a href="https://github.com/DenisYakimchuk/P2H-WP-Deployment" target="_blank" style="color: #f00;">Version ';} else {echo 'Version ';} ?><?php echo $deployment_settings['deployment_version']; if ( isset( $_COOKIE['skip_updater'] ) ) {echo '</a>';} ?></p>
    </footer>
</body>
</html>
