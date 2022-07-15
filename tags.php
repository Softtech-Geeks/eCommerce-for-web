<?php
session_start();
include 'init.php';
?>

<div class="container">
	<div class="row">
		<?php
		if (isset($_GET['name'])) {
			$tag = $_GET['name'];
			echo "<h1 class='text-center'>" . $tag . "</h1>";
			$tagItems = getAllFrom("*", "items", "Item_ID", "where tags like '%$tag%'", "AND Approve = 1");
			foreach ($tagItems as $item) { ?>
				<div class="col-sm-6 col-md-3">
					<div class="thumbnail item-box">
						<span class="price-tag"><?php $item['Price'] ?></span>
						<img class="img-responsive" src="img.png" alt="" />
						<div class="caption">
							<h3><a href="items.php?itemid=<?php $item['Item_ID'] ?>"><?php $item['Name'] ?></a></h3>
							<p><?php $item['Description'] ?></p>
							<div class="date"><?php $item['Add_Date'] ?></div>
						</div>
					</div>
				</div>
		<?php }
		} else {
			echo 'You Must Enter Tag Name';
		}
		?>
	</div>
</div>

<?php include $tpl . 'footer.php'; ?>