<?php

require_once "../address_book_php_pdo.php";

function read_addresses_db($dbc)
{
	return $dbc->query("SELECT n.id,CONCAT(n.first_name, ' ', n.last_name) 
		AS full_name, n.phone, a.address, a.city, a.state, a.zip 
		FROM names n
		JOIN names_address na
			ON na.name_id = n.id
		JOIN addresses a
			ON a.id = na.address_id")->fetchAll(PDO::FETCH_ASSOC);
}

$address_book = read_addresses_db($dbc);

// echo "you are where you want to be";

if(!empty($_GET))
{
	$var = (int) $_GET['id'];

	$stmt = $dbc->prepare("SELECT n.id,CONCAT(n.first_name, ' ', n.last_name) 
		AS full_name, n.phone, a.address, a.city, a.state, a.zip 
		FROM names n
		JOIN names_address na
			ON na.name_id = n.id
		JOIN addresses a
			ON a.id = na.address_id
		WHERE n.id = :id");

	$stmt->bindValue(':id', $var, PDO::PARAM_INT);

	$stmt->execute();
	
	$address_info = $stmt->fetchAll(PDO::FETCH_ASSOC);

	$stmt_2 = $dbc->prepare("SELECT n.id,CONCAT(n.first_name, ' ', n.last_name) 
		AS full_name, n.phone, a.address, a.city, a.state, a.zip 
		FROM names n
		JOIN names_address na
			ON na.name_id = n.id
		JOIN addresses a
			ON a.id = na.address_id
		WHERE a.address = :address");

	$stmt_3 = $dbc->prepare("SELECT a.address 
		FROM addresses a
		JOIN names_address na
			ON na.address_id = a.id 
		JOIN names n
			ON n.id = na.address_id
		WHERE n.id = :id");

	$stmt_3->bindValue(':id', $var, PDO::PARAM_INT);

	$stmt_3->execute();
	$addresses = $stmt_3->fetchall(PDO::FETCH_ASSOC);

	foreach ($addresses as $address_array)
	{
		$address = $address_array['address'];
	}

	$stmt_2->bindValue(':address', $address, PDO::PARAM_STR);

	$stmt_2->execute();

	$address_list = $stmt_2->fetchall(PDO::FETCH_ASSOC);
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
		<table  id="tableToOutput">
			<tr>
				<th>Name</th>
				<th>Phone Number</th>
				<th>Address</th>
				<th>City</th>
				<th>State</th>
				<th>Zip Code</th>
			</tr>
				<? if(isset($address_book)): ?>
					<? foreach($address_info as $key => $fields): ?>
					<tr>
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

<!-- //table to display addresses similar to user  -->

		<table  id="tableToOutput">
			<h2>People With Same Address</h2>
			<tr>
				<th>Name</th>
				<th>Phone Number</th>
				<th>Address</th>
				<th>City</th>
				<th>State</th>
				<th>Zip Code</th>
			</tr>
				<? if(isset($address_book)): ?>
					<? foreach($address_list as $key => $fields): ?>
					<tr>
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
		<a href="address_book_sql.php?back=<?= $_GET['id'] ?> ">Back</a>
	</body>
</html>