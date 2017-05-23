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
  <form action="./deployment.php" method="post" enctype="multipart/form-data">
    <fieldset>
      <legend>MySQL Settings</legend>
      <input type="text" name="dbname" size="34" placeholder="DB name + user name ( they are the same )" />
      <input type="text" name="dbuser" placeholder="DB User" />
      <input type="text" name="password" placeholder="Password" />
    </fieldset>
    <fieldset>
      <legend>Wordpress Settings</legend>
      <input type="text" name="sitename" placeholder="Site Name" />
    </fieldset>
    <fieldset>
      <legend>Create Pages</legend>
      <div class="clone-elements">
        <div class="pages">
          <a href="#" class="add-page">Add Page</a>
          <br /><br />
          <div class="page">
            <input type="text" name="pages[0][title]" placeholder="Page Title">
            <a href="#" class="add-subpage">Add Subpage</a>
          </div>
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
