$(".delStepAction").live("click", function() {
	$(this).parent().hide();
	$(this).parent().remove();
});

function setResult(tid) {
	$.ajax({
		type: "POST",
		url: url + 'ajax/route/',
		data: "action=setResult&tid=" + tid,
		success: function(res) {
			if (res == "null") {
				$("<div title='Уведомление'>Завершить задачу?</div>").dialog({
				    buttons: {
				    	"Да": function() {
				    		contRoute(tid, "");
							$(this).dialog("close");
						},
						"Нет": function() {
							$(this).dialog("close");
						}
					}
				});
			} else {
				$("<div title='Результат'>" + res + "</div>").dialog({
				    buttons: {
				    	"Готово": function() {
				    		var id = ""; var val = "";
				    		var formData = new Array(); var i = 0;
				    		$("#taskResults .valRes").each(function(n){
				    			id = this.id;
				    			id = id.substr(2);
				    			val = $(this).val();
	
				    			formData[i] = ['"' + id + '"', '"' +val + '"'].join(":");
	
				    			i++;
				    		});
	
				    	    var result = "{" + formData.join(",") + "}";
				    	    
				    		contRoute(tid, result);
							$(this).dialog("close");
						}
					}
				});
			}
		}
	});
};

function contRoute(tid, result) {
	$.ajax({
		type: "POST",
		url: url + 'ajax/route/',
		data: "action=closeTask&tid=" + tid + "&result=" + result,
		success: function(res) {
			window.location.href = window.location.href;
		}
	});
};