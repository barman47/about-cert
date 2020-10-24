<template>
	<div class="documents-page-content">
		<div>
   			<h3 style="margin-top: 1.5rem; margin-bottom: 0.5rem;" class="display-flex align-items-center">Sent Signature Request</h3>
   			<p v-if="!(signatureFolder && signatureFolder.id) && sentMarkers.length < 1" class="empty-marker-text">
   				You do not have any sent signature.
   			</p>
   			<div v-else class="signature-markers-container">
   				<single-file v-if="!!signatureFolder.id" :file="signatureFolder"/>
   				<signature-marker v-for="(file, i) in sentMarkers" :key="i" action="sent" :file="file" />
   			</div>
		</div>

		<div>
   			<h3 style="margin-top: 1.5rem; margin-bottom: 0.5rem;" class="display-flex align-items-center">Received Signature Request</h3>
   			<p v-if="receivedMarkers.length < 1" class="empty-marker-text">
   				You have not received any signature request.
   			</p>
   			<div v-else class="signature-markers-container">
   				<signature-marker v-for="file in receivedMarkers" :key="file.id" action="received" :file="file" />
   			</div>
		</div>
	</div>
</template>

<script>
	import SignatureMarker from "~/components/SignatureMarker"
	import SingleFile from "~/components/SingleFile"

	export default {
	    layout: "document",
	    components: {
	    	SignatureMarker,
	    	SingleFile
	    },
	    async fetch({store, $axios, params, redirect}){
	    	if(!store.state.signature_documents.signatureFolder.id){
	    		await $axios.get("/api/documents/get-signature-folder")
	    					.then(response => {
	    						store.commit("signature_documents/commitSignatureFolder", response.data.document)
	    					}).catch(err => {})
	    	}
	        if(!store.state.signature_documents.signatureReceivedMarkersLoaded){
	            await $axios.get("/api/documents/signatures/documents/received-markers")
	                    .then(response => {
	                        store.commit("signature_documents/commitReceivedMarkers", response.data)
	                    }).catch(() => {
	                        return redirect("/documents")
	                    })
	        }

	        if(!store.state.signature_documents.signatureSendMarkersLoaded){
	            await $axios.get("/api/documents/signatures/documents/send-markers")
	                    .then(response => {
	                        store.commit("signature_documents/commitSendMarkers", response.data)
	                    }).catch(() => {
	                        return redirect("/documents")
	                    })
	        }



	    },
	    data(){
	    	return {}
	    },
	    computed: {
	    	signatureFolder(){
	    		return this.$store.state.signature_documents.signatureFolder
	    	},
	    	sentMarkers(){
	    		return this.$store.state.signature_documents.signatureSendMarkers
	    	},
	    	receivedMarkers(){
	    		return this.$store.state.signature_documents.signatureReceivedMarkers
	    	},
	    	goToPage(page){
	    		if(page != this.$nuxt.$route.fullPath)
	    			this.$nuxt.$router.push(page)
	    	}
	    }
	}
</script>

<style scoped>
	.documents-page-content{
	    color: #7574A0;
	}

	.empty-marker-text{
		font-style: italic;
		font-size: 0.9rem;
	}

	.signature-markers-container{
		display: flex;
	}
</style>