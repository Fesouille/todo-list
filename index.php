<?php
try
{
	//We connect to MySQL
	//$db = new PDO('mysql:host=webtech.one.mysql;dbname=webtech_one_becode;charset=utf8', 'webtech_one_becode', 'BEcode2019');
	$db = new PDO('mysql:host=localhost;dbname=todolist;charset=utf8', 'root', 'root');
}
catch(Exception $e)
{
	// In case of error, we print a message and we stop everything
        die('Erreur : '.$e->getMessage());
}
function display_todolist(){
	global $db;
	global $order;
	//We import the todos table
	$sql="SELECT id, item, DATE_FORMAT(expiring_time, '%d %M %Y') AS exp_date, DATE_FORMAT(expiring_time, '%Hh%i') AS exp_hour, isdone FROM todos WHERE isdone = false $order";
	$resultat = $db->query($sql);

	//We display the data
	while ($donnees = $resultat->fetch(PDO::FETCH_ASSOC)){
		echo "<div id='".$donnees['id']."' class='row'>";
		echo "<div class='col-1 col-md-1 text-center check-col'>
				<input type='checkbox' onchange='submitForm(this.name, this.value);' class='checkItem' name='archive' value=".$donnees['id'].">
			</div>";
		echo "<div class='dragdrop col-5 col-md-5'>".$donnees['item']."</div>";
		echo "<div class='".$donnees['id']." dragdrop date col-5 col-md-5'>".$donnees['exp_date'].", ".$donnees['exp_hour']."</div>";
		echo "<div class='col-1 col-md-1 text-center delete-col collapse multi-collapse'>
				<button type='button' class='btn btn-outline-danger btn-circle' onclick='submitForm(this.name, this.value);' name='delete' value=".$donnees['id'].">X</button>
			</div></div>";
	}
	//We close the request treatment
	$resultat->closeCursor();
}

function display_archive(){
	global $db;
	//On importe la table météo
	$sql="SELECT * FROM todos WHERE isdone = true";
	$resultat = $db->query($sql);

	//On affiche les données
	while ($donnees = $resultat->fetch(PDO::FETCH_ASSOC)){
		echo "<div class='row'>";
		echo "<div class='col-1 col-md-1 text-center check-col'>
				<input type='checkbox' checked='checked' disabled name='dearchive' value=".$donnees['id'].">
			</div>";
		echo "<div class='archived dragdrop col-10 col-md-10'>".$donnees['item']."</div>";
		echo "<div class='col-1 col-md-1 text-center delete-col collapse multi-collapse'>
				<button type='button' class='btn btn-outline-danger btn-circle' onclick='submitForm(this.name, this.value);' name='delete' value=".$donnees['id'].">X</button>
			</div></div>";
	}
	//On termine le traitement de la requête
	$resultat->closeCursor();
}

function add_item(){
	global $db;
	$item=htmlentities($_POST['value'], ENT_QUOTES);
	$date=$_POST['date'];
	$sql="INSERT INTO todos (item, expiring_time) VALUES ('$item', '$date')";
	$addrow= $db->query($sql);
}
function archive_item(){
	global $db;
	$item=$_POST['value'];
	$sql="UPDATE todos SET isdone=true WHERE id='$item'";
	$archiverow = $db->query($sql);
}
function delete_item(){
	global $db;
	$item=$_POST['value'];
	$sql="DELETE FROM todos WHERE id='$item'";
	$deleterow = $db->query($sql);
}
function delete_archive(){
	global $db;
	foreach ($_POST["dearchive"] as $key => $value) {
		$sql="DELETE FROM todos WHERE id='$value'";
		$deleterow = $db->query($sql);
	}
}
/*Function to add an ordering rule in sql query where $value is the column to consider and $rule is the ordering rule
i.e. You have a column item in your table and want to order it by Ascending order (ORDER BY item ASC) --> run order('item', 'ASC');
then declare the variable $order as global to incorporate it in the desired function. For example, see display_todolist() function
}
*/
function order($value, $rule){
	global $order;
	$order="ORDER BY $value $rule";
}
//this command orders the list by expiring date at page load --> default order
order('expiring_time', 'ASC');

