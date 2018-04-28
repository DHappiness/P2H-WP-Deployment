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
    <link rel="icon" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAIIAAACDCAYAAABBX8NYAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6MzNGNDUzM0I5OUZEMTFFNzhGOERCQUE4RTE4MUJCNDciIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6MzNGNDUzM0M5OUZEMTFFNzhGOERCQUE4RTE4MUJCNDciPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDozM0Y0NTMzOTk5RkQxMUU3OEY4REJBQThFMTgxQkI0NyIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDozM0Y0NTMzQTk5RkQxMUU3OEY4REJBQThFMTgxQkI0NyIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PskaRuIAABCmSURBVHja7F0LcFxV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">
    <?php if ( ! empty( $deployment_result ) ) : ?>
        <style type="text/css"><?php
        echo file_get_contents( 'https://raw.githubusercontent.com/DenisYakimchuk/P2H-WP-Deployment/master/include/css/styles.css' );
        ?></style>
    <?php endif; ?>
</head>
<body>
    <?php $deployment_file_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>
    <header id="header">
        <h1 class="logo"><a href="<?php echo $deployment_file_url; ?>"><img src="data:image/png;base64,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" alt="Wordpress Deplyment P2H" /></a></h1>
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
                                    <label class="template" title="Separate Unique Template"><input type="checkbox" name="pages[0][template]" value="on" onchange="this.parentNode.classList.toggle('active');"><em></em><span>Separate Template</span></label>
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
        <p class="copyrights">&copy; Wordpress Deployment for P2H's Implementators. 2017 - <?php echo date( 'Y' ); ?>. <?php if ( isset( $_COOKIE['skip_updater'] ) ) {echo '<a href="https://github.com/DenisYakimchuk/P2H-WP-Deployment" target="_blank" style="color: #f00;">Version ';} else {echo 'Version ';} ?><?php echo $deployment_settings['deployment_version']; if ( isset( $_COOKIE['skip_updater'] ) ) {echo '</a>';} ?></p>
    </footer>
</body>
</html>