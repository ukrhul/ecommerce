</div>
<footer class="text-center" id="footer">&copy; Copyright 2018 TempKart.com</footer>

<script>
	function updateSizes(){
		var sizeString = '';
		for(var i=1;i<=12;i++){
			if(jQuery('#size'+i).val() != '')
				{
					sizeString += jQuery('#size' + i).val()+':'+jQuery('#qty'+i).val() + ':' + jQuery('#threshold'+i).val() + ',';
				}
		}
		jQuery('#size').val(sizeString);
	}
	
	function get_child_options(selected){
		if(typeof selected == 'underfined'){
			var selected = '';
		}
		var parentID = jQuery('#parent').val();
		jQuery.ajax({
			url: '/ecommerce/admin/parsers/child_categories.php',
			type: 'POST',
			data : {parentID : parentID, selected: selected },
			success : function(data){
				jQuery('#child').html(data);
			},
			error : function(){
				alert("something went wrong");
			},
		});
	}
	jQuery('select[name="parent"]').change(function(){
		get_child_options();
	});

</script>

</body>
</html>
