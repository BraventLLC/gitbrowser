<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php print $GLOBALS['language']->language; ?>" xml:lang="<?php print $GLOBALS['language']->language; ?>" class="imce">
<head>
  <link rel="shortcut icon" href="<?php echo drupal_get_path('module', 'imce')."/favicon.ico" ?>"/>
  <title><?php print t('File Browser'); ?></title>
  <meta name="robots" content="noindex,nofollow" />
  <?php if (isset($_GET['app'])): drupal_add_js(drupal_get_path('module', 'imce') .'/js/imce_set_app.js'); endif;?>
  <?php print drupal_get_html_head(); ?>
  <?php print drupal_get_css(); ?>
  <?php print drupal_get_js('header'); ?>
  <style media="all" type="text/css">/*Quick-override*/</style>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
  <script src="<?php echo drupal_get_path('module', 'imce')."/js/imce.loading.js" ?>"></script>
  <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo drupal_get_path('module', 'imce')."/lib/sweetalert2/sweetalert2.min.css" ?>">
  <script src="<?php echo drupal_get_path('module', 'imce')."/lib/sweetalert2/sweetalert2.min.js" ?>"></script>
  <!-- Loads SweetAlert Configs -->
  <script src="<?php echo drupal_get_path('module', 'imce')."/js/swal.config.js" ?>"></script>
  <script>
    window["itemsdata"] = [];
    window["current_data"] = "";
    window["current_file"] = "";
  </script>
</head>

<body class="imce">
<div id="imce-messages"><?php print theme('status_messages'); ?></div>
<?php print $content; ?>
<?php print drupal_get_js('footer'); ?>
</body>

</html>
