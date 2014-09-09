<?php  
 
// Define constant for filename to read/write
define('FILENAME', 'list.txt');
 
/* -------------------------------------- */
 
// OPEN FILE TO POPULATE LIST
 
function open_file($filename = FILENAME) {

	if (filesize($filename) == 0) {
		$filesize = 100;
	}

	else {
		$filesize = filesize($filename);
	}

    $handle = fopen($filename, 'r');
    $contents = trim(fread($handle, $filesize));
    $list = explode("\n", $contents);
    fclose($handle);
    return $list;
}
 
/* -------------------------------------- */
 
// SAVE LIST TO FILENAME



function save_to_file($list, $filename = FILENAME) 
{
	
 
    $handle = fopen($filename, 'w');
    foreach ($list as $item) {
        fwrite($handle, $item . PHP_EOL);
    }
    fclose($handle);        
}
 
/* -------------------------------------- */
 
// Initialize variable as an empty array for list items
$items = []; 
 
// Populate array with items from file
$items = open_file();

?>
 
<html>
<head>
	<title>Todo List App</title>
</head>
<body>
 
<h2>List of Items: </h2>
 
<ul>
 
	<?php
 
		// Check for key 'additem' in POST request
		if (isset($_POST['additem'])) {
			// Add item from POST to array $items
			$items[] = $_POST['additem'];
			// Save array of items to file
			save_to_file($items);
		}
 
		// Check for key 'remove' in GET request
		if (isset($_GET['remove'])) {
			// Define variable $keyToRemove according to value
			$keyToRemove = $_GET['remove'];
			// Remove item from array according to key specified
			unset($items[$keyToRemove]);
			// Numerically reindex values in array after removing item
			$items = array_values($items);
			// Save to file
			save_to_file($items);
		}
 
		



	if (count($_FILES) > 0 && $_FILES['file1']['error'] === UPLOAD_ERR_OK) {
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
        
        $uploadedList = open_file($saved_filename);
        $items = array_merge($items, $uploadedList);
        save_to_file($items);
    	}
    	else
    	{
    		echo "ERROR must be a text/plain file ONLY";
    	}
    }

    // Loop through array $items and output key => value pairs
		foreach ($items as $key => $item) {
			// Include anchor tag and link to perform GET request, according to $key 
			echo '<li> <a href=' . "?remove=$key" . '>Complete</a> - ' . "$item</li>";
		}
 
	?>
 
</ul>
 
 <!-- Form to allow items to be added -->
	<form name="additem" method="POST" action="/todo_list_model.php">
 
	<label>Add Item: </label>
	<input type="text" id="additem" name="additem">
	<button value="submit">Add Item</button>
 
	</form>

	<form name="uploaditem" method="POST" enctype="multipart/form-data" action="/todo_list_model.php">
<!-- Update your todo list by adding a form to allow a file to be uploaded. When a file is uploaded, move it to the uploads folder -->
	<label for="file1">Upload Item: </label>
	<input type="file" id="file1" name="file1">
	<br>
	<button value="submit">Upload Item</button>



<!-- ploads directory (see example in this lesson) and process the file, adding new todos to your existing todo list. -->
 
</body>
</html>