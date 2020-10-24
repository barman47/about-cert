<template>
  <div class="single-file" ref="currentSingleFile">
    <div class="top-icons">
      <div class="image check" style="background-image: url('/png/check.png')"></div>
      <div
        @click="showTopIconDropDown=true"
        class="image triple-dots"
        style="background-image: url('/png/triple-dots.png')"
      ></div>
      <div class="top-icon-dropdown" v-show="showTopIconDropDown">
        <ul v-if="isInTrash">
          <li @click="restoreDocument()">Restore</li>
          <li @click="permanentlyDeleteDocument()">Delete Permanently</li>
        </ul>
        <ul v-else>
          <li @click="openDocument()">Open</li>
          <li class="demacation"></li>
          <li v-if="file.type.toLowerCase() != 'folder'" @click="downloadDocument()">Download</li>
          <!-- <li>Convert</li> -->
          <li @click="showShareDocumentModal()">Share</li>
          <li @click="showDocumentSignatureRequestForm()" v-if="canBeSigned">Signature Request</li>
          <li class="demacation"></li>
          <li @click="openCopyToModal()">Copy To</li>
          <li @click="openMoveToModal()">Move To</li>
          <li class="demacation"></li>
          <li @click="deleteDocument()">Delete</li>
        </ul>
      </div>
    </div>

    <div class="file-info">
      <div class="middle-section image" :class="classes" @dblclick="openDocument()"></div>
      <div class="file-name" @click="openDocument()">{{file.name + "." + file.extension}}</div>

      <div class="file-footer">
        <div class="file-size">{{file.size}}</div>
        <div class="date">{{formattedDate}}</div>
      </div>
    </div>

    <DocumentSignatureRequestForm
      v-if="canBeSigned"
      :file="file"
      ref="documentSignatureRequestForm"
      @noPriviledge="noSigningPriviledge()"
      @hide="hideDocumentSignatureRequestForm()"
    />

    <div class="signature-plans-modal" v-if="canBeSigned && showingSignaturePlans">
      <div class="signature-plans-modal-content">
        <SignaturePlans @closed="hideSignaturePlans()" />
      </div>
    </div>

    <share-document-modal
      :document_id="file.id"
      @closed="renderShareDocumentModal = false"
      v-if="renderShareDocumentModal"
    ></share-document-modal>

    <folder-tree-modal ref="copyToModal" @submitted="commitCopyTo">
      <template v-slot:headerText>Copy To:</template>
      <template v-slot:submitButtonText>Copy</template>
    </folder-tree-modal>

    <folder-tree-modal ref="moveToModal" @submitted="commitMoveTo">
      <template v-slot:headerText>Move To:</template>
      <template v-slot:submitButtonText>Move</template>
    </folder-tree-modal>
  </div>
</template>

<script>
import ShareDocumentModal from "~/components/ShareDocumentModal.vue";
import FolderTreeModal from "~/components/FolderTreeModal.vue";
import Modal from "~/components/Modal.vue";
import DocumentSignatureRequestForm from "~/components/DocumentSignatureRequestForm.vue";
import SignaturePlans from "~/components/SignaturePlans.vue";

