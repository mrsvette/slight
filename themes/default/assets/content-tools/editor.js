window.addEventListener('load', function() {
	var element = document.getElementById('main-content');
	var region = new ContentEdit.Region(element);
    var editor;
	editor = ContentTools.EditorApp.get();
	editor.init('*[data-editable], [data-fixture]', 'data-name');

	editor.addEventListener('saved', function (ev) {
		var name, payload, regions, xhr;
		// Check that something changed
		regions = ev.detail().regions;
		if (Object.keys(regions).length == 0) {
		    return;
		}

		// Set the editor as busy while we save our changes
		this.busy(true);

		// Collect the contents of each region into a FormData instance
		payload = new FormData();
		for (name in regions) {
		    if (regions.hasOwnProperty(name)) {
		        payload.append(name, regions[name]);
		    }
		}
		// All content data
		payload.append('content', region.html());
		// The path url
		payload.append('path', window.location.pathname);

		// Send the update content to the server to be saved
		function onStateChange(ev) {
		    // Check if the request is finished
		    if (ev.target.readyState == 4) {
		        editor.busy(false);
		        if (ev.target.status == '200') {
		            // Save was successful, notify the user with a flash
		            new ContentTools.FlashUI('ok');
					setTimeout(function () {
						window.location.reload(true);
					}, 2000);
		        } else {
		            // Save failed, notify the user with a flash
		            new ContentTools.FlashUI('no');
		        }
		    }
		};

		xhr = new XMLHttpRequest();
		xhr.addEventListener('readystatechange', onStateChange);
		xhr.open('POST', '/panel-admin/pages/update-visual');
		xhr.send(payload);
	});
});
