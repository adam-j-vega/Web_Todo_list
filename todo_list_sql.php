<?php  

class UnexpectedTypeException extends Exception {
}

require_once "../inc/filestore.php";

//This opens the database todo_list_db

require_once "../php_pdo.php";

//$dbc now exists

$offset = 0;

$count = (int) ($dbc->query('SELECT count(*) FROM todo_list')->fetchColumn());


//=======================================================================

//this function returns an array that will get printed out.
function getItems($dbc,$offset) 
{

	return $dbc->query("SELECT id, list_item FROM todo_list LIMIT 10 OFFSET {$offset}")->fetchAll(PDO::FETCH_ASSOC);
}

$array = getItems($dbc,$offset);

var_dump($array);














	













//===========================================================
	
// Check for key 'additem' in POST request
if (isset($_POST['additem'])) 
{	
	// try{
		if (empty($_POST['additem']))
		{
			echo "Todo item cannot be empty"; // throw new UnexpectedTypeException("Todo item cannot be empty.");
		}
		elseif (strlen($_POST['additem'] >= 240))
		{
			echo "Todo item must be less than 240 character."; // throw new UnexpectedTypeException("Todo item must be less than 240 characters.");
		}
		else
		{
				//insert into database here
			$item[] = $_POST['additem'];

			// $stmt = $dbc->prepare('INSERT INTO todo_list (list_item) VALUES (:list_item)');
	


			foreach($item as $key => $value)
			{



				$query = "INSERT INTO todo_list (list_item) VALUES ('{$value}')";

				$dbc->exec($query);

				$array = getItems($dbc, $offset);


				// var_dump($value);
				// $stmt->bindValue(':list_item', $value, PDO::PARAM_STR);
			}
			// $stmt->execute();
			// foreach ($array as $key => $item)
			// 	{
			// 		foreach($item as $item_key => $value)
			// 	// Include anchor tag and link to perform GET request, according to $key 
			// 		echo '<p> <a href=' . "?remove=$item_key" . '>Complete</a> - ' . "$value</p>";
			// 	}

		}



		// $items[] = $_POST['additem'];


		// }catch(UnexpectedTypeException $e){
		// 	$errorMessage = $e->getMessage();
		// }

}
























// Check for key 'remove' in GET request
if (isset($_GET['remove'])) 
{

	// Define variable $keyToRemove according to value
	$item_key = $_GET['remove'];
	// Remove item from array according to key specified

	$stmt = $dbc->prepare('DELETE FROM todo_list WHERE id = :id');

	$stmt->bindValue(':id', $item_key, PDO::PARAM_INT);

	// $dbc->exec("DELETE FROM todo_list WHERE id = $item_key");

	$stmt->execute();

	$array = getItems($dbc,$offset);

}



//===========================================================








































// if (count($_FILES) > 0 && $_FILES['file1']['error'] === UPLOAD_ERR_OK) 
// {
// 	if($_FILES['file1']['type'] === 'text/plain')
// 	{
//     // Set the destination directory for uploads

//     $upload_dir = '/vagrant/sites/planner.dev/public/uploads/';

//     // Grab the filename from the uploaded file by using basename

//     $filename = basename($_FILES['file1']['name']);


//     // Create the saved filename using the file's original name and our upload directory

//     $saved_filename = $upload_dir . $filename;

//     // Move the file from the temp location to our uploads directory

//     move_uploaded_file($_FILES['file1']['tmp_name'], $saved_filename);
    
//     $uploadedList = $instance_of_Filestore->write($saved_filename);

//     $new_array = array_merge($array, $uploadedList);

//     $instance_of_Filestore->write ($new_array);
// 	}
// 	else
// 	{
// 		echo "ERROR must be a text/plain file ONLY";
// 	}
// }































// Loop through array $array and output key => value pairs
// if(isset($errorMessage))
// {
// 	echo $errorMessage;
// }


// $remove_item = 'cheese';






if(isset($array))
{
	foreach($array as $item_key => $value){
		// Include anchor tag and link to perform GET request, according to $key 

		echo "<p> <a href='?remove={$value['id']}'>Complete</a> - {$value['list_item']}</p>";
	}
}

	?>




















 
<html>
<head>
	<title>Todo List App</title>
	<title>National Parks</title>
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
	<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
</head>
<body>

 
		<h2>List of array: </h2>
		 
		<ul>
		 
		 <!-- Form to allow array to be added -->
			<form name="additem" method="POST" action="/todo_list_sql.php">
		 
			<label>Add Item: </label>
			<input type="text" id="additem" name="additem">
			<button value="submit">Add Item</button>
		 
			</form>
		<!-- Update your todo list by adding a form to allow a file to be uploaded. When a file is uploaded, move it to the uploads folder -->
		<!-- 	<form name="uploaditem" method="POST" enctype="multipart/form-data" action="/todo_list_sql.php">

			<label for="file1">Upload Item: </label>
			<input type="file" id="file1" name="file1">
			<br>
			<button value="submit">Upload Item</button> -->


		</ul>

			<div class="btn-toolbar">
				<div class="btn-group">
					<?php if($offset != 0):?>

					<button type='button' class='btn btn-default'><a href='?offset=<?=($offset-10);?>'>PREV</a></button>

					<button type='button' class='btn btn-default'><a href='$_GET offset=<?=($offset-10);?>'>PREV</a></button>

					<?endif;?>
					<?if(($offset+10)<$count):?>
					<button type="button" class="btn btn-default"><a href='?offset=<?=$offset+10;?>'>NEXT</a></button>
					<?endif;?>
				</div>
			</div>
	
<!-- ploads directory (see example in this lesson) and process the file, adding new todos to your existing todo list. -->

</body>
</html>