export default {
  components: {
    ShareDocumentModal,
    FolderTreeModal,
    Modal,
    DocumentSignatureRequestForm,
    SignaturePlans
  },
  props: ["file", "intent"],
  data() {
    return {
      showTopIconDropDown: false,
      renderShareDocumentModal: false,
      showingSignaturePlans: false
    };
  },
  computed: {
    classes() {
      let data = {};
      let file = this.file;
      if (file.type.toLowerCase() == "folder") {
        Object.assign(data, { folder: true });
      } else {
        let extensions = [
          "pdf",
          "svg",
          "epub",
          "txt",
          "png",
          "jpg",
          "doc",
          "html",
          "css",
          "js",
          "md",
          "yml",
          "dll",
          "exe",
          "log",
          "tmp",
          "php",
          "java",
          "ai",
          "mp3",
          "m4a"
        ];

        if (extensions.some(el => el == file.extension)) {
          let temp = {};
          temp[file.extension] = true;
          Object.assign(data, temp);
        } else if (file.extension == "docx") {
          Object.assign(data, { doc: true });
        } else if (file.extension == "jpeg") {
          Object.assign(data, { jpg: true });
        } else if (file.extension == "htm") {
          Object.assign(data, { html: true });
        } else if (file.extension == "yaml") {
          Object.assign(data, { yml: true });
        } else if (file.extension == "s") {
          Object.assign(data, { css: true });
        } else {
          Object.assign(data, { "generic-file": true });
        }
      }
      return data;
    },
    date() {
      return new Date(this.file.created_at);
    },
    formattedDate() {
      return (
        this.date.getDay() +
        "/" +
        (this.date.getMonth() + 1) +
        "/" +
        this.date.getFullYear()
      );
    },
    canBeSigned() {
      let ext = this.file.extension;
      let list = ["doc", "pdf", "docx"];
      return list.some(el => el == ext);
    },
    isInTrash() {
      return (this.intent || "").toLowerCase() == "trash";
    }
  },
  methods: {
    downloadDocument(){
      this.$axios.get("/api/documents/download/" + this.file.id)
        .then(response => response.data)
        .then(response => {
          let a = document.createElement("a")
          a.href = response.download_url
          a.download = response.download_url
          a.click()
        }).catch(err => console.log(err))

        this.hideTopIcons()
    },
    openFolder() {
      if (this.file.type.toLowerCase() == "folder") {
        this.$store.dispatch("documents/getBreadCrumb", {
          id: this.$nuxt.$route.params.id
        });
        this.$nuxt.$router.push("/documents/" + this.file.id);
      }
    },
    openDocument() {
      if (this.file.type.toLowerCase() == "folder") {
        this.openFolder();
        return;
      }

      this.$nuxt.$router.push("/documents/view/" + this.file.id);
    },
    documentMonitor(e) {
      const path = e.path || (e.composedPath ? e.composedPath() : []);
      if (path.some(el => el == this.$refs.currentSingleFile)) {
        // console.log("within the file")
      } else {
        // console.log("Outside the file")
        this.showTopIconDropDown = false;
      }
    },

    hideTopIcons() {
      this.showTopIconDropDown = false;
    },
    showShareDocumentModal() {
      this.renderShareDocumentModal = true;
      this.hideTopIcons();
    },
    openCopyToModal() {
      this.hideTopIcons();
      this.$refs.copyToModal.openModal();
    },
    closeCopyToModal() {
      this.hideTopIcons();
      this.$refs.copyToModal.closeModal();
    },
    openMoveToModal() {
      this.hideTopIcons();
      this.$refs.moveToModal.openModal();
    },
    commitCopyTo(val) {
      let data = {
        document_id: this.file.id,
        folder_id: !val ? "root" : val
      };

      let attempt = this.$store.dispatch(
        "documents/copyDocumentToFolder",
        data
      ).then(() => {
        this.closeCopyToModal()
      })
    },
    commitMoveTo(val) {
      let data = {
        document_id: this.file.id,
        folder_id: !val ? "root" : val
      };

      let attempt = this.$store.dispatch(
        "documents/moveDocumentToFolder",
        data
      );
    },
    showDocumentSignatureRequestForm() {
      this.hideTopIcons();
      this.$refs.documentSignatureRequestForm.show();
    },
    hideDocumentSignatureRequestForm() {
      this.$refs.documentSignatureRequestForm.close();
    },
    showSignaturePlans() {
      this.showingSignaturePlans = true;
    },
    hideSignaturePlans() {
      this.showingSignaturePlans = false;
    },
    noSigningPriviledge() {
      this.hideDocumentSignatureRequestForm();
      setTimeout(() => {
        this.showSignaturePlans();
      }, 50);
    },
    deleteDocument() {
      let attempt = this.$store.dispatch("documents/deleteDocument", {
        document_id: this.file.id
      });
    },
    permanentlyDeleteDocument() {
      let attempt = this.$store.dispatch(
        "documents/permanentlyDeleteDocument",
        { document_id: this.file.id }
      );
    },
    restoreDocument() {
      this.$store.dispatch("documents/restoreDocument", {
        document_id: this.file.id
      });
    }
  },
  mounted() {
    document.addEventListener("click", this.documentMonitor);
  } //end the mounted function
};
</script>

<style scoped>
.single-file {
  --scaling-value: 0.65;

  width: calc(11rem * var(--scaling-value));
  height: calc(13rem * var(--scaling-value));

  /* transform: scale(0.9); */

  background: #ffffff;
  border: 1px solid #e2e2e2;
  box-sizing: border-box;
  border-radius: 5px;
  margin: 0.5rem;
  /* overflow: hidden; */
  padding: 0;
  display: flex;
  flex-direction: column;
}

.dark-theme .single-file {
  background: #140034;
  border: 1px solid #41325a;
}

.file-info {
  flex: 1;
  padding: 0.5rem;
  padding-top: 0;
  padding-bottom: 0.1rem;
}

.image {
  background-repeat: no-repeat;
  background-size: contain;
  background-position: center center;
}

