<?php
$imce =& $imce_ref['imce'];//keep this line.

/*
 * Although the file list table here is available for theming,
 * it is not recommended to change the table structure, because
 * it is read and manipulated by javascript assuming this is the deafult structure.
 * You can always change the data created by format functions
 * such as format_size or format_date, or you can do css theming which is the best practice here.
 */
?>
<table id="file-list" class="files"><tbody><?php
if (@$imce['perm']['browse'] && !empty($imce['files'])) {
  foreach (@$imce['files'] as $name => $file) {?>
  <?php
  $raw = rawurlencode($file['name']);
  
  $ext = strtolower(pathinfo($raw, PATHINFO_EXTENSION));
     ?>
  <?php if($ext === "html" || $ext === "txt" || $ext === "htm" || $ext === "xhtml" || $ext === "xml" || $ext === "dita"): ?>
  <tr id="<?php print $raw; ?>" data-name="<?php print urlencode($raw); ?>" class="edit-item-action">
    <td class="name"><?php print $raw; ?></td>
    <td class="size" id="<?php print $file['size']; ?>"><?php print format_size($file['size']); ?></td>
    <td class="date" id="<?php print $file['date']; ?>"><?php print format_date($file['date'], 'short'); ?></td>
  </tr>
  <?php endif; ?>
  <?php
  }
}?>
</tbody></table>
<script type="text/javascript" src="<?php echo drupal_get_path('module', 'imce')."/js/imce.editfile.js" ?>"></script>