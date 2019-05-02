//Makes vibrate all tasks that are expired and not yet done
var shake_interval=setInterval(shake,2500);

/*Checks every second if a task is expired*/
var interval=setInterval(check_date,100);


/*declaration of variables*/
	//array to stock all the expiring dates from the list and the IDs of their HTML elements
var dates=[];
var dates_id=[];

/*Action of the 'Select all button'*/
select_all();
function select_all(){
	document.querySelector("#checkAll").addEventListener("click", function(){
	    var checkValue=document.querySelector("#checkAll").checked;
	    var checkItemBox=document.querySelectorAll(".checkItem");
	    checkItemBox.forEach(function(element, index){
	        element.checked=checkValue;
	    });
	});
}
/*generate the list of dates when page loads, to perform a first check*/
get_dates();

//Use of Ajax --> creation of XML instance
function createInstance()
{
  var xhttp = null;
  if(window.XMLHttpRequest) {
    xhttp = new XMLHttpRequest();
  }
  else if (window.ActiveXObject) {
    try {
      xhttp = new ActiveXObject("Msxml2.XMLHTTP");
    } catch (e) {
       try {
        xhttp = new ActiveXObject("Microsoft.XMLHTTP");
        } catch (e) {
            alert("XHR not created");
          }
      }
    }
    return xhttp;
}
//This is to update the lists displayed via AJAX
function submitForm(action, item_value, date_value){
    var data="action="+action+"&value="+item_value+"&date="+date_value;
    var xhttp = createInstance();
    xhttp.onreadystatechange = function()
    { 
      if(xhttp.readyState == 4)
      {
         if(xhttp.status == 200)
         {
            document.querySelector("body").innerHTML ="";
            document.querySelector("body").innerHTML = xhttp.responseText;
            $('.table').sortable();
            get_dates();
            select_all();
         }  
         else   
         {
            document.ajax.dyn.value="Error: returned status code " + xhttp.status + " " + xhttp.statusText;
         }  
      } 
    }; 
    xhttp.open("POST", "index.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send(data);
 }
	//compares all expiring dates with the date of today
function check_date(){
		//creation of the current date-time(sets the seconds to 00)
	var today=new Date();
	today.setSeconds(0);

	dates.forEach(function(element, index){
	var itemLine=document.getElementById(dates_id[index]);
	var item=itemLine.children[1].innerHTML;
	var exp_date=new Date(element);
		if(today>=exp_date && !itemLine.classList.contains("expired")){
			itemLine.classList.add("expired");
			alert("Please complete the following task now !!!\n"+item.toUpperCase());
		}
	})
}

 /*Gets the expiring dates of the tasks and transforms it into javascript format*/
function get_dates(){
	//resets the arrays to empty them, in order to refill them from scratch
	dates=[];
	dates_id=[];
	//gets the expiring dates from the html in the SQL format and the IDs of their HTML elements
	var sql_dates=document.querySelectorAll(".date");
	sql_dates.forEach(function(element, index){
		dates.push(element.innerHTML.split(' '));
		dates_id.push(element.classList[0])
	})
	//transforms the displayed SQL format into javascript format
	dates.forEach(function(element, index){
		//transforms the month from String format into two-digits format
		element[1]=get_month(element[1]);
		//removes the ','
		element[2]=element[2].replace(",", "");
		//replaces the 'h' by ':'
		element[3]=element[3].replace("h", ":");
		//concatenates all dates elements into one string according to the javascript format
		var x=element[1]+"/"+element[0]+"/"+element[2]+" "+element[3]+":00";
		dates[index]=x;
	});

}

/* This is to add an entry to the list
--> Empty the input field
--> Call the function submitForm() to update the list via AJAX
*/
 function add_entry(action, item_value, date_value){
    //resets the input field
    document.getElementById('item').value="";
    //transforms the html datetime format (yyyy-mm-ddThh:mm) type into SQL datetime format (yyyy-mm-dd hh:mm:ss)
    date_value=date_value.replace("T", " ")+":00";
    //resets the default value of the date and time
    var default_date=document.getElementById('date').defaultValue;
    document.getElementById('date').value=default_date;
    //submits the form with the entered input
    submitForm(action, ucfirst(item_value), date_value);
 }

//to shake the expired tasks
function shake(){
	TweenMax.to('.expired', 0.1, {x:"+=15", yoyo:true, repeat:5});
	TweenMax.to('.expired', 0.1, {x:"-=15", yoyo:true, repeat:5});
}

//to transform the first letter into upper case
function ucfirst(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}
 /*This is to drag and drop the tasks, using JQUERY*/
$('.table').sortable();

/*Converts the months in string format into two-digits format*/
function get_month(month){
	var m;
	switch (month){
		case 'January':
			m='01';
			break;
		case 'February':
			m='02';
			break;
		case 'March':
			m='03';
			break;
		case 'April':
			m='04';
			break;
		case 'May':
			m='05';
			break;
		case 'June':
			m='06';
			break;
		case 'July':
			m='07';
			break;
		case 'August':
			m='08';
			break;
		case 'September':
			m='09';
			break;
		case 'October':
			m='10';
			break;
		case 'November':
			m='11';
			break;
		case 'December':
			m='12';
			break;
	}
	return m;
}