<!DOCTYPE html>
<html>
<head>
    <title>WP Deployment</title>
    <link rel="stylesheet" type="text/css" href="include/css/styles.css">
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="include/js/scripts.js"></script>
</head>
<body>
<div class="main-settings">
  <form action="./deployment.php" method="post" enctype="multipart/form-data" class="form-validation">
    <fieldset>
      <legend>MySQL Settings</legend>
      <input type="text" name="dbname" data-required="true" size="34" placeholder="DB name + user name ( they are the same )" />
      <input type="text" name="dbuser" placeholder="DB User" />
      <input type="text" name="password" data-required="true" placeholder="Password" />
    </fieldset>
    <fieldset>
      <legend>Wordpress Settings</legend>
      <input type="text" name="sitename" data-required="true" placeholder="Site Name" />
    </fieldset>
    <fieldset>
      <legend>Create CPTs</legend>
      <div class="clone-elements" data-depth="1" data-subitems="taxonomies">
        <a href="#" class="add-item">Add CPT</a>
        <br /><br />
        <div class="item">
          <input type="text" name="cpt[0][title]" placeholder="Title (Singular)">
          <a href="#" class="add-subitem">Add Taxonomy</a>
        </div>
      </div>
    </fieldset>
    <fieldset>
      <legend>Create Pages</legend>
      <div class="clone-elements" data-subitems="subpages">
        <a href="#" class="add-item">Add Page</a>
        <br /><br />
        <div class="item">
          <input type="text" name="pages[0][title]" placeholder="Page Title">
          <a href="#" class="add-subitem">Add Subpage</a>
        </div>
      </div>
    </fieldset>
    <br>
    <input type="hidden" name="action" value="true" />
    <input type="submit" value="Execute">
  </form>
</div>
</body>
</html>
