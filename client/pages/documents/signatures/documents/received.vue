<template>
  <div class="documents-page-content">
    <DocumentSignatureNav />
    <table class="display-table">
      <thead>
        <th class="document-icon-header"></th>
        <th class="document-name-header">Document name</th>
        <th class="signatures-header">
          <div>
            Signatries
            <div class="question-icon" ref="tooltip">
              ?
              <div class="color-guide-container">
                <div class="header">Color Guide</div>
                <div class="content">
                  <ul>
                    <li style="--data-list-color: #5CC05C;">Signed</li>
                    <li style="--data-list-color: #F4DC04;">Not viewed</li>
                    <li style="--data-list-color: rgba(255, 69, 44, 1);">Viewed but not signed</li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </th>
        <th class="reminder-header">Actions</th>
        <th class="status-header">Status</th>
        <th class="download-header">Download</th>
      </thead>
      <tbody>
        <tr v-for="receiveMarker in receiveMarkers" :key="receiveMarker.id">
          <td>
            <div></div>
          </td>
          <td>
            <div class="table-document-name">{{receiveMarker.document_name}}</div>
            <span>
              from
              <span
                class="table-document-sender-name"
                @click="goToPage('/users/' + receiveMarker.send_marker.user.id)"
              >{{receiveMarker.send_marker.user.name}}</span>
            </span>
            <br />
            <br />
            <span style="color: #9b9b9b;">{{receiveMarker.time}}</span>
          </td>
          <td class="signatures-column">
            <div v-for="recipient in receiveMarker.send_marker.recipients" :key="recipient.email">
              <button
                :class="{
								rejected: (recipient.viewed == 1 && recipient.signed == 0),
								signed: (recipient.signed == 1),
								'not-viewed': (recipient.viewed == 0)
							}"
                @click="recipient.is_platform_user == 1 ? goToPage($auth.user.id == recipient.id ? '/profile/edit' : '/users/' + recipient.id) : {}"
              >{{recipient.name}}</button>
            </div>
          </td>
          <td class="reminder-column">
            <div>
              <button
                class="reminder active"
                @click="viewAndSignDocument(receiveMarker.embedded_signing_url)"
                v-if="receiveMarker.signed == 1"
              >View</button>
              <button
                class="reminder active"
                @click="viewAndSignDocument(receiveMarker.embedded_signing_url)"
                v-else
              >View & Sign</button>
            </div>
          </td>
          <td>
            <span v-if="receiveMarker.send_marker.executed == 1">Completed</span>
            <span v-else>Pending</span>
          </td>
          <td class="download-column">
            <div>
              <button
                @click="receiveMarker.send_marker.executed != 1 ? {} : downloadDocument(receiveMarker.send_marker.id)"
                :class="{muted: receiveMarker.send_marker.executed != 1}"
              >
                Save
                <div class="icon"></div>
              </button>
            </div>
          </td>
        </tr>
      </tbody>
    </table>

    <div class="display-mobile">
      <MobileReceiveSignatureRow
        ref="mobileSignatureRows"
        @viewAndSignDocument="viewAndSignDocument($event)"
        @downloadDocument="downloadDocument($event)"
        @expanded="mobileExpandedEvent($event)"
        :receiveMarker="receiveMarker"
        v-for="receiveMarker in receiveMarkers"
        :key="receiveMarker.id"
      />
    </div>
    <FolderTreeModal ref="folderTreeModal" @submitted="downloadToFolder" />
  </div>
</template>

<script>
import DocumentSignatureNav from "~/components/DocumentSignatureNav.vue";
import FolderTreeModal from "~/components/FolderTreeModal";
import MobileReceiveSignatureRow from "~/components/MobileReceiveSignatureRow";

