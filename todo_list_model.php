<?php  
 
// Define constant for filename to read/write
// define('FILENAME', 'list.txt');
 
/* -------------------------------------- */

class UnexpectedTypeException extends Exception {
}

require_once "../inc/filestore.php";

require_once "../php_pdo.php"

$instance_of_Filestore = new Filestore('list.txt');

$array = $instance_of_Filestore->read();




//===========================================================
	
// Check for key 'additem' in POST request
if (isset($_POST['additem'])) 
{	
	try{
		if (empty($_POST['additem']))
		{
			throw new UnexpectedTypeException("Todo item cannot be empty.");
		}
		elseif (strlen($_POST['additem'] <= 240))
		{
			throw new UnexpectedTypeException("Todo item must be less than 240 characters.");
		}

		$items[] = $_POST['new_item'];


	}catch(UnexpectedTypeException $e){
		$errorMessage = $e->getMessage();
	}
 	// Add item from POST to array $array

	if(!isset($errorMessage)) {
		$array[] = $_POST['additem'];
		$instance_of_Filestore->write($array);
	}
}
// Check for key 'remove' in GET request
if (isset($_GET['remove'])) 
{
	// Define variable $keyToRemove according to value
	$keyToRemove = $_GET['remove'];
	// Remove item from array according to key specified
	unset($array[$keyToRemove]);
	// Numerically reindex values in array after removing item
	$array = array_values($array);
	$instance_of_Filestore->write($array);
}

//===========================================================


if (count($_FILES) > 0 && $_FILES['file1']['error'] === UPLOAD_ERR_OK) 
{
	if($_FILES['file1']['type'] === 'text/plain')
	{
    // Set the destination directory for uploads

    $upload_dir = '/vagrant/sites/planner.dev/public/uploads/';

    // Grab the filename from the uploaded file by using basename

    $filename = basename($_FILES['file1']['name']);


    // Create the saved filename using the file's original name and our upload directory

    $saved_filename = $upload_dir . $filename;

    // Move the file from the temp location to our uploads directory

    move_uploaded_file($_FILES['file1']['tmp_name'], $saved_filename);
    
    $uploadedList = $instance_of_Filestore->write($saved_filename);

    $new_array = array_merge($array, $uploadedList);

    $instance_of_Filestore->write ($new_array);
	}
	else
	{
		echo "ERROR must be a text/plain file ONLY";
	}
}

// Loop through array $array and output key => value pairs
if(isset($errorMessage))
{
	echo $errorMessage;
}
foreach ($array as $key => $item)
	{
	// Include anchor tag and link to perform GET request, according to $key 
		echo '<p> <a href=' . "?remove=$key" . '>Complete</a> - ' . "$item</p>";
	}

	?>
 
<html>
<head>
	<title>Todo List App</title>
</head>
<body>
 
<h2>List of array: </h2>
 
<ul>
 
 <!-- Form to allow array to be added -->
	<form name="additem" method="POST" action="/todo_list_model.php">
 
	<label>Add Item: </label>
	<input type="text" id="additem" name="additem">
	<button value="submit">Add Item</button>
 
	</form>
<!-- Update your todo list by adding a form to allow a file to be uploaded. When a file is uploaded, move it to the uploads folder -->
	<form name="uploaditem" method="POST" enctype="multipart/form-data" action="/todo_list_model.php">

	<label for="file1">Upload Item: </label>
	<input type="file" id="file1" name="file1">
	<br>
	<button value="submit">Upload Item</button>
	<form>
	</form>

</ul>

<!-- ploads directory (see example in this lesson) and process the file, adding new todos to your existing todo list. -->
 
</body>
</html>