$(document).ready(function(){
	$("button,input[type=\"button\"],input[type=\"submit\"]").on("mousedown",function(){
		$(this).removeClass("button-inactive");
		$(this).addClass("button-active");
	});
	$("button,input[type=\"button\"],input[type=\"submit\"]").on("mouseup mouseleave",function(){
		$(this).removeClass("button-active");
		$(this).addClass("button-inactive");
	});
	$("button,input[type=\"button\"],input[type=\"submit\"]").addClass("button-inactive");
});
function redirect(url){
	window.location=url;
}