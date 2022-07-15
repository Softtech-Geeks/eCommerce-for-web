<?php
ob_start();
session_start();
$pageTitle = 'Profile';
include 'init.php';
if (isset($_SESSION['user'])) {
	$getUser = $con->prepare("SELECT * FROM users WHERE Username = ?");
	$getUser->execute(array($sessionUser));
	$info = $getUser->fetch();
	$userid = $info['UserID'];
?>
	<h1 class="text-center">My Profile</h1>
	<div class="information block">
		<div class="container">
			<div class="panel panel-primary">
				<div class="panel-heading">My Information</div>
				<div class="panel-body">
					<ul class="list-unstyled">
						<li>
							<em class="fa fa-unlock-alt fa-fw"></em>
							<span>Login Name</span> : <?php echo $info['Username'] ?>
						</li>
						<li>
							<em class="fa fa-envelope-o fa-fw"></em>
							<span>Email</span> : <?php echo $info['Email'] ?>
						</li>
						<li>
							<em class="fa fa-user fa-fw"></em>
							<span>Full Name</span> : <?php echo $info['FullName'] ?>
						</li>
						<li>
							<em class="fa fa-calendar fa-fw"></em>
							<span>Registered Date</span> : <?php echo $info['Date'] ?>
						</li>
						<li>
							<em class="fa fa-tags fa-fw"></em>
							<span>Fav Category</span> :
						</li>
					</ul>
					<a href="#" class="btn btn-default">Edit Information</a>
				</div>
			</div>
		</div>
	</div>
	<div id="my-ads" class="my-ads block">
		<div class="container">
			<div class="panel panel-primary">
				<div class="panel-heading">My Items</div>
				<div class="panel-body">
					<?php
					$myItems = getAllFrom("*", "items", "Item_ID", "where Member_ID = $userid", "");
					if (!empty($myItems)) {
						echo '<div class="row">';
						foreach ($myItems as $item) { ?>
							<div class="col-sm-6 col-md-3">
								<div class="thumbnail item-box">
									<?php if ($item['Approve'] == 0) {
										echo '<span class="approve-status">Waiting Approval</span>';
									} ?>
									<span class="price-tag"><?php echo $item['Price'] ?></span>
									<img class="img-responsive" src="img.png" alt="" />
									<div class="caption">
										<h3><a href="items.php?itemid=<?php echo $item['Item_ID'] ?>"><?php echo $item['Name'] ?></a></h3>
										<p><?php echo $item['Description'] ?></p>
										<div class="date"><?php echo $item['Add_Date'] ?></div>
									</div>
								</div>
							</div>
					<?php }
						echo '</div>';
					} else {
						echo 'Sorry There\' No Ads To Show, Create <a href="newad.php">New Ad</a>';
					}
					?>
				</div>
			</div>
		</div>
	</div>
	<div class="my-comments block">
		<div class="container">
			<div class="panel panel-primary">
				<div class="panel-heading">Latest Comments</div>
				<div class="panel-body">
					<?php
					$myComments = getAllFrom("comment", "comments", "c_id", "where user_id = $userid", "");
					if (!empty($myComments)) {
						foreach ($myComments as $comment) {
							echo '<p>' . $comment['comment'] . '</p>';
						}
					} else {
						echo 'There\'s No Comments to Show';
					}
					?>
				</div>
			</div>
		</div>
	</div>
<?php
} else {
	header('Location: login.php');
	exit();
}
include $tpl . 'footer.php';
ob_end_flush();
?>