if(isset($_POST["action"]) && $_POST["action"]=="archive"){
	archive_item();
}
elseif($_POST["action"]=="submit") {
	add_item();
}
elseif($_POST["action"]=="delete") {
	delete_item();
}
elseif($_POST["action"]=="delete_archive") {
	delete_archive();
}
//This is to order the sql list according to the selected rule
$rule1="asc";
$rule2="desc";
$rule3="creation";
$rule4="completion";
//var_dump($_POST);
if(isset($_POST["action"]) && $_POST["action"]=="order") {
	if($_POST["value"]==$rule1){
		order('item', 'ASC');
	}
	elseif ($_POST["value"]==$rule2) {
		order('item', 'DESC');
	}
	elseif ($_POST["value"]==$rule3) {
		order('id', 'ASC');
	}
	elseif ($_POST["value"]==$rule4) {
		order('expiring_time', 'ASC');
	}
}

//we create the date element for the default date value of the date input
$default_date=strftime('%Y-%m-%d', mktime(0,0,0, date('m'), date('d')+1, date('Y')));
$current_date=strftime('%Y-%m-%d', mktime(0,0,0, date('m'), date('d'), date('Y')));
$current_time=strftime('%H:%M', mktime(0,0,0, date('m'), date('d'), date('Y')));
?>
<!DOCTYPE html>
<html lang='en'>
  <head>
    <!-- Required meta tags -->
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>
    <link href='https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css' rel='stylesheet' integrity='sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T' crossorigin='anonymous'>
    <link rel='stylesheet' href='style.css'>
    <link rel="icon" href="./img/list.png">
    <title>Todo List</title>
  </head>

  <body>
	<div id='display_add' class='container'>
		<h2>Add a task to do</h2>
		<div class='row'>
			<div class='col'>
				<label>To do</label><br>
				<input type='text' name='item' id='item' autocomplete='off' placeholder='ex: Have a breakfast'>
			</div>
			<div class='col'>
				<label>To complete before</label><br>
				<input type='datetime-local' name='date' id='date' <?php echo "value='".$default_date."T00:00' min='".$current_date."T".$current_time."'";?>>	
			</div>
			<div class='col'>
				<button type='button' id='submit_button' class='btn btn-outline-primary' onclick='add_entry(this.name, item.value, date.value);' name='submit'>Add</button>
			</div>

		</div>
	</div>
	<br>
	<div id='display_lists' class='container'>
		<div class='row'>
			<div id='display_todo' class='col-md-7'>
				<h4>To do</h4>
				<div class='row'>
					<div class=' col-10 col-sm-10 col-md-10'>
						<label>Order by</label>
						<select name='order' onchange='submitForm(this.name, this.value);'>
							<option value=<?php echo $rule4; if($_POST["value"]==$rule4){echo " selected";}?>>Completion date</option>
							<option value=<?php echo $rule1; if($_POST["value"]==$rule1){echo " selected";}?>>Asc</option>
							<option value=<?php echo $rule2; if($_POST["value"]==$rule2){echo " selected";}?>>Desc</option>
							<option value=<?php echo $rule3; if($_POST["value"]==$rule3){echo " selected";}?>>Creation date</option>
						</select>
					</div>
					<div class="col-2 col-sm-2 col-md-2 text-right"><button type="button" class="btn btn-danger btn-sm" id="btn-delete" data-toggle="collapse" data-target=".multi-collapse" aria-expanded="false" aria-controls="delete-col">Delete</button></div>
				</div>
				<div class='row'>
					<div class='col-md-12 border border-dark rounded'>
						<div class='row table-title'>
							<div class='col-1 col-md-1 text-center check-col'><input type='checkbox' id='checkAll' title='Select all'></div>
							<div class='col-5 col-md-5'>Task</div>
							<div class='col-5 col-md-5'>To complete before</div>
							<div class='col-1 col-md-1'></div>
						</div>
						<div class="table">
							<?php
		  						display_todolist();
		  					?>	
						</div>
					</div>					
				</div>
			</div>
			<div class='offset-md-1'></div>
			<div id='display_archives' class='col-md-4'>
				<h4>Archives</h4>
				<div class='row'>
					<div class='col-md-12'>
						<p></p>
					</div>
				</div>
				<div class='row'>
					<div class='col-md-12 border border-dark rounded'>
						<div class='row table-title'>
							<div class='col-12 col-md-12'>Done</div>
						</div>
						<div class="table">						
							<?php
	  							display_archive();
	  						?>	
	  					</div>
					</div>					
				</div>			
			</div>
		</div>
	</div>
	<script src='https://code.jquery.com/jquery-3.3.1.min.js'></script> 
    <script src='https://code.jquery.com/ui/1.12.0/jquery-ui.min.js'></script>
	<script src='https://cdnjs.cloudflare.com/ajax/libs/gsap/2.1.2/TweenMax.min.js'></script>
	
	<script src='script.js'></script>
	<!-- jQuery first, then Popper.js, then Bootstrap JS -->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </body>
</html>