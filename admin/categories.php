<?php

/*
	================================================
	== Category Page
	================================================
	*/

ob_start(); // Output Buffering Start

session_start();

$pageTitle = 'Categories';
$div = "</div>";
$initialSuccess = "<div class='alert alert-success'>";
$initialDiv = "<div class='container'>";

if (isset($_SESSION['Username'])) {

	include 'init.php';

	$do = isset($_GET['do']) ? $_GET['do'] : 'Manage';

	if ($do == 'Manage') {

		$sort = 'asc';

		$sort_array = array('asc', 'desc');

		if (isset($_GET['sort']) && in_array($_GET['sort'], $sort_array)) {

			$sort = $_GET['sort'];
		}

		$stmt2 = $con->prepare("SELECT * FROM categories WHERE parent = 0 ORDER BY Ordering $sort");

		$stmt2->execute();

		$cats = $stmt2->fetchAll();

		if (!empty($cats)) {

?>

			<h1 class="text-center">Manage Categories</h1>
			<div class="container categories">
				<div class="panel panel-default">
					<div class="panel-heading">
						<em class="fa fa-edit"></em> Manage Categories
						<div class="option pull-right">
							<em class="fa fa-sort"></em> Ordering: [
							<a class="<?php if ($sort == 'asc') {
											echo 'active';
										} ?>" href="?sort=asc">Asc</a> |
							<a class="<?php if ($sort == 'desc') {
											echo 'active';
										} ?>" href="?sort=desc">Desc</a> ]
							<em class="fa fa-eye"></em> View: [
							<span class="active" data-view="full">Full</span> |
							<span data-view="classic">Classic</span> ]
						</div>
					</div>
					<div class="panel-body">
						<?php
						foreach ($cats as $cat) { ?>
							<div class='cat'>
								<div class='hidden-buttons'>
									<a href='categories.php?do=Edit&catid="<?php echo $cat['ID']  ?> "' class='btn btn-xs btn-primary'><em class='fa fa-edit'></em> Edit</a>
									<a href='categories.php?do=Delete&catid="<?php echo $cat['ID']  ?> "' class='confirm btn btn-xs btn-danger'><em class='fa fa-close'></em> Delete</a>
								</div>
								<h3><?php echo $cat['Name'] ?></h3>
								<div class='full-view'>
									<p>
										<?php if ($cat['Description'] == '') {
											echo 'This category has no description';
										} else {
											echo $cat['Description'];
										} ?>
									</p>
									<?php if ($cat['Visibility'] == 1) {
										echo '<span class="visibility cat-span"><i class="fa fa-eye"></i> Hidden</span>';
									}
									if ($cat['Allow_Comment'] == 1) {
										echo '<span class="commenting cat-span"><i class="fa fa-close"></i> Comment Disabled</span>';
									}
									if ($cat['Allow_Ads'] == 1) {
										echo '<span class="advertises cat-span"><i class="fa fa-close"></i> Ads Disabled</span>';
									} ?>
								</div>

								// Get Child Categories
								<?php
								$childCats = getAllFrom("*", "categories", "ID", "where parent = {$cat['ID']}", "", "ASC");
								if (!empty($childCats)) { ?>
									<h4 class='child-head'>Child Categories</h4>
									<ul class='list-unstyled child-cats'>
								<?php foreach ($childCats as $c) {
										echo "<li class='child-link'>
												<a href='categories.php?do=Edit&catid=" . $c['ID'] . "'>" . $c['Name'] . "</a>
												<a href='categories.php?do=Delete&catid=" . $c['ID'] . "' class='show-delete confirm'> Delete</a>
											</li>";
									}
									echo "</ul>";
								}
								echo "</div><hr>";
							}
								?>
							</div>
					</div>
					<a class="add-category btn btn-primary" href="categories.php?do=Add"><em class="fa fa-plus"></em> Add New Category</a>
				</div>

			<?php } else { ?>

				<div class="container">';
					<div class="nice-message">There\'s No Categories To Show</div>';
					<a href="categories.php?do=Add" class="btn btn-primary">
						<em class="fa fa-plus"></em> New Category
					</a>';
				</div>';
			<?php } ?>

		<?php

	} elseif ($do == 'Add') { ?>

			<h1 class="text-center">Add New Category</h1>
			<div class="container">
				<form class="form-horizontal" action="?do=Insert" method="POST">
					<!-- Start Name Field -->
					<div class="form-group form-group-lg">
						<label class="col-sm-2 control-label">Name</label>
						<div class="col-sm-10 col-md-6">
							<input type="text" name="name" class="form-control" autocomplete="off" required="required" placeholder="Name Of The Category" />
						</div>
					</div>
					<!-- End Name Field -->
					<!-- Start Description Field -->
					<div class="form-group form-group-lg">
						<label class="col-sm-2 control-label">Description</label>
						<div class="col-sm-10 col-md-6">
							<input type="text" name="description" class="form-control" placeholder="Describe The Category" />
						</div>
					</div>
					<!-- End Description Field -->
					<!-- Start Ordering Field -->
					<div class="form-group form-group-lg">
						<label class="col-sm-2 control-label">Ordering</label>
						<div class="col-sm-10 col-md-6">
							<input type="text" name="ordering" class="form-control" placeholder="Number To Arrange The Categories" />
						</div>
					</div>
					<!-- End Ordering Field -->
					<!-- Start Category Type -->
					<div class="form-group form-group-lg">
						<label class="col-sm-2 control-label">Parent?</label>
						<div class="col-sm-10 col-md-6">
							<select name="parent">
								<option value="0">None</option>
								<?php
								$allCats = getAllFrom("*", "categories", "ID", "where parent = 0", "", "ASC");
								foreach ($allCats as $cat) {
									echo "<option value='" . $cat['ID'] . "'>" . $cat['Name'] . "</option>";
								}
								?>
							</select>
						</div>
					</div>
					<!-- End Category Type -->
					<!-- Start Visibility Field -->
					<div class="form-group form-group-lg">
						<label class="col-sm-2 control-label">Visible</label>
						<div class="col-sm-10 col-md-6">
							<div>
								<input id="vis-yes" type="radio" name="visibility" value="0" checked />
								<label for="vis-yes">Yes</label>
							</div>
							<div>
								<input id="vis-no" type="radio" name="visibility" value="1" />
								<label for="vis-no">No</label>
							</div>
						</div>
					</div>
					<!-- End Visibility Field -->
					<!-- Start Commenting Field -->
					<div class="form-group form-group-lg">
						<label class="col-sm-2 control-label">Allow Commenting</label>
						<div class="col-sm-10 col-md-6">
							<div>
								<input id="com-yes" type="radio" name="commenting" value="0" checked />
								<label for="com-yes">Yes</label>
							</div>
							<div>
								<input id="com-no" type="radio" name="commenting" value="1" />
								<label for="com-no">No</label>
							</div>
						</div>
					</div>
					<!-- End Commenting Field -->
					<!-- Start Ads Field -->
					<div class="form-group form-group-lg">
						<label class="col-sm-2 control-label">Allow Ads</label>
						<div class="col-sm-10 col-md-6">
							<div>
								<input id="ads-yes" type="radio" name="ads" value="0" checked />
								<label for="ads-yes">Yes</label>
							</div>
							<div>
								<input id="ads-no" type="radio" name="ads" value="1" />
								<label for="ads-no">No</label>
							</div>
						</div>
					</div>
					<!-- End Ads Field -->
					<!-- Start Submit Field -->
					<div class="form-group form-group-lg">
						<div class="col-sm-offset-2 col-sm-10">
							<input type="submit" value="Add Category" class="btn btn-primary btn-lg" />
						</div>
					</div>
					<!-- End Submit Field -->
				</form>
			</div>

			<?php

		} elseif ($do == 'Insert') {

			if ($_SERVER['REQUEST_METHOD'] == 'POST') { ?>

				<h1 class='text-center'>Insert Category</h1>
				<div class='container'>

					// Get Variables From The Form
					<?php
					$name 		= $_POST['name'];
					$desc 		= $_POST['description'];
					$parent 	= $_POST['parent'];
					$order 		= $_POST['ordering'];
					$visible 	= $_POST['visibility'];
					$comment 	= $_POST['commenting'];
					$ads 		= $_POST['ads'];

					// Check If Category Exist in Database

					$check = checkItem("Name", "categories", $name);

					if ($check == 1) {

						$theMsg = '<div class="alert alert-danger">Sorry This Category Is Exist</div>';

						redirectHome($theMsg, 'back');
					} else {

						// Insert Category Info In Database

						$stmt = $con->prepare("INSERT INTO 

						categories(Name, Description, parent, Ordering, Visibility, Allow_Comment, Allow_Ads)

					VALUES(:zname, :zdesc, :zparent, :zorder, :zvisible, :zcomment, :zads)");

						$stmt->execute(array(
							'zname' 	=> $name,
							'zdesc' 	=> $desc,
							'zparent' 	=> $parent,
							'zorder' 	=> $order,
							'zvisible' 	=> $visible,
							'zcomment' 	=> $comment,
							'zads'		=> $ads
						));

						// Echo Success Message
						redirectHome($initialSuccess . $stmt->rowCount() . ' Record Inserted</div>', 'back');
					}
				} else { ?>

					<div class='container'>
					<?php
					redirectHome('<div class="alert alert-danger">Sorry You Cant Browse This Page Directly</div>', 'back');

					echo
					$div;
				}

				echo
				$div;
			} elseif ($do == 'Edit') {

				// Check If Get Request catid Is Numeric & Get Its Integer Value

				$catid = isset($_GET['catid']) && is_numeric($_GET['catid']) ? intval($_GET['catid']) : 0;

				// Select All Data Depend On This ID

				$stmt = $con->prepare("SELECT * FROM categories WHERE ID = ?");

				// Execute Query

				$stmt->execute(array($catid));

				// Fetch The Data

				$cat = $stmt->fetch();

				// The Row Count

				$count = $stmt->rowCount();

				// If There's Such ID Show The Form

				if ($count > 0) { ?>

						<h1 class="text-center">Edit Category</h1>
						<div class="container">
							<form class="form-horizontal" action="?do=Update" method="POST">
								<input type="hidden" name="catid" value="<?php echo $catid ?>" />
								<!-- Start Name Field -->
								<div class="form-group form-group-lg">
									<label class="col-sm-2 control-label">Name</label>
									<div class="col-sm-10 col-md-6">
										<input type="text" name="name" class="form-control" required="required" placeholder="Name Of The Category" value="<?php echo $cat['Name'] ?>" />
									</div>
								</div>
								<!-- End Name Field -->
								<!-- Start Description Field -->
								<div class="form-group form-group-lg">
									<label class="col-sm-2 control-label">Description</label>
									<div class="col-sm-10 col-md-6">
										<input type="text" name="description" class="form-control" placeholder="Describe The Category" value="<?php echo $cat['Description'] ?>" />
									</div>
								</div>
								<!-- End Description Field -->
								<!-- Start Ordering Field -->
								<div class="form-group form-group-lg">
									<label class="col-sm-2 control-label">Ordering</label>
									<div class="col-sm-10 col-md-6">
										<input type="text" name="ordering" class="form-control" placeholder="Number To Arrange The Categories" value="<?php echo $cat['Ordering'] ?>" />
									</div>
								</div>
								<!-- End Ordering Field -->
								<!-- Start Category Type -->
								<div class="form-group form-group-lg">
									<label class="col-sm-2 control-label">Parent?</label>
									<div class="col-sm-10 col-md-6">
										<select name="parent">
											<option value="0">None</option>
											<?php
											$allCats = getAllFrom("*", "categories", "ID", "where parent = 0", "", "ASC");
											foreach ($allCats as $c) {
												echo "<option value='" . $c['ID'] . "'";
												if ($cat['parent'] == $c['ID']) {
													echo ' selected';
												}
												echo ">" . $c['Name'] . "</option>";
											}
											?>
										</select>
									</div>
								</div>
								<!-- End Category Type -->
								<!-- Start Visibility Field -->
								<div class="form-group form-group-lg">
									<label class="col-sm-2 control-label">Visible</label>
									<div class="col-sm-10 col-md-6">
										<div>
											<input id="vis-yes" type="radio" name="visibility" value="0" <?php if ($cat['Visibility'] == 0) {
																												echo 'checked';
																											} ?> />
											<label for="vis-yes">Yes</label>
										</div>
										<div>
											<input id="vis-no" type="radio" name="visibility" value="1" <?php if ($cat['Visibility'] == 1) {
																											echo 'checked';
																										} ?> />
											<label for="vis-no">No</label>
										</div>
									</div>
								</div>
								<!-- End Visibility Field -->
								<!-- Start Commenting Field -->
								<div class="form-group form-group-lg">
									<label class="col-sm-2 control-label">Allow Commenting</label>
									<div class="col-sm-10 col-md-6">
										<div>
											<input id="com-yes" type="radio" name="commenting" value="0" <?php if ($cat['Allow_Comment'] == 0) {
																												echo 'checked';
																											} ?> />
											<label for="com-yes">Yes</label>
										</div>
										<div>
											<input id="com-no" type="radio" name="commenting" value="1" <?php if ($cat['Allow_Comment'] == 1) {
																											echo 'checked';
																										} ?> />
											<label for="com-no">No</label>
										</div>
									</div>
								</div>
								<!-- End Commenting Field -->
								<!-- Start Ads Field -->
								<div class="form-group form-group-lg">
									<label class="col-sm-2 control-label">Allow Ads</label>
									<div class="col-sm-10 col-md-6">
										<div>
											<input id="ads-yes" type="radio" name="ads" value="0" <?php if ($cat['Allow_Ads'] == 0) {
																										echo 'checked';
																									} ?> />
											<label for="ads-yes">Yes</label>
										</div>
										<div>
											<input id="ads-no" type="radio" name="ads" value="1" <?php if ($cat['Allow_Ads'] == 1) {
																										echo 'checked';
																									} ?> />
											<label for="ads-no">No</label>
										</div>
									</div>
								</div>
								<!-- End Ads Field -->
								<!-- Start Submit Field -->
								<div class="form-group form-group-lg">
									<div class="col-sm-offset-2 col-sm-10">
										<input type="submit" value="Save" class="btn btn-primary btn-lg" />
									</div>
								</div>
								<!-- End Submit Field -->
							</form>
						</div>

			<?php

					// If There's No Such ID Show Error Message

				} else {

					echo $initialDiv;

					$theMsg = '<div class="alert alert-danger">Theres No Such ID</div>';

					redirectHome($theMsg);

					echo
					$div;
				}
			} elseif ($do == 'Update') {

				echo "<h1 class='text-center'>Update Category</h1>";
				echo $initialDiv;

				if ($_SERVER['REQUEST_METHOD'] == 'POST') {

					// Get Variables From The Form

					$id 		= $_POST['catid'];
					$name 		= $_POST['name'];
					$desc 		= $_POST['description'];
					$order 		= $_POST['ordering'];
					$parent 	= $_POST['parent'];

					$visible 	= $_POST['visibility'];
					$comment 	= $_POST['commenting'];
					$ads 		= $_POST['ads'];

					// Update The Database With This Info

					$stmt = $con->prepare("UPDATE 
											categories 
										SET 
											Name = ?, 
											Description = ?, 
											Ordering = ?, 
											parent = ?,
											Visibility = ?,
											Allow_Comment = ?,
											Allow_Ads = ? 
										WHERE 
											ID = ?");

					$stmt->execute(array($name, $desc, $order, $parent, $visible, $comment, $ads, $id));

					// Echo Success Message

					$theMsg = $initialSuccess . $stmt->rowCount() . ' Record Updated</div>';

					redirectHome($theMsg, 'back');
				} else {

					$theMsg = '<div class="alert alert-danger">Sorry You Cant Browse This Page Directly</div>';

					redirectHome($theMsg);
				}

				echo
				$div;
			} elseif ($do == 'Delete') {

				echo "<h1 class='text-center'>Delete Category</h1>";
				echo $initialDiv;

				// Check If Get Request Catid Is Numeric & Get The Integer Value Of It

				$catid = isset($_GET['catid']) && is_numeric($_GET['catid']) ? intval($_GET['catid']) : 0;

				// Select All Data Depend On This ID

				$check = checkItem('ID', 'categories', $catid);

				// If There's Such ID Show The Form

				if ($check > 0) {

					$stmt = $con->prepare("DELETE FROM categories WHERE ID = :zid");

					$stmt->bindParam(":zid", $catid);

					$stmt->execute();

					$theMsg = $initialSuccess . $stmt->rowCount() . ' Record Deleted</div>';

					redirectHome($theMsg, 'back');
				} else {

					$theMsg = '<div class="alert alert-danger">This ID is Not Exist</div>';

					redirectHome($theMsg);
				}

				echo '</div>';
			}

			include $tpl . 'footer.php';
		} else {

			header('Location: index.php');

			exit();
		}

		ob_end_flush(); // Release The Output

			?>