export default {
  layout: "document",
  components: {
    DocumentSignatureNav,
    FolderTreeModal,
    MobileReceiveSignatureRow
  },
  async fetch({ store, $axios, params, redirect }) {
    if (!store.state.signature_documents.signatureReceivedMarkersLoaded) {
      await $axios
        .get("/api/documents/signatures/documents/received-markers")
        .then(response => {
          store.commit(
            "signature_documents/commitReceivedMarkers",
            response.data
          );
        })
        .catch(() => {
          return redirect("/documents");
        });
    }
  },
  computed: {
    receiveMarkers() {
      return this.$store.getters[
        "signature_documents/signatureReceivedMarkers"
      ];
    }
  },
  methods: {
    documentMonitor(e) {
      this.documentTooltipMonitor(e);
    },
    documentTooltipMonitor(e) {
      const path = e.path || (e.composedPath ? e.composedPath() : []);
      // debugger

      if (path.length == 0) return;

      if (e.target == this.$refs.tooltip) {
        if (!this.$refs.tooltip.classList.contains("active")) {
          this.$refs.tooltip.classList.add("active");
        }
        return;
      }
      if (!path.some(el => el == this.$refs.tooltip)) {
        if (this.$refs.tooltip.classList.contains("active")) {
          this.$refs.tooltip.classList.remove("active");
        }
      }
    },
    goToPage(path) {
      if (this.$nuxt.$route.fullPath == path) return;
      this.$nuxt.$router.push(path);
    },
    viewAndSignDocument(embeddedUrl) {
      window.open(embeddedUrl, "_blank");
    },
    openDownloadToFolderModal() {
      this.$refs.folderTreeModal.openModal();
    },
    closeDownloadToFolderModal() {
      this.$refs.folderTreeModal.closeModal();
    },
    downloadDocument(val) {
      this.downloadSendMarkerId = val;
      this.openDownloadToFolderModal();
    },
    downloadToFolder(val) {
      const data = {
        signature_send_marker_id: this.downloadSendMarkerId,
        folder_id: val
      };

      let attempt = this.$store.dispatch(
        "signature_documents/downloadSignedDocumentToFolder",
        data
      );

      attempt.then(data => {
        this.closeDownloadToFolderModal();
      });
    },
    mobileExpandedEvent(id) {
      let mobileSignatureRows = this.$refs.mobileSignatureRows
        .slice()
        .filter(el => el.receiveMarker.id != id);
      for (let mobileSignatureRow of mobileSignatureRows) {
        mobileSignatureRow.collapse();
      }
    }
  },
  mounted() {
    document.addEventListener("click", this.documentMonitor);
  }
};
</script>

<style>
#other-content {
  padding: 0.7rem;
}
</style>

<style scoped>
.documents-page-content {
  color: #7574a0;
  background: white;
  border-radius: 10px;
  padding: 0.8rem;
  min-height: 100%;
  font-size: 0.6rem;
}

.display-mobile {
  display: none;
}

table {
  width: 100%;
  text-align: left;
  border-collapse: collapse;
  border: 1px solid #f0f0f0;
}

table thead {
  text-transform: uppercase;
  font-size: 0.7rem;
  background: #f8f8f8;
}

table tbody tr:nth-child(even) {
  background: rgba(248, 248, 248, 0.6);
}

table th,
table td {
  border: 1px solid #f0f0f0;
  border-left: none;
  border-right: none;
}

table thead th {
  padding: 0.2rem;
}

table thead th.document-icon-header {
  width: 60px;
}

table thead th.document-name-header {
  width: 25%;
}

table thead th.signatures-header {
  width: 25%;
}

table thead th.signatures-header > div {
  display: inline-flex;
  align-items: center;
}

/*Question icon*/

table thead th.signatures-header .question-icon {
  margin-left: 0.3rem;
  height: 1rem;
  width: 1rem;
  border-radius: 50%;
  background: red;
  background: #3a2c51;
  display: inline-flex;
  justify-content: center;
  align-items: center;
  color: white;
  cursor: pointer;
  position: relative;
}

table thead th.signatures-header .question-icon.active {
  cursor: initial;
}

.question-icon.active * {
  font-family: Poppins;
}

.question-icon.active::before {
  content: "";
  position: absolute;
  top: 100%;
  left: calc(50% - 5px);
  border-right: 5px solid transparent;
  border-left: 5px solid transparent;
  border-top: none;
  border-bottom: 7px solid #00c7a5;
}

.question-icon .color-guide-container {
  display: none;
}

.question-icon.active .color-guide-container {
  background: #3a2c51;
  position: absolute;
  top: calc(100% + 7px);
  left: -30px;
  z-index: 15;
  border-radius: 5px;
  overflow: hidden;
  text-transform: none;
  display: block;
}

.color-guide-container .header {
  min-width: 10rem;
  height: 30px;
  display: flex;
  align-items: center;
  background: #00c7a5;
  padding-left: 0.5rem;
  padding-right: 0.5rem;
}

