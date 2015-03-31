<?php require('header.php'); ?>
<div>
  <ul class="breadcrumb">
    <li>
      <a href="#">Home</a>
    </li>
    <li>
      <a href="#">Categories</a>
    </li>
  </ul>
</div>
<h3>All Your Categories</h3>
<?php
openBox("Categories", "folder-open", 12, "<p>The table below listing your product categories is editable.</p>"
        . "<p>You can edit the category name or the associated comment by clicking on it.  You can also set a category inactive (which means the ads belonging to it won't be served) by clicking on the green <b>Active</b> button. An inactive button will have a red <b>Disabled</b>.</p>"
        . "<p> If you want to create a new category, please use the menu item <a href='categories-new.php'><b>New Category</b></a>.<p>");
?>
<table class="table table-striped table-bordered responsive data-table">
  <thead>
    <tr>
      <th style='width:35%'>Name</th>
      <th style='width:45%'>Comment</th>
      <th class="center-text" style='width:7%'>Products</th>
      <th class="center-text" style='width:7%'>Subscriptions</th>
      <th class="center-text" style='width:6%;min-width:90px'>Active?</th>
    </tr>
  </thead>
  <tbody>

    <?php
    $active = 1;
    $categories = $db->getData('categories');
    foreach ($categories as $cat) {
      extract($cat);
      if ($active) {
        $class = 'success';
      }
      else {
        $class = 'danger';
      }
      $bannerCount = $db->getCount('products', array('category_id' => $id));
      $htmlCount = $db->getCount('products', array('category_id' => $id, 'recurring' => 1));
      echo <<<EOF
    <tr>
      <td><a class='xedit' data-name='name' data-pk='$id' data-tpl='<input type="text" style="width:100px">' >$name</a></td>
      <td><a class='xedit' data-name='comment' data-pk='$id' data-tpl='<input type="text" style="width:550px">' >$comment</a></td>
      <td class="center-text">$bannerCount</td>
      <td class="center-text">$htmlCount</td>
      <td class="center-text"><a class='xedit-checkbox btn-sm btn-$class' data-name='active' data-type='checklist' data-pk='$id' data-title='Status' data-value='$active'></a></td>
    </tr>
EOF;
    }
    ?>
  </tbody>
</table>
<a class="btn btn-success action" href="categories-new.php"><i class="glyphicon glyphicon-plus icon-white action"></i>&nbsp;Add New Category</a>

<?php
closeBox();
?>
<script>
  var xeditHandler = 'ajax/categories.php';
</script>
<?php
require('footer.php');
