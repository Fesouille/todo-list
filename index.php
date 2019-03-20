<?php
try
{
	// On se connecte à MySQL
	$db = new PDO('mysql:host=localhost;dbname=todolist;charset=utf8', 'root', 'root');
}
catch(Exception $e)
{
	// En cas d'erreur, on affiche un message et on arrête tout
        die('Erreur : '.$e->getMessage());
}

function display_todolist(){
	global $db;
	//On importe la table météo
	$resultat = $db->query('SELECT * FROM todos WHERE isdone = false');

	//On affiche les données
	echo "<table style='border: 1px solid black; padding-right:25px; padding-left:25px'>
			<tr>
				<th><input type='checkbox' id='checkAll' title='Select all'> Select</th>
				<th>To Do</th>
			</tr>";
	while ($donnees = $resultat->fetch(PDO::FETCH_ASSOC)){
		echo "<tr>";
		echo "<td><input type='checkbox' class='checkItem' name='checked[]' value=".$donnees['id']."></td>";
		echo "<td>".$donnees['item']."</td>";
	  	echo "</tr>";
	}
	echo "</table>";
	//On termine le traitement de la requête
	$resultat->closeCursor();
}

function display_archive(){
	global $db;
	//On importe la table météo
	$resultat = $db->query('SELECT * FROM todos WHERE isdone = true');

	//On affiche les données
	echo "<table style='border: 1px solid black; padding-right:25px; padding-left:25px'>
			<tr>
				<th colspan='2'>Done</th>
			</tr>";
	while ($donnees = $resultat->fetch(PDO::FETCH_ASSOC)){
		echo "<tr>";
		echo "<td><input type='checkbox' checked='checked' disabled name='dearchive[]' value=".$donnees['id']."></td>";
		echo "<td class='archived'>".$donnees['item']."</td>";
	  	echo "</tr>";
	}
	echo "</table>";
	//On termine le traitement de la requête
	$resultat->closeCursor();
}

function add_item(){
	global $db;
	$item=$_POST['item'];
	$sql="INSERT INTO todos (item) VALUES ('$item')";
	$addrow= $db->query($sql);
}
function archive_item(){
	global $db;
	foreach ($_POST["checked"] as $key => $value) {
		$sql="UPDATE todos SET isdone=true WHERE id='$value'";
		$deleterow = $db->query($sql);
	}
}
function delete_item(){
	global $db;
	foreach ($_POST["checked"] as $key => $value) {
		$sql="DELETE FROM todos WHERE id='$value'";
		$deleterow = $db->query($sql);
	}
}
function delete_archive(){
	global $db;
	foreach ($_POST["dearchive"] as $key => $value) {
		$sql="DELETE FROM todos WHERE id='$value'";
		$deleterow = $db->query($sql);
	}
}
if(isset($_POST["archive"])){
	archive_item();
}
elseif(isset($_POST["submit"])) {
	add_item();
}
elseif(isset($_POST["delete"])) {
	delete_item();
}
elseif(isset($_POST["delete_archive"])) {
	delete_archive();
}
//$deleterow= $db->query('DELETE FROM `Météo` WHERE ville=\'Gembloux\'');
//$deleterow= $db->query('DELETE FROM `Météo` WHERE haut=36');
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="style.css">
    <title>Todo List</title>
  </head>

  <body>
  	<form method="post" action="index.php">
		<div>
			<h4>To do</h4>
			<?php
  				display_todolist();
  			?>
  			<p>
				<button type="submit" name="archive">Archive</button>
				<button type="submit" name="delete">Delete</button>
			</p>
		</div>
		<div>
			<h4>Archives</h4>
			<?php
  				display_archive();
  			?>
  			<!--<p><button type="submit" name="delete_archive">Delete archives</button></p>-->
		</div>
		<hr>
		<div>
			<h2>Ajouter un tâche</h2>
			<label>La tâche à effectuer</label><br>
			<input type="text" name="item" id="item" autocomplete="off" placeholder="ex: Aller aux toilettes">
			<button type="submit" name="submit">Ajouter</button>
		</div>
	</form>
	<script src="script.js"></script>
  </body>
</html>