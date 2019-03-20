//var btn=document.querySelector('#button');
//btn.disabled=true;
//var inputs=document.querySelectorAll(".input");

/*Array.from(inputs).forEach(function(input) {
        input.addEventListener("input", function() {
            console.log(input);
            if(inputs[0].value=="" || inputs[1].value==""){
            	btn.disabled=true;
            }
            else if(inputs[0].value=="" || inputs[1].value==" "){
            	btn.disabled=true;
            }
            else if(inputs[0].value==" " || inputs[1].value==""){
            	btn.disabled=true;
            }
            else if(inputs[0].value==" " || inputs[1].value==" "){
            	btn.disabled=true;
            }
            else{
            	btn.disabled=false;
            }
        });
    });
*/

document.querySelector("#checkAll").addEventListener("click", function(){
    var checkValue=document.querySelector("#checkAll").checked;
    var checkItemBox=document.querySelectorAll(".checkItem");
    checkItemBox.forEach(function(element, index){
        element.checked=checkValue;
    });
    
});