<link rel="stylesheet" type="text/css" href="./css/styles.css">
<div class="main-settings">
  <form action="./deployment.php" method="post" enctype="multipart/form-data">
    <fieldset>
      <legend>MySQL Settings</legend>
      <input type="text" name="dbname" placeholder="DB name + user name ( they are the same )" />
      <input type="text" name="dbuser" placeholder="DB User" />
      <input type="text" name="password" placeholder="Password" />
    </fieldset>
    <fieldset>
      <legend>Wordpress Settings</legend>
      <input type="text" name="sitename" placeholder="Site Name" />
    </fieldset>
    <fieldset>
      <legend>Homepage HTML Template ( if exists )</legend>
      <input type="file" name="homepage" placeholder="Home Template" />
    </fieldset>
    <input type="hidden" name="action" value="true" />
    <input type="submit" value="Execute">
  </form>
</div>