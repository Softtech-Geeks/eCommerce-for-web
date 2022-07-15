<?php
ob_start();
session_start();
$pageTitle = 'Homepage';
include 'init.php';
?>
<div class="container">
	<div class="row">
		<?php
		$allItems = getAllFrom('*', 'items', 'Item_ID', 'where Approve = 1', '');
		foreach ($allItems as $item) { ?>
			<div class="col-sm-6 col-md-3">
				<div class="thumbnail item-box">
					<span class="price-tag"><?php echo $item['Price'] ?></span>
					<img class="img-responsive" src="img.png" alt="" />
					<div class="caption">
						<h3><a href="items.php?itemid=<?php echo $item['Item_ID'] ?>"><?php echo $item['Name'] ?></a></h3>
						<p><?php echo $item['Description'] ?></p>
						<div class="date"><?php echo $item['Add_Date'] ?></div>
					</div>
				</div>
			</div>
		<?php } ?>
	</div>
</div>
<?php
include $tpl . 'footer.php';
ob_end_flush();
?>