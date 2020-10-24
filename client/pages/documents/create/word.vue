<template>
  <div id="create-document">
    <!-- <h4>Create Document</h4> -->
    <input type="image" ref="imageInput" src="" alt="" hidden>
     <editor
       api-key="lyvhxjkj22767y2229u6ip6vx1kito5b8pdltcd47x1gk584"
       initialValue="<p>This is the initial content of the editor</p>"
       :init="{
         height: 500,
         menubar: 'favs file edit view insert format tools table help',
         plugins: [
           'advlist autolink lists link image charmap print preview anchor',
           'searchreplace visualblocks code fullscreen',
           'insertdatetime media table paste code help wordcount'
         ],
         toolbar:
           'undo redo | code | formatselect | bold italic backcolor | \
           alignleft aligncenter alignright alignjustify | \
           bullist numlist outdent indent | removeformat | help',
          image_title: true,
          file_picker_types: 'image',
          automatic_uploads: true,
          file_picker_callback: file_picker_callback
       }"
       ></editor>
        <div class="checkbox-container">
            <div>
              <input type="text" id="document-name-input" ref="documentNameInput" placeholder="Enter document name">
            </div>
            <div>
              <small style="color: #e86262">{{errorMessage}}</small>
            </div>
            <div>
                <input type="checkbox" name="" id="download-checkbox">
                Download
            </div>
        </div>
        <button @click="saveDocument()" ref="submitButton">Save</button>
  </div>
</template>

<script>
import Editor from '@tinymce/tinymce-vue';
// import TinyTextArea from "~/components/TinyTextArea";
export default {
  layout: "document",
  components: {
      // TinyTextArea,
      Editor
  },
  head() {
    return {
      script: [
        // {
        //   src: "https://cdn.tiny.cloud/1/lyvhxjkj22767y2229u6ip6vx1kito5b8pdltcd47x1gk584/tinymce/5/tinymce.min.js"
        // }
      ],
    //   link: [
    //     {
    //       rel: "stylesheet",
    //       href: "https://fonts.googleapis.com/css?family=Roboto&display=swap"
    //     }
    //   ]
    }
  },
  data(){
    return{
      errorMessage: ""
    }
  },
  mounted(){
    
  },
  methods: {
      saveDocument(){
        this.errorMessage = ""

        if(!this.$refs.documentNameInput.value){
          this.errorMessage = "The document name can't be empty"
          return
        }

        const data = {
            html: tinymce.activeEditor.getContent(),
            type: "doc",
            name: this.$refs.documentNameInput.value
        }

        const download = document.getElementById("download-checkbox").checked

        if(download)
            Object.assign(data, {download: "true"})
        let attempt = this.$store.dispatch("documents/forgeDocument", data)

        let button = this.$refs.submitButton
        button.setAttribute("diabled", "true")
        button.classList.add("loading")

        attempt.then(url => {
          button.classList.remove("loading")
          button.classList.add("success")
          if(download){
            let a = document.createElement("a")
            a.href = url
            a.setAttribute("hidden", "true")
            a.click()
          }
          tinymce.activeEditor.setContent("")
        }).catch(() => {
          button.classList.remove("loading")
          button.classList.add("error")
          this.errorMessage = "An error occured"
        })
        .finally(() => {
          button.removeAttribute("hidden")
          button.classList.remove("loading")
          setTimeout(() => {
            button.classList.remove("error")
            button.classList.remove("success")
          }, 2000);
        })
      },
      file_picker_callback(cb, value, meta){
        var input = this.$refs.imageInput;
        input.setAttribute('type', 'file');
        input.setAttribute('accept', 'image/*');

        input.onchange = function() {
            var file = this.files[0];
            var reader = new FileReader();
            
            reader.onload = function () {
            var id = 'blobid' + (new Date()).getTime();
            var blobCache =  tinymce.activeEditor.editorUpload.blobCache;
            var base64 = reader.result.split(',')[1];
            var blobInfo = blobCache.create(id, file, base64);
            blobCache.add(blobInfo);

            // call the callback and populate the Title field with the file name
            cb(blobInfo.blobUri(), { title: file.name });
            };
            reader.readAsDataURL(file);
        };
        
        input.click();
        }
      
  }
};
</script>

<style scoped>
    #create-document{
        display: flex;
        height: 100%;
        flex-direction: column;
        padding-bottom: 1rem;
        overflow-x:hidden;
    }

    button{
        border: none;
        color: white;
        background: #0084ff;
        border-radius: 3px;
        padding: 0.5rem 1rem 0.5rem 1rem;
    }

    .checkbox-container{
        color: #9b9b9b;
        font-size: 0.8rem;
        margin-top: 0.5rem;
        margin-bottom: 0.5rem;
    }

    button.error{
        background: #e86262;
    }

    button.loading{
        background: #fcd462;
    }

    button.success{
        background: #7ed202;
    }

    #document-name-input{
      /* width: 10rem; */
      height: 30px;
      outline: none;
      border: 1px solid #9b9b9b;
      border-radius: 5px;
      padding-left: 0.5rem;
      padding-right: 0.5rem;
      color: #333;
      font-size: 0.9rem;
    }
</style>