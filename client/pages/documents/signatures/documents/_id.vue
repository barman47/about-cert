<template>
	<div class="documents-page-content">
		This is the detailed view of the signature request
	</div>
</template>

<script>

	export default {
	    layout: "document",
	    async fetch({store, $axios, params, redirect}){
	    	// TODO: Remove the 'true'
	        if(true || !store.state.signature_documents.signatureReceivedMarkersLoaded){
	            await $axios.get("/api/documents/signatures/documents/received-markers")
	                    .then(response => {
	                        store.commit("signature_documents/commitReceivedMarkers", response.data)
	                    }).catch(() => {
	                        return redirect("/documents")
	                    })
	        }

	        // TODO: Remove the 'true'
	        if(true || !store.state.signature_documents.signatureSendMarkersLoaded){
	            await $axios.get("/api/documents/signatures/documents/send-markers")
	                    .then(response => {
	                        store.commit("signature_documents/commitSendMarkers", response.data)
	                    }).catch(() => {
	                        return redirect("/documents")
	                    })
	        }
	    },
	}
</script>

<style scoped>
	.documents-page-content{
	    color: #7574A0;
	}
</style>