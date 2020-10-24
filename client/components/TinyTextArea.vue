<template>
    <div>
        <textarea ref="textarea" v-show="ready" name="" id="textarea" cols="30" rows="10"></textarea>
        <small v-show="!ready">Loading editor...</small>
    </div>
</template>

<script>
export default {
    components: {
    },
    data(){
        return {
            intervalTimeout: undefined,
            textareaPlugins: [
                'a11ychecker',
                'advcode',
                'advtable' ,
                'anchor',
                'autolink',
                'casechange',
                'checklist' ,
                'emoticons',
                'ExportToDoc',
                'formatpainter',
                'linkchecker' ,
                'lists' ,
                'media' ,
                'mediaembed' ,
                'pageembed' ,
                'permanentpen' ,
                'powerpaste' ,
                // "save",
                'table' ,
                'template',
                'tinycomments' ,
                'tinydrive' ,
                'tinymcespellchecker',
            ],
            ready: false
        }
    },
    computed: {
        apiKey(){
            return process.env.TINY_MCE_API_KEY
        },
        textarea(){
            return this.$refs.textarea
        }
    },
    methods: {
        checkForTinyMCE(){
            this.intervalTimeout = setTimeout(() => {
                if(tinymce){
                    this.ready = true
                    tinymce.init({
                        selector: 'textarea',
                        plugins: this.textareaPlugins,
                        toolbar: 'formatpainter code insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons ExportToDoc',
                        // toolbar: 'save a11ycheck addcomment anchor showcomments casechange checklist code formatpainter insertfile pageembed permanentpen table',
                        toolbar_drawer: 'floating',
                        menubar: 'favs file edit view insert format tools table help',
                        tinycomments_mode: 'embedded',
                        tinycomments_author: this.$auth.user.name,
                        save_enablewhendirty: true
                    });
                    clearInterval(this.intervalTimeout)
                    
                }
            }, 500);
        },
        
    },
    mounted(){
        this.checkForTinyMCE()
    },
    updated(){

    }
}
</script>