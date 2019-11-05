var Demo = (function() {

	function demoUpload() {
		var $uploadCrop;

		function readFile(input) {
			if (input.files && input.files[0]) {
	            		var reader = new FileReader();
	            
	            		reader.onload = function (e) {
					$('.upload-demo').addClass('ready');
	            			$uploadCrop.croppie('bind', {
	            				url: e.target.result
	            			}).then(function(){
	            				console.log('jQuery bind complete');
	            			});
	            	
	            		}
	            
	            		reader.readAsDataURL(input.files[0]);
	        	}
	        	else {
		        	swal("Sorry - you're browser doesn't support the FileReader API");
		    	}
		}

		$uploadCrop = $('#upload-demo').croppie({
       			enableOrientation: true,
			viewport: {
				width: 480,
				height: 640
			},
			enableExif: true
		});

		$('#upload').on('change', function () { 
			readFile(this); 
		});
		$('.upload-result').on('click', function (ev) {
			$uploadCrop.croppie('result', {
				type: 'canvas',
				format: 'jpeg',
				size: 'viewport'
			}).then(function (resp) {
				$.ajax({
					url: "save_image.php",
					type: "POST",
					data: {"image":resp}
				});
			});
		});
	}

	function init() {
		demoUpload();
	}

	return {
		init: init
	};
})();

