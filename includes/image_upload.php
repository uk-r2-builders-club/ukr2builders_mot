<link rel="Stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-sweetalert/1.0.1/sweetalert.css" />
<link rel="Stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.4/croppie.min.css" />
<link rel="Stylesheet" type="text/css" href="image_upload.css" />

<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-sweetalert/1.0.1/sweetalert.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.4/croppie.js"></script>
<script src="includes/image_upload.js"></script>
<script src="https://cdn.jsdelivr.net/npm/exif-js"></script>


<? 

function imageUpload($image_type) {

?> 

<button id="button_<? echo $image_type; ?>">Change Image</button>

<div id="image_upload_popup_<? echo $image_type; ?>" class="modal">
  <div class="modal-content">
    <span class="close_<? echo $image_type; ?>">&times;</span>
      <div class="demo-wrap upload-demo">
        <div class="container">
          <div class="grid">
            <div>
             <div class="actions">
               <a class="btn file-btn">
                 <span>Upload</span>
                 <input type="file" id="upload" value="Choose a file" accept="image/*" />
               </a>
               <button class="upload-result">Save</button>
             </div>
           </div>
           <div>
             <div class="upload-msg">
               Upload a file to start cropping
             </div>
             <div class="upload-demo-wrap">
               <div id="upload-demo"></div>
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

          Demo.init();


</script>



<?
}
