<form 
	id="image-form"
	class="media-upload-form type-form validate" 
	action="http://pvapi.dev/wp-admin/media-upload.php?type=pvapi&tab=type&post_id=11"
	method="post"
	enctype="multipart/form-data"
	>
<h2>Select PinkVisual Media</h2>
<p>Use this menu to select the Pink Visual media to insert into your post</p>
<h3>Available Sources</h3>
<div id="pvapi_holder"><p>Loading Sources ...</p></div>
<input type="button" id="pvapi_insert_button" value="Insert General PinkVisual Tag" />
<script type="text/javascript">
jQuery(document).ready(function(){
	console.log("Setting up PVAPI Inserter");
	jQuery("#pvapi_insert_button").click(function(){
		console.log("Inserting PV Tag");
		var win = window.dialogArguments || opener || parent || top;
		win.send_to_editor("[pvapi]");
		return false;
	});
	jQuery.post(ajaxurl, {action: 'pvapi_list_sources'}, function(response) {
		console.log("Got a response for 'pvapi_list_sources'");
		console.log(response);
		try{
		var data = jQuery.parseJSON(response);
		} catch(err) {
			console.warn("Error parsing json");
			console.warn(err);
			alert("Error fetching sources from PinkVisual. Is your API Key correct?");
			return;
		}
		var holder = jQuery("#pvapi_holder");
		holder.hide();
		holder.html(jQuery("#pvapi_source_list_base").html());
		var table = jQuery(holder).find("#pvapi_source_list_table").find("tbody");
		for(var x in data['sources']) {
			var sc = data['sources'][x];
			table.append("<tr><td>"+sc.name+"</td><td><a class='pvapi_source_link' href='#' pvid='"+sc.id+"'>Insert Source</a></td></tr>");
		}
		jQuery(".pvapi_source_link").click(function() {
			var id = jQuery(this).attr('pvid');
			console.log("Putting source " + id + " to the editor");
			var win = window.dialogArguments || opener || parent || top;
			win.send_to_editor("[pvapi source=\""+id+"\"]"); 
			return false;
		});
		holder.show();
	});
});
</script>
<div style="display: none" id="pvapi_source_list_base">
	<p>There are <span id="pvapi_source_list_count"></span> sources available.</p>
	<table id="pvapi_source_list_table">
		<thead>
			<tr>
				<th>Name</th>
				<th>Insert</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>
</form>