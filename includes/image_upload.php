<link rel="Stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-sweetalert/1.0.1/sweetalert.css" />
<link rel="Stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.4/croppie.min.css" />
<link rel="Stylesheet" type="text/css" href="image_upload.css" />

<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-sweetalert/1.0.1/sweetalert.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.4/croppie.js"></script>
<script src="https://cdn.jsdelivr.net/npm/exif-js"></script>


<? 

function imageUpload($image_type, $member, $droid=0) {

?> 

<button id="button_<? echo $image_type; ?>">Change Image (<? echo $image_type; ?>)</button>

<div id="image_upload_popup_<? echo $image_type; ?>" class="modal">
  <div class="modal-content">
    <span class="close_<? echo $image_type; ?>">&times; Close</span>
      <div class="demo-wrap upload-demo image_class_<? echo $image_type; ?>">
        <div class="container">
          <div class="grid">
            <div>
             <div class="actions">
               <a class="btn file-btn">
                 <span>Open File</span>
                 <input type="file" id="upload_<? echo $image_type; ?>" value="Choose a file" accept="image/*" />
               </a>
             </div>
           </div>
           <div>
             <div class="upload-msg">
               Crop an image
             </div>
             <div class="upload-demo-wrap">
               <div id="image_area_<? echo $image_type; ?>"></div>
             </div>
<!---
             <button class="rotate_<? echo $image_type; ?>" data-deg="-90">Rotate Left</button>
             <button class="rotate_<? echo $image_type; ?>" data-deg="90">Rotate Right</button>
-->
             <button class="upload-result_<? echo $image_type; ?>">Save</button>
           </div>
         </div>
       </div>
     </div>
  </div>
</div>

<script>

// Get the modal
var modal_<? echo $image_type; ?> = document.getElementById("image_upload_popup_<? echo $image_type; ?>");

// Get the button that opens the modal
var btn_<? echo $image_type; ?> = document.getElementById("button_<? echo $image_type; ?>");

// Get the <span> element that closes the modal
var span_<? echo $image_type; ?> = document.getElementsByClassName("close_<? echo $image_type; ?>")[0];

// When the user clicks on the button, open the modal
btn_<? echo $image_type; ?>.onclick = function() {
  modal_<? echo $image_type; ?>.style.display = "block";
}

// When the user clicks on <span> (x), close the modal
span_<? echo $image_type; ?>.onclick = function() {
  modal_<? echo $image_type; ?>.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
  if (event.target == modal_<? echo $image_type; ?>) {
    modal_<? echo $image_type; ?>.style.display = "none";
  }
}

$(function(){
  var $uploadCrop;
  var file_upload = document.getElementById("upload_<? echo $image_type; ?>");
  var image_class = document.getElementsByClassName("image_class_<? echo $image_type; ?>");
  var image_area = document.getElementById("image_area_<? echo $image_type; ?>");
  var display_image = document.getElementById("<? echo $image_type; ?>");

  console.log("loaded function");

  function readFile(input) {
    console.log("Reading file...");
    if (input.files && input.files[0]) {
      var reader = new FileReader();
      reader.onload = function (e) {
        console.log("onload");
        $('.image_class_<? echo $image_type; ?>').addClass('ready');
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


  $uploadCrop = $('#image_area_<? echo $image_type; ?>').croppie({
    enableOrientation: false,
    boundary: { width: 480, height: 640 },
    viewport: {
      width: 480,
      height: 640 
    },
    enableExif: true
  });


  $('#upload_<? echo $image_type; ?>').on('change', function () { 
    console.log("on change"); 
    readFile(this); 
  });

  $('.rotate_<? echo $image_type; ?>').on('click', function(ev) {
	  console.log("Degrees: " + parseInt($(this).data('deg')));
	  $uploadCrop.croppie('rotate', {
	  	degrees: parseInt($(this).data('deg'))
          });
  });

  $('.upload-result_<? echo $image_type; ?>').on('click', function (ev) {
    $uploadCrop.croppie('result', {
      type: 'base64',
      format: 'jpeg',
      size:{ width:672, height:896 } 
    }).then(function (resp) {
      $.ajax({
        url: "save_image.php",
        type: "POST",
        data: {
               "type":"<? echo $image_type; ?>",
               "member":"<? echo $member; ?>",
               "droid":"<? echo $droid; ?>",
               "image":resp
          }
      });
    });
//    $uploadCrop.croppie('result', {
//      type: 'canvas',
//      format: 'jpeg',
//      size: 'original'
//    }).then(function (resp) {
//      $.ajax({
//        url: "save_image.php",
//        type: "POST",
//        data: {
//               "type":"<? echo $image_type; ?>",
//               "member":"<? echo $member; ?>",
//               "droid":"<? echo $droid; ?>",
//	       "orig":"true",
//               "image":resp
//          }
//      });
//    });
    modal_<? echo $image_type; ?>.style.display = "none";  
    //console.log("Reloading image");
    //display_image.src="showImage.php?member_id=<? echo $member; ?>&droid_id=<? echo $droid; ?>&type=droid&name=<? echo $image_type; ?>&width=240&<? echo rand(); ?>";
  });

});

</script>



<?
}
