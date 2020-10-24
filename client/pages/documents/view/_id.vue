<template>
    <div class="div" id="viewing-document">
        <no-ssr v-if="false">
            <VueDocPreview v-if="documentType && supportedTypes.some(el => el == documentType) && value" :value="value" :type="documentType" :requestConfig="requestConfig"/>
            <div v-else-if="documentType == 'image'" id="viewing-images">
              <img :src="value"/>
            </div>
            <div v-else-if="documentType == 'pdf'" id="viewing-other-docs">
              <iframe v-if="!!value" :src="'https://docs.google.com/viewer?embedded=true&url='+value" width="100%" height="100%"></iframe>
            </div>
            <div v-else-if="documentType" id="viewing-other-docs">
              <!-- <object data="http://192.168.137.1/aboutcert/public/test.pdf" type="application/pdf" style="height: 100%; width: 100%;">
                <embed src="http://192.168.137.1/aboutcert/public/test.pdf" type="application/pdf"></embed>
              </object> -->
              <iframe id="myIframe" :src="value" width="100%" height="100%"></iframe>
            </div>
            <div class="" v-else>Loading ...</div>
        </no-ssr>

        <no-ssr>
          <VueDocPreview v-if="documentType && documentType == 'office'" :value="value" :type="documentType" :requestConfig="requestConfig"/>
          <div v-else-if="documentType == 'image'" id="viewing-images">
            <img :src="value"/>
          </div>
          <iframe v-else-if="documentType" :src="'https://docs.google.com/viewer?embedded=true&url='+url" width="100%" height="100%" ></iframe>
          <div class="" v-else>Loading ...</div>
        </no-ssr>

    </div>
</template>

<script>
// import VueDocPreview from

export default {
    layout: "authenticated",
    data(){
      return {
        url : undefined,
        document: undefined,
        documentTypes: {
          office : [
            "docx",
            "docm",
            "dotx",
            "dotm",
            "docb",
            "docb",
            "xls",
            "xlt",
            "xlm",
            "xlsx",
            "xltx",
            "xltm",
            "xlsb",
            "xla",
            "xlam",
            "xll",
            "xlw",
            "ppt",
            "pot",
            "pps",
            "pptx",
            "pptm",
            "potx",
            "potm",
            "potx",
            "ppam",
            "ppsx",
            "ppsm",
            "sldx",
            "sldm"
          ],
          markdown : ["md"],
          text: ["txt"],
          code: [
              "js",
              "html",
              "css",
              "java",
              "json",
              "ts",
              "cpp",
              "xml",
              "bash",
              "less",
              "nginx",
              "php",
              "powershell",
              "python",
              "scss",
              "shell",
              "sql",
              "yaml",
              "yml",
              "ini",
              "tmp"
          ],
          pdf: ["pdf"],
          image: [
            "jpg",
            "jpeg",
            "png"
          ]
        },
        supportedTypes: ["office", "markdown", "text", "code"],
        mapExtensionToLanguage: {
            "js" : "javascript"
        },
        tempValue: undefined,
        fileUrl: undefined
      }
    },
    components: {
        'VueDocPreview' : () => import('vue-doc-preview')
    },
    created(){
        this.fetchData()
    },
    mounted(){
      // debugger
    },
    updated(){
    },
    methods: {
      fetchData(){
        this.$axios.get("/api/documents/view?document_id="+this.$nuxt.$route.params.id)
            .then(response => response.data)
            .then(data => {
              this.url = data.file_url
              this.document = data.document
              this.fileUrl = data.file_url

              for(let key in this.documentTypes){
                if(this.documentTypes[key].some(el => el == this.document.extension)){
                  // console.log("===========================> key:" + key)
                  this.documentType == key
                }
              }

              // if(this.documentType == "pdf"){
              //   window.open(this.url, "_blank")
              //   this.$router.go(-1)
              // }

              this.tempValueC()
            })
      },
      download() {
        return new Promise((resolve, reject) => {
          this.$axios.get(this.fileUrl).then(res => {
            const reader = new FileReader()
            reader.readAsText(new Blob([res.data]))
            reader.onload = function () {
              resolve(reader.result)
            }
          }).catch(err => {
            reject(err)
          })
        })
      },
      tempValueC() {
        if (this.documentType == 'office') {
          this.tempValue = this.url
          
        } else if(this.supportedTypes.some(el => el == this.documentType)) {
          this.tempValue = this.url
          return
          this.download().then(data => {
            this.tempValue = data
          }).catch(err => {
            this.tempValue = 'Download Error!'
            console.error(err)
          })
        }else{
          this.tempValue = this.url
        }
      },
    },
    computed: {
      documentType(){
        if(!this.document)
          return undefined

        for(let key in this.documentTypes){
          if(this.documentTypes[key].some(el => el == this.document.extension))
            return key
        }

        return undefined
      },
      value() {
        this.tempValueC()
        if(this.documentType == "office"){
          //TODO: uncomment the next like and comment the one after that
          return  this.tempValue
          // return 'https://35f1a06e.ngrok.io/aboutcert/public' + this.tempValue
        }else if (this.documentType == 'code' && this.language) {
          return `\`\`\`${this.language}\n${this.tempValue}\n\`\`\``
        } else {
          // let Url = this.tempValue

          // if (Url != null && Url.contains("=")) {
          //     let Urll =Url.substring(Url.lastIndexOf("&signature=") + 1).replace("%2B", "%252B");
          //     if (null != Urll && Urll.length() > 0 && Urll.contains("%252B")) {
          //         let endIndex = Url.lastIndexOf("signature");
          //         if (endIndex != -1) {
          //             Url = Url.substring(0, endIndex);
          //             Url = Url + Urll;
          //         }
          //     }
          //     Url = Url.replace("?AWSAccessKeyId=", "?AWSAccessKeyId%3D")
          //             .replace("&expires=", "%26Expires%3D").replace("&signature=", "%26signature%3D");
          // }

          return encodeURI(this.tempValue)
        }
      },
      language(){
        if(this.documentType != "code")
          return undefined

        let temp = this.document.extension

        for(let index in this.mapExtensionToLanguage){
          if(temp == index){
            temp = this.mapExtensionToLanguage[index]
          }
        }

        return temp
      },
      requestConfig(){
        return {
          headers : {
            "Access-Control-Allow-Origin": "*",
            'X-Requested-With': 'XMLHttpRequest'
          }
        }
      }
    }
}
</script>

<style scoped>
#viewing-document{
  background: white;
  border-radius: 5px;
  /* overflow: hidden; */
  overflow-x: auto;
}

#viewing-other-docs{
  min-height: 100%;
  height: 100%;
  max-height: 100%;
  max-width: 100%;
}

#viewing-images{
  height: 100%;
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
}

#viewing-images img {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
}

#VueDocPreviewRoot{
  padding: 1rem;

  width: 100%;
}

#VueDocPreviewRoot .content{
  background: green;
}
</style>
