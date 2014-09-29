<?php

class UnexpectedTypeException extends Exception {
}

require_once('address_data_store.php');

$address_object = new AddressDataStore('address_book/address_book.csv');



//$filename should have been automatically returned from the class DefaultFileName being instantiated.
	
// // Populate array with items from file


$address_book = $address_object->read_address_book();

//==================================================Handles Input================================================
 
	// Check for key 'additem' in POST request
	if (!empty($_POST)) {
		// Add item from POST to array $address_book
		// $new_zipcode = $_POST['postToArray[]'];
		$save = true;



		try{
			foreach ($_POST as $value) {
					if (empty($value) || (strlen($value) > 125))
					{
						throw new UnexpectedTypeException("Address cannot be empty or longer than 125 characters.");
					}
			}
			$newAddress = [
				$_POST['addname'],
				$_POST['addaddress'],
				$_POST['addcity'],
				$_POST['addstate'],
				$_POST['addzip']
			];

			// $address_book = array_merge($address_book, $newAddress);
			$address_book[] = $newAddress;

			$address_object->write_address_book($address_book);
		}catch(UnexpectedTypeException $e){
		$errorMessage = $e->getMessage();
		}
	

	}

//TO DO EVENTUALLY=================================================Handles Input function================================
	// Check for key 'remove' in GET request
	if (isset($_GET['remove'])) {
		// Define variable $keyToRemove according to value
		$keyToRemove = $_GET['remove'];
		// Remove item from array according to key specified
		unset($address_book[$keyToRemove]);
		// Numerically reindex values in array after removing item
		$address_book = array_values($address_book);
		// Save to file
		$address_object->write_address_book($address_book);
	}

//**************************NOT UPDATED************************************************************************************
//*************************************************************************************************************************

if (count($_FILES) > 0 && $_FILES['addressBookFile']['error'] === UPLOAD_ERR_OK) {
    // Set the destination directory for uploads

	    $upload_dir = '/vagrant/sites/planner.dev/public/address_book/';

	    // Grab the filename from the uploaded file by using basename

	    $filename = basename($_FILES['addressBookFile']['name']);





	    // Create the saved filename using the file's original name and our upload directory

	    $saved_filename = $upload_dir . $filename;


	    //********THIS IS BAD FORM!!! ********* THIS IS BAD FORM!!!!


	    $saved_address_object = new AddressDataStore('address_book/address_book_new.csv');
	    //$object->filename = $saved_filename;

	    // Move the file from the temp location to our uploads directory

	    move_uploaded_file($_FILES['addressBookFile']['tmp_name'], $saved_filename);
	    
	    $uploadedList = $saved_address_object->read_address_book($saved_filename);
	    $address_book = array_merge($address_book, $uploadedList);
	    $saved_address_object->write_address_book($address_book);
}

//*****unset(....) INVOKES CLASS DESTRUCTORS, NOW COMMENTED OUT

// unset($address_object);
// unset($saved_address_object);

	
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
					<th>Name</th>
					<th>Address</th>
					<th>City</th>
					<th>State</th>
					<th>Zip</th>
				</tr>
					<? var_dump($address_book); ?>
					<? if(isset($errorMessage)) echo $errorMessage?>
					<? foreach($address_book as $key => $fields): ?>
					<tr>
						<td><a href="?remove=<?= $key; ?>">Remove</a></td>
						<? foreach($fields as $value): ?>
							<td><?= "$value" ?></td>
					 	<? endforeach ?>
					</tr>
				<? endforeach ?>
			</table>

			<h2>List of Items: </h2>
			 
			<ul>
			 
			 <!-- Form to allow items to be added -->
				<form name="additem" id="" method="POST" action="/address_book.php">
			 
				<label for="name">Name</label>
				<input type="text" id="name" name="addname">

				<br>

				<label for="address">Address</label>
				<input type="text" id="address" name="addaddress">
			
				<br>

				<label>City</label>
				<input type="text" id="" name="addcity">

				<br>

				<label>State</label>
				<input type="text" id="" name="addstate">
				<br>


				<label>Zip Code </label>
				<input type="text" id="" name="addzip">
				<button value="submit">Add Item</button>
				<br>
			 
				</form>
				<br>
<!-- ************************YOU NEED TO CARE ABOUT THIS -->
				<!-- THIS IS THE UPLOAD THINGY -->















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