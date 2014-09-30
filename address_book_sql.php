<?php

// class UnexpectedTypeException extends Exception {
// }
 
//move php to a seperate required file.

require_once "../address_book_php_pdo.php";

//attempt in future * maintain pagination before going back to main page
// if(isset($_GET['back']))
// {
// 	$num = $_GET['page'];
// 	$offset = ($num - 1) * 10;
// 	$address_book = read_addresses_db($dbc, $offset);
// }
// else
// {
// 	$offset = 0;
// }

$offset = 0;

$items_per_page = 10;

function read_addresses_db($dbc, $offset, $items_per_page)
{
	return $dbc->query("SELECT n.id,CONCAT(n.first_name, ' ', n.last_name) 
		AS full_name, n.phone, a.address, a.city, a.state, a.zip 
		FROM names n
		JOIN names_address na
			ON na.name_id = n.id
		JOIN addresses a
			ON a.id = na.address_id
		LIMIT $items_per_page OFFSET {$offset}")->fetchAll(PDO::FETCH_ASSOC);

}

$address_book = read_addresses_db($dbc, $offset, $items_per_page);

//===============================================================================

//pagination

function countItems($dbc) 
{
	return $dbc->query('SELECT COUNT(*) FROM names')->fetchColumn();
}

$page_id = 1;



$max_pages = ceil(countItems($dbc) / $items_per_page);

if(!empty($_GET['page']))
{
	$page_id = $_GET['page'];

	$offset = ($page_id - 1) * $items_per_page;

	$address_book = read_addresses_db($dbc, $offset, $items_per_page);
}

//===============================================================================

//adds items to addresses database

if(!empty($_POST))
{
	// if(!is_int($_POST['addPhone']))
	// {
	// 	echo "Item must be a number";
	// }
	// elseif(!is_int($_POST['addZip']))
	// {
	// 	echo "Item must be a number";
	// }
	// else
	// {
		$stmt_1 = $dbc->prepare("INSERT INTO names (first_name, last_name, phone) VALUES (:first_name, :last_name, :phone)");
		
		$stmt_1->bindValue(':first_name', $_POST['addFirstname'], PDO::PARAM_STR);
		$stmt_1->bindValue(':last_name', $_POST['addLastname'], PDO::PARAM_STR);
		$stmt_1->bindValue(':phone', $_POST['addPhone'], PDO::PARAM_STR);
		
		$stmt_2 = $dbc->prepare("INSERT INTO addresses (address, city, state, zip) VALUES (:address, :city, :state, :zip);");

		$stmt_2->bindValue(':address', $_POST['addAddress'], PDO::PARAM_STR);
		$stmt_2->bindValue(':city', $_POST['addCity'], PDO::PARAM_STR);
		$stmt_2->bindValue(':state', $_POST['addState'], PDO::PARAM_STR);
		$stmt_2->bindValue(':zip', $_POST['addZip'], PDO::PARAM_STR);

		$stmt_1->execute();
		$nameId = $dbc->lastInsertId();

		//**Ben says used named paramaters

		$stmt_2->execute();
		$addressId = $dbc->lastInsertId();

		//execute returns an id with some sort of function last inserted item

		$dbc->query("INSERT INTO names_address (name_id, address_id) VALUES ($nameId, $addressId)");

		$address_book = read_addresses_db($dbc, $offset, $items_per_page);
	// }
}

//==============================================================================

//remove item from database

if(!empty($_GET['remove']))
{
	$idToRemove = $_GET['remove'];

	//use a prepared statement here
	$stmt_1 = $dbc->prepare("DELETE FROM names WHERE id = :id");
	$stmt_2 = $dbc->prepare("DELETE FROM addresses WHERE id = :id");
	$stmt_3 = $dbc->prepare("DELETE FROM names_address WHERE id = :id");

	$stmt_1->bindValue(':id', $idToRemove, PDO::PARAM_STR);
	$stmt_2->bindValue(':id', $idToRemove, PDO::PARAM_STR);
	$stmt_3->bindValue(':id', $idToRemove, PDO::PARAM_STR);

	$stmt_1->execute();
	$stmt_2->execute();
	$stmt_3->execute();

	$address_book = read_addresses_db($dbc, $offset, $items_per_page);
}
?>
 
<html>
	<head>
		<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
		<title>Address Book</title>
		<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
	    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

	</head>
	<body>
		<div class="container">
		
			<table  id="tableToOutput">
				<tr>
					<th>Go To</th>
	 				<th>Index</th>
					<th>Name</th>
					<th>Phone Number</th>
					<th>Address</th>
					<th>City</th>
					<th>State</th>
					<th>Zip Code</th>
				</tr>

					<? if(isset($address_book)): ?>
						<? foreach($address_book as $key => $fields): ?>
						<tr>
							<td><a href='address_book_sql_info.php?id=<?= $fields['id']  ?>'>info</a></td>
							<td><?= $fields['id'] ?></td>
							<td><?= $fields['full_name'] ?></td>
							<td><?= $fields['phone'] ?></td>
							<td><?= $fields['address'] ?></td>
							<td><?= $fields['city'] ?></td>
							<td><?= $fields['state'] ?></td>
							<td><?= $fields['zip'] ?></td>
						</tr>
						<? endforeach ?>
				<? endif ?>
			</table>
			<div class="btn-group">
				<? if($page_id > 1): ?>
				<button type="button"  class="btn btn-default">
			  		<a href="?page=<?= ($page_id - 1) ?>">Previous</a>
			  	</button>
				<? endif ?>
				<? if ($page_id < $max_pages): ?>
				<button type="button" class="btn btn-default">
			  		<a href="?page=<?= ($page_id + 1) ?>">Next</a>
				</button>
				<? endif ?>
			</div>

			<!-- this is the dropdown menu -->
			<div class="dropdown">
			  <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown">
			    Remove Item
			    <span class="caret"></span>
			  </button>
			  <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
			  	<? if(isset($address_book)): ?>
			  		<? foreach($address_book as $key => $fields): ?>
					    <li role="presentation"><a role="menuitem" tabindex="-1" href="?remove=<?= $fields['id']; ?>"><?= "$fields[full_name]" ?></a></li>
					<? endforeach ?>
				<? endif ?>
			  </ul>
			</div>

			<h2>Add Address: </h2>
			 
			<ul>
			 
			 <!-- Form to allow items to be added -->
				<form name="additem" id="" method="POST" action="address_book_sql.php">
			 
				<label for="first_name">First Name</label>
				<input require type="text" id="first_name" name="addFirstname">

				<br>

				<label for="last_name">Last Name</label>
				<input require type="text" id="last_name" name="addLastname">

				<br>

				<label for="phone">Phone</label>
				<input require type="text" id="phone" name="addPhone">
			
				<br>

				<label for='address'>Address</label>
				<input require type="text" id="address" name="addAddress">

				<br>

				<label>City</label>
				<input require type="text" id="city" name="addCity">
				<br>

				<label for='State'>State</label>
				<input require type="text" id="state" name="addState">
				<br>

				<label for='zip'>Zip</label>
				<input require type="text" id="zip" name="addZip">
				<br>

				<button value="submit">Add Item</button>
				<br>

				</form>

				<br>

			</ul>
	    </div>
	</body>
</html>