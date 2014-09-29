		<?php



//=============================================================

		function save_to_file(){

			//create some functions to carry out these objectives
			//

		}
		function write_to_file(){

		}

		$filename = 'todo_list.txt';
		$handle = fopen($filename, "r");
		$contents = fread($handle, filesize($filename));
		fclose($handle);
		//for some reason $contents returns as a boolean.
		// var_dump($contents);
		$todo_items= $contents;

//=============================================================
		?>
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="site.css">
	</head>
	<body>
		<div id="container">
			<h2>TODO Input</h2>
			<h1>GET</h1> 
				<?php var_dump($_GET); ?>
			<h1>POST</h1> 
				<?php var_dump($_POST); ?>
			<h2>TODO List</h2>

			
			<ol>			
			<?php
//=====================================================================
			if(!empty($_POST["new_item"]))
			{
				foreach ($todo_items as $items) 
				{
					fwrite($todo_items);
				}
			}

				foreach ($todo_items as $item_index => $items) {
					echo "<li>" . $items . "</li>";
				};
//=====================================================================
			?>
			</ol>

			<form method="POST">
				<p>
					<label for="new_item">Input new Item</label>
					<input type="text" name="new_item" id="new_item" />
					<input type="submit">
				</p>
			
			</form>
		</div>
	</body>
</html>