.top-icons {
  height: 9%;
  display: flex;
  align-items: center;
  justify-content: space-between;
  position: relative;
  margin-top: 0.5rem;
}

.top-icon-dropdown {
  position: absolute;
  top: 1px;
  right: 1px;
  background: #efefef;
  box-shadow: 2px 2px 4px #c4c4c4;
  z-index: 2;
  max-width: calc(100% + 1.5rem);
  overflow: hidden;
}

.top-icon-dropdown ul {
  list-style: none;
  padding-inline-start: 0;
  padding: 0;
}
.top-icon-dropdown ul li {
  padding-right: 0.8rem;
  padding-left: 0.8rem;
  padding-top: 0.4rem;
  padding-bottom: 0.4rem;
  font-size: 0.8rem;
  cursor: pointer;
  border-radius: 5px;

  border: none;
  border-bottom: 2px solid white;
  white-space: nowrap;
  width: 100%;
  text-overflow: ellipsis;
  overflow: hidden;
}

.top-icon-dropdown ul li.demacation {
  padding: 0;
  height: 5px;
  background: #9b9b9ba1;
}

.top-icon-dropdown ul li:first-child {
  padding-top: 0;
}

.top-icon-dropdown ul li:last-child {
  border-bottom: none;
  padding-bottom: 0;
}

.top-icon-dropdown ul li:hover {
  color: black;
}

.middle-section {
  height: 50%;
  cursor: pointer;
}

.file-name {
  line-height: 0.9rem;
  font-size: 0.7rem;
  text-align: center;
  -webkit-line-clamp: 2;
  display: -webkit-box;
  -webkit-box-orient: vertical;
  overflow: hidden;
  max-height: 30%;
  min-height: 20%;
  padding-top: 0.15rem;
  cursor: pointer;
}

.file-footer {
  height: 10%;
  overflow: hidden;
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  font-size: 0.6rem;
}

.file-size {
  font-weight: bold;
}

.check,
.triple-dots {
  height: 100%;
  width: 1.5rem;
  cursor: pointer;
  background-origin: content-box;
}

.check {
  background-position: left center;
  padding-left: 0.5rem;
}

.triple-dots {
  background-position: right center;
  padding-right: 0.5rem;
}

.folder {
  background-image: url("/png/folder.png");
}

.pdf {
  background-image: url("/png/pdf-icon.png");
}

.svg {
  background-image: url("/png/svg-icon.png");
}

.jpg {
  background-image: url("/png/jpg-icon.png");
}

.png {
  background-image: url("/png/png-icon.png");
}

.doc {
  background-image: url("/png/doc-icon.png");
}

.epub {
  background-image: url("/png/epub-icon.png");
}

.tiff {
  background-image: url("/png/tiff-icon.png");
}

.txt {
  background-image: url("/png/txt-icon.png");
}

.js {
  background-image: url("/png/js-icon.png");
}

.md {
  background-image: url("/png/md-icon.png");
}

.yml {
  background-image: url("/png/yml-icon.png");
}

.dll {
  background-image: url("/png/dll-icon.png");
}

.log {
  background-image: url("/png/log-icon.png");
}

.tmp {
  background-image: url("/png/tmp-icon.png");
}

.php {
  background-image: url("/png/php-icon.png");
}

.java {
  background-image: url("/png/java-icon.png");
}

.ai {
  background-image: url("/png/ai-icon.png");
}

.exe {
  background-image: url("/png/exe-icon.png");
}

.html {
  background-image: url("/png/html-icon.png");
}

.css {
  background-image: url("/png/css-icon.png");
}

.mp3 {
  background-image: url("/png/mp3.png");
}

.m4a {
  background-image: url("/png/m4a.png");
}

.generic-file {
  background-image: url("/png/generic-file-icon.png");
}

/*Signature priviledge modal*/

.signature-plans-modal {
  position: fixed;
  z-index: 999;
  margin: 0;
  top: 0;
  bottom: 0;
  right: 0;
  left: 0;

  background: rgba(0, 0, 0, 0.25);
  display: flex;
  justify-content: center;
  align-items: center;
}

.signature-plans-modal-content {
  width: 30%;
}

@media (max-width: 1199.98px) {
  .signature-plans-modal-content {
    width: 40%;
  }
}

@media (max-width: 991.98px) {
  .signature-plans-modal-content {
    width: 60%;
  }
}

@media (max-width: 767.98px) {
  .signature-plans-modal-content {
    width: 70%;
  }
}

@media (max-width: 575.98px) {
  .signature-plans-modal-content {
    width: 80%;
  }
}
</style>
