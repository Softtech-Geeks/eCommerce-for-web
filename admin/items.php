<?php

/*
	================================================
	== Items Page
	================================================
	*/

ob_start(); // Output Buffering Start

session_start();

$pageTitle = 'Items';
$option = "<option value='";
$endOption = "</option>";
$div = "<div class='container'>";
$enDiv = "</div>";
$secDiv = "<div class='alert alert-success'>";
if (isset($_SESSION['Username'])) {

	include 'init.php';

	$do = isset($_GET['do']) ? $_GET['do'] : 'Manage';

	if ($do == 'Manage') {


		$stmt = $con->prepare("SELECT 
										items.*, 
										categories.Name AS category_name, 
										users.Username 
									FROM 
										items
									INNER JOIN 
										categories 
									ON 
										categories.ID = items.Cat_ID 
									INNER JOIN 
										users 
									ON 
										users.UserID = items.Member_ID
									ORDER BY 
										Item_ID DESC");

		// Execute The Statement

		$stmt->execute();

		// Assign To Variable 

		$items = $stmt->fetchAll();

		if (!empty($items)) {

?>

			<h1 class="text-center">Manage Items</h1>
			<div class="container">
				<div class="table-responsive">
					<table class="main-table text-center table table-bordered">
						<th>
						<td>#ID</td>
						<td>Item Name</td>
						<td>Description</td>
						<td>Price</td>
						<td>Adding Date</td>
						<td>Category</td>
						<td>Username</td>
						<td>Control</td>
						</th>
						<?php
						foreach ($items as $item) { ?>
							<tr>
								<td><?php $item['Item_ID'] ?></td>
								<td><?php $item['Name'] ?></td>
								<td><?php $item['Description'] ?></td>
								<td><?php $item['Price'] ?></td>
								<td><?php $item['Add_Date'] ?></td>
								<td><?php $item['category_name'] ?></td>
								<td><?php $item['Username'] ?></td>
								<td>
									<a href='items.php?do=Edit&itemid="<?php $item['Item_ID'] ?> "' class='btn btn-success'><em class='fa fa-edit'></em> Edit</a>
									<a href='items.php?do=Delete&itemid="<?php $item['Item_ID'] ?> "' class='btn btn-danger confirm'><em class='fa fa-close'></em> Delete </a>";
									<?php if ($item['Approve'] == 0) {
										echo "<a 
													href='items.php?do=Approve&itemid=" . $item['Item_ID'] . "' 
													class='btn btn-info activate'>
													<em class='fa fa-check'></em> Approve</a>";
									} ?>
								</td>
							</tr>
						<?php }
						?>
						<tr>
					</table>
				</div>
				<a href="items.php?do=Add" class="btn btn-sm btn-primary">
					<em class="fa fa-plus"></em> New Item
				</a>
			</div>

		<?php } else { ?>

			<div class="container">
				<div class="nice-message">There\'s No Items To Show</div>
				<a href="items.php?do=Add" class="btn btn-sm btn-primary">
					<em class="fa fa-plus"></em> New Item
				</a>
			</div>

		<?php 	}
	} elseif ($do == 'Add') { ?>

		<h1 class="text-center">Add New Item</h1>
		<div class="container">
			<form class="form-horizontal" action="?do=Insert" method="POST">
				<!-- Start Name Field -->
				<div class="form-group form-group-lg">
					<label class="col-sm-2 control-label">Name</label>
					<div class="col-sm-10 col-md-6">
						<input type="text" name="name" class="form-control" required="required" placeholder="Name of The Item" />
					</div>
				</div>
				<!-- End Name Field -->
				<!-- Start Description Field -->
				<div class="form-group form-group-lg">
					<label class="col-sm-2 control-label">Description</label>
					<div class="col-sm-10 col-md-6">
						<input type="text" name="description" class="form-control" required="required" placeholder="Description of The Item" />
					</div>
				</div>
				<!-- End Description Field -->
				<!-- Start Price Field -->
				<div class="form-group form-group-lg">
					<label class="col-sm-2 control-label">Price</label>
					<div class="col-sm-10 col-md-6">
						<input type="text" name="price" class="form-control" required="required" placeholder="Price of The Item" />
					</div>
				</div>
				<!-- End Price Field -->
				<!-- Start Country Field -->
				<div class="form-group form-group-lg">
					<label class="col-sm-2 control-label">Country</label>
					<div class="col-sm-10 col-md-6">
						<input type="text" name="country" class="form-control" required="required" placeholder="Country of Made" />
					</div>
				</div>
				<!-- End Country Field -->
				<!-- Start Status Field -->
				<div class="form-group form-group-lg">
					<label class="col-sm-2 control-label">Status</label>
					<div class="col-sm-10 col-md-6">
						<select name="status">
							<option value="0">...</option>
							<option value="1">New</option>
							<option value="2">Like New</option>
							<option value="3">Used</option>
							<option value="4">Very Old</option>
						</select>
					</div>
				</div>
				<!-- End Status Field -->
				<!-- Start Members Field -->
				<div class="form-group form-group-lg">
					<label class="col-sm-2 control-label">Member</label>
					<div class="col-sm-10 col-md-6">
						<select name="member">
							<option value="0">...</option>
							<?php
							$allMembers = getAllFrom("*", "users", "UserID", "", "");
							foreach ($allMembers as $user) {
								echo $option . $user['UserID'] . "'>" . $user['Username'] . $endOption;
							}
							?>
						</select>
					</div>
				</div>
				<!-- End Members Field -->
				<!-- Start Categories Field -->
				<div class="form-group form-group-lg">
					<label class="col-sm-2 control-label">Category</label>
					<div class="col-sm-10 col-md-6">
						<select name="category">
							<option value="0">...</option>
							<?php
							$allCats = getAllFrom("*", "categories", "ID", "where parent = 0", "");
							foreach ($allCats as $cat) {
								echo $option . $cat['ID'] . "'>" . $cat['Name'] . $endOption;
								$childCats = getAllFrom("*", "categories", "ID", "where parent = {$cat['ID']}", "");
								foreach ($childCats as $child) {
									echo $option . $child['ID'] . "'>--- " . $child['Name'] . $endOption;
								}
							}
							?>
						</select>
					</div>
				</div>
				<!-- End Categories Field -->
				<!-- Start Tags Field -->
				<div class="form-group form-group-lg">
					<label class="col-sm-2 control-label">Tags</label>
					<div class="col-sm-10 col-md-6">
						<input type="text" name="tags" class="form-control" placeholder="Separate Tags With Comma (,)" />
					</div>
				</div>
				<!-- End Tags Field -->
				<!-- Start Submit Field -->
				<div class="form-group form-group-lg">
					<div class="col-sm-offset-2 col-sm-10">
						<input type="submit" value="Add Item" class="btn btn-primary btn-sm" />
					</div>
				</div>
				<!-- End Submit Field -->
			</form>
		</div>

		<?php

	} elseif ($do == 'Insert') {

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {

			echo "<h1 class='text-center'>Insert Item</h1>";
			echo $div;

			// Get Variables From The Form

			$name		= $_POST['name'];
			$desc 		= $_POST['description'];
			$price 		= $_POST['price'];
			$country 	= $_POST['country'];
			$status 	= $_POST['status'];
			$member 	= $_POST['member'];
			$cat 		= $_POST['category'];
			$tags 		= $_POST['tags'];

			// Validate The Form

			$formErrors = array();

			if (empty($name)) {
				$formErrors[] = 'Name Can\'t be <strong>Empty</strong>';
			}

			if (empty($desc)) {
				$formErrors[] = 'Description Can\'t be <strong>Empty</strong>';
			}

			if (empty($price)) {
				$formErrors[] = 'Price Can\'t be <strong>Empty</strong>';
			}

			if (empty($country)) {
				$formErrors[] = 'Country Can\'t be <strong>Empty</strong>';
			}

			if ($status == 0) {
				$formErrors[] = 'You Must Choose the <strong>Status</strong>';
			}

			if ($member == 0) {
				$formErrors[] = 'You Must Choose the <strong>Member</strong>';
			}

			if ($cat == 0) {
				$formErrors[] = 'You Must Choose the <strong>Category</strong>';
			}

			// Loop Into Errors Array And Echo It

			foreach ($formErrors as $error) {
				echo '<div class="alert alert-danger">' . $error . $enDiv;
			}

			// Check If There's No Error Proceed The Update Operation

			if (empty($formErrors)) {

				// Insert Userinfo In Database

				$stmt = $con->prepare("INSERT INTO 

						items(Name, Description, Price, Country_Made, Status, Add_Date, Cat_ID, Member_ID, tags)

						VALUES(:zname, :zdesc, :zprice, :zcountry, :zstatus, now(), :zcat, :zmember, :ztags)");

				$stmt->execute(array(

					'zname' 	=> $name,
					'zdesc' 	=> $desc,
					'zprice' 	=> $price,
					'zcountry' 	=> $country,
					'zstatus' 	=> $status,
					'zcat'		=> $cat,
					'zmember'	=> $member,
					'ztags'		=> $tags

				));

				// Echo Success Message

				$theMsg = $secDiv . $stmt->rowCount() . ' Record Inserted</div>';

				redirectHome($theMsg, 'back');
			}
		} else {

			echo $div;

			$theMsg = '<div class="alert alert-danger">Sorry You Cant Browse This Page Directly</div>';

			redirectHome($theMsg);

			echo $enDiv;
		}

		echo $enDiv;
	} elseif ($do == 'Edit') {

		// Check If Get Request item Is Numeric & Get Its Integer Value

		$itemid = isset($_GET['itemid']) && is_numeric($_GET['itemid']) ? intval($_GET['itemid']) : 0;

		// Select All Data Depend On This ID

		$stmt = $con->prepare("SELECT * FROM items WHERE Item_ID = ?");

		// Execute Query

		$stmt->execute(array($itemid));

		// Fetch The Data

		$item = $stmt->fetch();

		// The Row Count

		$count = $stmt->rowCount();

		// If There's Such ID Show The Form

		if ($count > 0) { ?>

			<h1 class="text-center">Edit Item</h1>
			<div class="container">
				<form class="form-horizontal" action="?do=Update" method="POST">
					<input type="hidden" name="itemid" value="<?php echo $itemid ?>" />
					<!-- Start Name Field -->
					<div class="form-group form-group-lg">
						<label class="col-sm-2 control-label">Name</label>
						<div class="col-sm-10 col-md-6">
							<input type="text" name="name" class="form-control" required="required" placeholder="Name of The Item" value="<?php echo $item['Name'] ?>" />
						</div>
					</div>
					<!-- End Name Field -->
					<!-- Start Description Field -->
					<div class="form-group form-group-lg">
						<label class="col-sm-2 control-label">Description</label>
						<div class="col-sm-10 col-md-6">
							<input type="text" name="description" class="form-control" required="required" placeholder="Description of The Item" value="<?php echo $item['Description'] ?>" />
						</div>
					</div>
					<!-- End Description Field -->
					<!-- Start Price Field -->
					<div class="form-group form-group-lg">
						<label class="col-sm-2 control-label">Price</label>
						<div class="col-sm-10 col-md-6">
							<input type="text" name="price" class="form-control" required="required" placeholder="Price of The Item" value="<?php echo $item['Price'] ?>" />
						</div>
					</div>
					<!-- End Price Field -->
					<!-- Start Country Field -->
					<div class="form-group form-group-lg">
						<label class="col-sm-2 control-label">Country</label>
						<div class="col-sm-10 col-md-6">
							<input type="text" name="country" class="form-control" required="required" placeholder="Country of Made" value="<?php echo $item['Country_Made'] ?>" />
						</div>
					</div>
					<!-- End Country Field -->
					<!-- Start Status Field -->
					<div class="form-group form-group-lg">
						<label class="col-sm-2 control-label">Status</label>
						<div class="col-sm-10 col-md-6">
							<select name="status">
								<option value="1" <?php if ($item['Status'] == 1) {
														echo 'selected';
													} ?>>New</option>
								<option value="2" <?php if ($item['Status'] == 2) {
														echo 'selected';
													} ?>>Like New</option>
								<option value="3" <?php if ($item['Status'] == 3) {
														echo 'selected';
													} ?>>Used</option>
								<option value="4" <?php if ($item['Status'] == 4) {
														echo 'selected';
													} ?>>Very Old</option>
							</select>
						</div>
					</div>
					<!-- End Status Field -->
					<!-- Start Members Field -->
					<div class="form-group form-group-lg">
						<label class="col-sm-2 control-label">Member</label>
						<div class="col-sm-10 col-md-6">
							<select name="member">
								<?php
								$allMembers = getAllFrom("*", "users", "UserID", "", "");
								foreach ($allMembers as $user) {
									echo $option . $user['UserID'] . "'";
									if ($item['Member_ID'] == $user['UserID']) {
										echo 'selected';
									}
									echo ">" . $user['Username'] . $endOption;
								}
								?>
							</select>
						</div>
					</div>
					<!-- End Members Field -->
					<!-- Start Categories Field -->
					<div class="form-group form-group-lg">
						<label class="col-sm-2 control-label">Category</label>
						<div class="col-sm-10 col-md-6">
							<select name="category">
								<?php
								$allCats = getAllFrom("*", "categories", "ID", "where parent = 0", "");
								foreach ($allCats as $cat) {
									echo $option . $cat['ID'] . "'";
									if ($item['Cat_ID'] == $cat['ID']) {
										echo ' selected';
									}
									echo ">" . $cat['Name'] . $endOption;
									$childCats = getAllFrom("*", "categories", "ID", "where parent = {$cat['ID']}", "");
									foreach ($childCats as $child) {
										echo $option . $child['ID'] . "'";
										if ($item['Cat_ID'] == $child['ID']) {
											echo ' selected';
										}
										echo ">--- " . $child['Name'] . $endOption;
									}
								}
								?>
							</select>
						</div>
					</div>
					<!-- End Categories Field -->
					<!-- Start Tags Field -->
					<div class="form-group form-group-lg">
						<label class="col-sm-2 control-label">Tags</label>
						<div class="col-sm-10 col-md-6">
							<input type="text" name="tags" class="form-control" placeholder="Separate Tags With Comma (,)" value="<?php echo $item['tags'] ?>" />
						</div>
					</div>
					<!-- End Tags Field -->
					<!-- Start Submit Field -->
					<div class="form-group form-group-lg">
						<div class="col-sm-offset-2 col-sm-10">
							<input type="submit" value="Save Item" class="btn btn-primary btn-sm" />
						</div>
					</div>
					<!-- End Submit Field -->
				</form>

				<?php

				// Select All Users Except Admin 

				$stmt = $con->prepare("SELECT 
												comments.*, users.Username AS Member  
											FROM 
												comments
											INNER JOIN 
												users 
											ON 
												users.UserID = comments.user_id
											WHERE item_id = ?");

				// Execute The Statement

				$stmt->execute(array($itemid));

				// Assign To Variable 

				$rows = $stmt->fetchAll();

				if (!empty($rows)) {

				?>
					<h1 class="text-center">Manage [ <?php echo $item['Name'] ?> ] Comments</h1>
					<div class="table-responsive">
						<table class="main-table text-center table table-bordered">
							<th>
							<td>Comment</td>
							<td>User Name</td>
							<td>Added Date</td>
							<td>Control</td>
							</th>
							<?php
							foreach ($rows as $row) { ?>
								<tr>
									<td><?php $row['comment'] ?></td>
									<td><?php $row['Member'] ?></td>
									<td><?php $row['comment_date'] ?></td>
									<td>
										<a href='comments.php?do=Edit&comid="<?php $row['c_id']  ?>"' class='btn btn-success'><em class='fa fa-edit'></em> Edit</a>
										<a href='comments.php?do=Delete&comid="<?php $row['c_id']  ?>"' class='btn btn-danger confirm'><em class='fa fa-close'></em> Delete </a>";
									<?php if ($row['status'] == 0) {
										echo "<a href='comments.php?do=Approve&comid="
											. $row['c_id'] . "' 
														class='btn btn-info activate'>
														<i class='fa fa-check'></i> Approve</a>";
									}
									echo "</td></tr>";
								}
									?>
								<tr>
						</table>
					</div>
				<?php } ?>
			</div>

<?php

			// If There's No Such ID Show Error Message

		} else {

			echo $div;

			$theMsg = '<div class="alert alert-danger">Theres No Such ID</div>';

			redirectHome($theMsg);

			echo $enDiv;
		}
	} elseif ($do == 'Update') {

		echo "<h1 class='text-center'>Update Item</h1>";
		echo $div;

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {

			// Get Variables From The Form

			$id 		= $_POST['itemid'];
			$name 		= $_POST['name'];
			$desc 		= $_POST['description'];
			$price 		= $_POST['price'];
			$country	= $_POST['country'];
			$status 	= $_POST['status'];
			$cat 		= $_POST['category'];
			$member 	= $_POST['member'];
			$tags 		= $_POST['tags'];

			// Validate The Form

			$formErrors = array();

			if (empty($name)) {
				$formErrors[] = 'Name Can\'t be <strong>Empty</strong>';
			}

			if (empty($desc)) {
				$formErrors[] = 'Description Can\'t be <strong>Empty</strong>';
			}

			if (empty($price)) {
				$formErrors[] = 'Price Can\'t be <strong>Empty</strong>';
			}

			if (empty($country)) {
				$formErrors[] = 'Country Can\'t be <strong>Empty</strong>';
			}

			if ($status == 0) {
				$formErrors[] = 'You Must Choose the <strong>Status</strong>';
			}

			if ($member == 0) {
				$formErrors[] = 'You Must Choose the <strong>Member</strong>';
			}

			if ($cat == 0) {
				$formErrors[] = 'You Must Choose the <strong>Category</strong>';
			}

			// Loop Into Errors Array And Echo It

			foreach ($formErrors as $error) {
				echo '<div class="alert alert-danger">' . $error . $enDiv;
			}

			// Check If There's No Error Proceed The Update Operation

			if (empty($formErrors)) {

				// Update The Database With This Info

				$stmt = $con->prepare("UPDATE 
												items 
											SET 
												Name = ?, 
												Description = ?, 
												Price = ?, 
												Country_Made = ?,
												Status = ?,
												Cat_ID = ?,
												Member_ID = ?,
												tags = ?
											WHERE 
												Item_ID = ?");

				$stmt->execute(array($name, $desc, $price, $country, $status, $cat, $member, $tags, $id));

				// Echo Success Message

				$theMsg = $secDiv . $stmt->rowCount() . ' Record Updated</div>';

				redirectHome($theMsg, 'back');
			}
		} else {

			$theMsg = '<div class="alert alert-danger">Sorry You Cant Browse This Page Directly</div>';

			redirectHome($theMsg);
		}

		echo $enDiv;
	} elseif ($do == 'Delete') {

		echo "<h1 class='text-center'>Delete Item</h1>";
		echo $div;

		// Check If Get Request Item ID Is Numeric & Get The Integer Value Of It

		$itemid = isset($_GET['itemid']) && is_numeric($_GET['itemid']) ? intval($_GET['itemid']) : 0;

		// Select All Data Depend On This ID

		$check = checkItem('Item_ID', 'items', $itemid);

		// If There's Such ID Show The Form

		if ($check > 0) {

			$stmt = $con->prepare("DELETE FROM items WHERE Item_ID = :zid");

			$stmt->bindParam(":zid", $itemid);

			$stmt->execute();

			$theMsg = $secDiv . $stmt->rowCount() . ' Record Deleted</div>';

			redirectHome($theMsg, 'back');
		} else {

			$theMsg = '<div class="alert alert-danger">This ID is Not Exist</div>';

			redirectHome($theMsg);
		}

		echo $enDiv;
	} elseif ($do == 'Approve') {

		echo "<h1 class='text-center'>Approve Item</h1>";
		echo $div;

		// Check If Get Request Item ID Is Numeric & Get The Integer Value Of It

		$itemid = isset($_GET['itemid']) && is_numeric($_GET['itemid']) ? intval($_GET['itemid']) : 0;

		// Select All Data Depend On This ID

		$check = checkItem('Item_ID', 'items', $itemid);

		// If There's Such ID Show The Form

		if ($check > 0) {

			$stmt = $con->prepare("UPDATE items SET Approve = 1 WHERE Item_ID = ?");

			$stmt->execute(array($itemid));

			$theMsg = $secDiv . $stmt->rowCount() . ' Record Updated</div>';

			redirectHome($theMsg, 'back');
		} else {

			$theMsg = '<div class="alert alert-danger">This ID is Not Exist</div>';

			redirectHome($theMsg);
		}

		echo $enDiv;
	}

	include $tpl . 'footer.php';
} else {

	header('Location: index.php');

	exit();
}

ob_end_flush(); // Release The Output

?>