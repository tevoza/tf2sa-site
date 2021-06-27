$(document).ready(function(){
	// File upload via Ajax
	$("#uploadForm").on('submit', function(e){
			e.preventDefault();
			$.ajax({
					xhr: function() {
							var xhr = new window.XMLHttpRequest();
							xhr.upload.addEventListener("progress", function(evt) {
									if (evt.lengthComputable) {
											var percentComplete = ((evt.loaded / evt.total) * 100);
											$(".progress-bar").width(percentComplete + '%');
											$(".progress-bar").html(percentComplete+'%');
									}
							}, false);
							return xhr;
					},
					type: 'POST',
					url: 'upload.php',
					data: new FormData(this),
					contentType: false,
					cache: false,
					processData:false,
					beforeSend: function(){
							$(".progress-bar").width('0%');
							$('#uploadStatus').html('<img src="loading.gif"/>');
					},
					error:function(){
							$('#uploadStatus').html('<p style="color:#EA4335;">File upload failed, please try again.</p>');
					},
					success: function(resp){
							if(resp == 'err'){
									$('#uploadStatus').html('<p style="color:#EA4335;">Please select a valid file to upload.</p>');
							}else{
									$('#uploadForm')[0].reset();
									$('#uploadStatus').html('<p style="color:#28A74B;">File has uploaded successfully!</p>');
									document.getElementById("imgHash").value = resp;
									//send resp to main form.
							}
					}
			});
	});

	// File type validation
	$("#fileInput").change(function(){
			var allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
			var file = this.files[0];
			var fileType = file.type;
			if(!allowedTypes.includes(fileType)){
					alert('Please select a valid file (JPEG/JPG/PNG/GIF).');
					$("#fileInput").val('');
					return false;
			}
	});
});
