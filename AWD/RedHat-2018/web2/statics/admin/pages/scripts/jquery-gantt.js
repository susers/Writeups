var jgantt = function () {

    return {
        
        init: function () {
            var gantt_data = [
				{ "name": " Step A ","desc": "&rarr; Step B"  ,"values": [{"id": "b0", "from": "/Date(1320182000000)/", "to": "/Date(1320301600000)/", "desc": "Id: 0<br/>Name:   Step A", "label": " Step A", "customClass": "ganttRed", "dep": "b1"}]}
			];

            $(".jgantt").gantt({source: gantt_data, navigate: 'scroll', scale: 'days', maxScale: 'weeks', minScale: 'hours'});
		}
    };
}();

jQuery(document).ready(function() {    
	 jgantt.init(); 
});
  