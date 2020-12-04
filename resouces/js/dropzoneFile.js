Dropzone.autoDiscover = false;

function setup(id) {
    var id_temp = '';

    let options = {
        url: "../../controllers/ProductController.php",
        paramName: "file",
        autoProcessQueue: false,
        thumbnailHeight: 210,
        thumbnailWidth: 140,
        maxFilesize: 3,
        maxFiles: 5,
        dictResponseError: "Servidor no configurat",
        dictFileTooBig: "La mida del fitxer ({{filesize}}MB) és massa gran. Ha de ser més petita de {{maxFilesize}}MB.",
        dictInvalidFileType: "Tipus de fitxer invàlid",
        dictCancelUpload: "",
        dictMaxFilesExceeded: "Only {{maxFiles}} files are allowed",
        acceptedFiles: ".png,.jpg,.jpeg",
        init: function () {
            var self = this;
            //New file added
            self.on("addedfile", function (file) {
                //console.log("new file added ", file);
            });
            // Send file starts
            self.on("sending", function (file) {
                //console.log("upload started", file);
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
            self.on("thumbnail", function(file){
                var data = file;
                data.name_file = file.name;
                data = JSON.stringify(data);
                $.ajax({
                    url: '../../controllers/ProductController.php',
                    type: "Post",
                    data:{"image_newProduct":data,"dataUrl":data.dataUrl},
                    contentType: 'application/x-www-form-urlencoded',
                    success: function (response) {
                        console.log("END",response)
                        id_temp = JSON.parse(response);
                        document.getElementById("button").addEventListener('click',function (event){
                            console.log(event.target.id);
                            $.ajax({
                                url: '../../controllers/ProductController.php',
                                type: "Post",
                                data:{"delete_image_newProduct":event.target.id},
                                contentType: 'application/x-www-form-urlencoded',
                                success: function (response){
                                    console.log("Image deleted",response);
                                },
                                error: function (response){
                                    console.log("Error deleting image",response);
                                }
                            })
                        });
                        document.getElementById("button").id = id_temp.id_temp;
                        console.log( document.getElementById("button"))
                        console.log( document.getElementById(id_temp.id_temp))
                    },
                    error: function (response){
                        console.log("error",response)
                    }
                })
            });
        },
        previewTemplate: `
            <div class="row text-center justify-content-center d-flex">
                <div class="col-12 col-md-3 mt-3 mr-5 image-area">
                    <img data-dz-thumbnail class="img-fluid border"/>
                    <button href="javascript:undefined;" class="remove-image" href="#" style="display: inline;" data-dz-remove="" id="button">&#215;</button>
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
