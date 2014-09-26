<?php

// class UnexpectedTypeException extends Exception {
// }
 
require_once "../address_book_php_pdo.php";

//$filename should have been automatically returned from the class DefaultFileName being instantiated.
	
// // Populate array with items from file

//read from database 

			//this is where $offset is written into WHERE after JOIN ON

function read_addresses_db($dbc){
	return $dbc->query("SELECT n.id,CONCAT(n.first_name, ' ', n.last_name) 
		AS full_name, n.phone, a.address, a.city, a.state, a.zip 
		FROM names n
		JOIN names_address na
			ON na.name_id = n.id
		JOIN addresses a
			ON a.id = na.address_id")->fetchAll(PDO::FETCH_ASSOC);

}

$offset = 0;

$address_book = read_addresses_db($dbc);

if(isset($address_book))
	{
		var_dump($address_book);
	}

//===============================================================================

//adds items to addresses database

if(!empty($_POST))
{
	$stmt_1 = $dbc->prepare("INSERT INTO names (first_name, last_name, phone) VALUES (:first_name, :last_name, :phone)");
	
	$stmt_1->bindvalue(':first_name', $_POST['addFirstname'], PDO::PARAM_STR);
	$stmt_1->bindvalue(':last_name', $_POST['addLastname'], PDO::PARAM_STR);
	$stmt_1->bindvalue(':phone', $_POST['addPhone'], PDO::PARAM_STR);
	
	$stmt_2 = $dbc->prepare("INSERT INTO addresses (address, city, state, zip) VALUES (:address, :city, :state, :zip);");

	$stmt_2->bindvalue(':address', $_POST['addAddress'], PDO::PARAM_STR);
	$stmt_2->bindvalue(':city', $_POST['addCity'], PDO::PARAM_STR);
	$stmt_2->bindvalue(':state', $_POST['addState'], PDO::PARAM_STR);
	$stmt_2->bindvalue(':zip', $_POST['addZip'], PDO::PARAM_STR);

	$stmt_1->execute();
	$nameId = $dbc->lastInsertId();

	$stmt_2->execute();
	$addressId = $dbc->lastInsertId();

	//execute returns an id with some sort of function last inserted item

	$dbc->query("INSERT INTO names_address (name_id, address_id) VALUES ($nameId, $addressId)");
	$address_book = read_addresses_db($dbc);
}






//removed item from database

if(!empty($_GET))
{
	var_dump($_GET);
	foreach($address_book as $key => $fields)
	{
		$idToRemove = $_GET['remove'];
		$dbc->query("DELETE FROM names WHERE id = $idToRemove;");
	}
	$address_book = read_addresses_db($dbc);
}







































































// if (count($_FILES) > 0 && $_FILES['addressBookFile']['error'] === UPLOAD_ERR_OK) {
//     // Set the destination directory for uploads

// 	    $upload_dir = '/vagrant/sites/planner.dev/public/address_book/';

// 	    // Grab the filename from the uploaded file by using basename

// 	    $filename = basename($_FILES['addressBookFile']['name']);





// 	    // Create the saved filename using the file's original name and our upload directory

// 	    $saved_filename = $upload_dir . $filename;


// 	    //********THIS IS BAD FORM!!! ********* THIS IS BAD FORM!!!!


// 	    $saved_address_object = new AddressDataStore('address_book/address_book_new.csv');
// 	    //$object->filename = $saved_filename;

// 	    // Move the file from the temp location to our uploads directory

// 	    move_uploaded_file($_FILES['addressBookFile']['tmp_name'], $saved_filename);
	    
// 	    $uploadedList = $saved_address_object->read_address_book($saved_filename);
// 	    $address_book = array_merge($address_book, $uploadedList);
// 	    $saved_address_object->write_address_book($address_book);
// }



































	
?>
 
<html>
	<head>
		<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
		<title>Address Book</title>
	</head>
	<body>
		<div class="container">
		
			<table  id="tableToOutput">
				<tr>
					<th>Remove</th>
	 				<th>Index</th>
					<th>Name</th>
					<th>Phone Number</th>
					<th>Address</th>
					<th>City</th>
					<th>State</th>
					<th>Zip Code</th>
				</tr>
<!-- 					<? var_dump($address_book); ?> -->
<!-- 					<? if(isset($errorMessage)) echo $errorMessage?> -->
							<? if(isset($address_book)): ?>
								<? foreach($address_book as $key => $fields): ?>
								<tr>
									<td><a href="?remove=<?= $fields['id']; ?>">Remove</a></td>
										<td><?= "$fields[id]" ?></td>
										<td><?= "$fields[full_name]" ?></td>
										<td><?= "$fields[phone]" ?></td>
										<td><?= "$fields[address]" ?></td>
										<td><?= "$fields[city]" ?></td>
										<td><?= "$fields[state]" ?></td>
										<td><?= "$fields[zip]" ?></td>
								</tr>
								<? endforeach ?>
						<? endif ?>
			</table>

			<h2>List of Items: </h2>
			 
			<ul>
			 
			 <!-- Form to allow items to be added -->
				<form name="additem" id="" method="POST" action="address_book_sql.php">
			 
				<label for="first_name">First Name</label>
				<input type="text" id="first_name" name="addFirstname">

				<br>

				<label for="last_name">Last Name</label>
				<input type="text" id="last_name" name="addLastname">

				<br>

				<label for="phone">Phone</label>
				<input type="text" id="phone" name="addPhone">
			
				<br>

				<label for='address'>Address</label>
				<input type="text" id="address" name="addAddress">

				<br>

				<label>City</label>
				<input type="text" id="city" name="addCity">
				<br>

				<label for='State'>State</label>
				<input type="text" id="state" name="addState">
				<br>

				<label for='zip'>Zip</label>
				<input type="text" id="zip" name="addZip">
				<br>

				<button value="submit">Add Item</button>
				<br>

				</form>

				<br>






























<!-- ================================================================================================================================= -->

				<form name="uploadItem" method="POST" enctype="multipart/form-data" action="/address_book.php">
				<label for="addresBookFile">Upload Item: </label>
				<input type="file" id="addressBookFile" name="addressBookFile">
				<br>
				<button value="submit">Upload Item</button>
				<form>
				</form>









			</ul>
	    </div>
	</body>
</html>