.color-guide-container .content {
  font-size: 0.7rem;
  white-space: nowrap;
  /*padding-left: 0.7rem;*/
  padding-right: 0.7rem;
}

.color-guide-container .content ul {
  list-style: none;
  padding-left: 30px;
}

.color-guide-container .content ul li {
  position: relative;
  font-weight: 300;
  font-family: Poppins;
  --data-list-color: white;
}

.color-guide-container .content ul li::before {
  content: "";
  position: absolute;
  background: var(--data-list-color);

  height: 10px;
  width: 10px;
  top: calc(50% - 5px);
  right: calc(100% + (30px / 2) - 10px / 2);

  border-radius: 3px;
}

table thead th.reminder-header {
  width: 15%;
}
table thead th.download-header {
  width: 15%;
}

table tbody td {
  vertical-align: top;
  padding-top: 0.5rem;
  padding-bottom: 0.5rem;
}

table tbody > tr > td:first-child div {
  padding: 0.2rem;
  height: 2.5rem;
  width: 100%;
  background-image: url("/png/doc-icon.png");
  background-origin: content-box;
  background-position: top center;
  background-repeat: no-repeat;
  background-size: contain;
}

table tbody tr > td.signatures-column button,
table tbody tr > td.reminder-column button {
  font-size: 0.6rem;
  border: 0;
  background: #27debf;
  border-radius: 3px;
  color: white;
  padding: 0.3rem;
  padding-left: 0.5rem;
  padding-right: 0.5rem;
  margin-bottom: 0.3rem;
  display: inline-block;
}

table tbody tr > td.signatures-column button {
  border-radius: 5px 10px;
  background: #c8c8c8;
  color: white;
  cursor: not-allowed;
}

table tbody tr > td.signatures-column button.rejected,
table tbody tr > td.signatures-column button.signed,
table tbody tr > td.signatures-column button.not-viewed {
  cursor: pointer;
}

table tbody tr > td.signatures-column button.rejected {
  color: #ffffff;
  background: rgba(255, 69, 44, 0.8);
}

table tbody tr > td.signatures-column button.signed {
  background: rgba(92, 192, 92, 0.9);
  white-space: nowrap;
}

table tbody tr > td.signatures-column button.not-viewed {
  background: rgb(253, 241, 135);

  /*background: #F4DC04;*/
  color: #521661;
}

table tbody tr > td.reminder-column button {
  background: white;
  color: #3a2c51;
  border: 1px solid #27de70;
}

table tbody tr > td.reminder-column button.muted {
  cursor: unset;
  background: #c8c8c8;
  color: #ffffff;
}

table tbody tr > td.signatures-column > button:last-child {
  margin-bottom: 2px;
}

table tbody tr > td.download-column > div > button {
  font-size: 0.7rem;
  border: 0;
  background: #0084ff;
  border-radius: 3px;
  color: white;
  padding: 0.4rem;
  padding-left: 0.6rem;
  padding-right: 0.6rem;
  margin-bottom: 0.3rem;
  display: inline-flex;
  flex-wrap: nowrap;
  align-items: center;
}

table tbody tr > td.download-column > div > button.muted {
  background: #c8c8c8;
  cursor: not-allowed;
}

table tbody tr > td.download-column > div > button .icon {
  background-image: url(/png/download-icon-sm.png);
  height: 0.9rem;
  width: 0.9rem;
  background-size: contain;
  background-repeat: no-repeat;
  background-position: center center;
  margin-left: 0.7rem;
}

.table-document-name {
  font-size: 0.8rem;
  margin-top: 0.5rem;
  margin-bottom: 0.2rem;
}

.table-document-sender-name {
  color: #27debf;
  cursor: pointer;
}


/* // Large devices (desktops, less than 1200px) */
@media (max-width: 1199.98px) {
}

/* // Medium devices (tablets, less than 992px) */
@media (max-width: 991.98px) {
}

/* // Small devices (landscape phones, less than 768px) */
@media (max-width: 767.98px) {
  .documents-page-content {
    background: none;
    padding: 0;
  }

  .display-table {
    display: none;
  }

  .display-mobile {
    display: block;
  }

  .display-mobile > * {
    margin-bottom: 0.5rem;
  }
}

/* // Extra small devices (portrait phones, less than 576px) */
@media (max-width: 575.98px) {
}
</style>