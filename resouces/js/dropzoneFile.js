Dropzone.autoDiscover = false;

function setup(id) {
    let options = {
        thumbnailHeight: 210,
        thumbnailWidth: 140,
        maxFilesize: 3,
        maxFiles: 5,
        dictResponseError: "Servidor no configurat",
        dictFileTooBig: "La mida del fitxer ({{filesize}}MB) és massa gran. Ha de ser més petita de {{maxFilesize}}MB.",
        dictCancelUpload: "",
        acceptedFiles: ".png,.jpg,.jpeg",
        init: function () {
            var self = this;
            //New file added
            self.on("addedfile", function (file) {
                console.log("new file added ", file);
            });
            // Send file starts
            self.on("sending", function (file) {
                console.log("upload started", file);
            });

            self.on("complete", function (file, response) {
                if (file.name !== "442343.jpg") {
                    //this.removeFile(file);
                }
            });

            self.on("maxfilesreached", function (file, response) {
                //alert("too big");
            });

            self.on("maxfilesexceeded", function (file, response) {
                this.removeFile(file);
            });
        },
        accept: function (file, done) {
            console.log(file);
        },


        previewTemplate: `
<div class="row text-center justify-content-center d-flex">
             <div class="col-12 col-md-3 mt-3 mr-5 image-area">
                  <img data-dz-thumbnail class="img-fluid border" onclick="window.open(data-dz-thumbnail)"/>
                   <a href="javascript:undefined;" class="remove-image" href="#" style="display: inline;" data-dz-remove="">&#215;</a>
                   <div class="dz-error-message"><i class="fa fa-warning">&nbsp;</i><span data-dz-errormessage></span></div>
  <div class="dz-filename"><span data-dz-name></span></div>
  <div class="dz-progress">
    <span class="dz-upload" data-dz-uploadprogress></span>
  </div>
             </div>

        </div>



`


    };

    var myDropzone = new Dropzone(`#${id}`, options);
}

setup("my-awesome-dropzone");
