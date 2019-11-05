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

<button id="button_<? echo $image_type; ?>">Change Image</button>

<div id="image_upload_popup_<? echo $image_type; ?>" class="modal">
  <div class="modal-content">
    <span class="close_<? echo $image_type; ?>">&times;</span>
      <div class="demo-wrap upload-demo image_class_<? echo $image_type; ?>">
        <div class="container">
          <div class="grid">
            <div>
             <div class="actions">
               <a class="btn file-btn">
                 <span>Upload</span>
                 <input type="file" id="upload_<? echo $image_type; ?>" value="Choose a file" accept="image/*" />
               </a>
               <button class="upload-result_<? echo $image_type; ?>">Save</button>
             </div>
           </div>
           <div>
             <div class="upload-msg">
               Upload a file to start cropping
             </div>
             <div class="upload-demo-wrap">
               <div id="image_area_<? echo $image_type; ?>"></div>
             </div>
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
    enableOrientation: true,
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

  $('.upload-result_<? echo $image_type; ?>').on('click', function (ev) {
    $uploadCrop.croppie('result', {
      type: 'canvas',
      format: 'jpeg',
      size: 'viewport'
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
    modal_<? echo $image_type; ?>.style.display = "none";  
    });
  });

});

</script>



<?
}
