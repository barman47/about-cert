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
        <th class="reminder-header">Send Reminder</th>
        <th class="status-header">Status</th>
        <th class="download-header">Download</th>
      </thead>
      <tbody>
        <tr v-for="sendMarker in sendMarkers" :key="sendMarker.id">
          <td>
            <div :class="{pdf: sendMarker.document_name.endsWith('.pdf')}"></div>
          </td>
          <td>
            <div class="table-document-name">{{sendMarker.document_name}}</div>
            <span>
              from
              <span
                class="table-document-sender-name"
                @click="goToPage('/profile/edit')"
              >{{$auth.user.name}}</span>
            </span>
            <br />
            <br />
            <span style="color: #9b9b9b;">{{sendMarker.time}}</span>
          </td>
          <td class="signatures-column">
            <div
              v-for="recipient in (sendMarker.id == showMoreId ? sendMarker.recipients : sendMarker.recipients.slice(0, 2))"
              :key="recipient.email"
            >
              <button
                :class="{
								rejected: sendMarker.sent == 1 && (recipient.viewed == 1 && recipient.signed == 0),
								signed: sendMarker.sent == 1 && (recipient.signed == 1),
								'not-viewed': sendMarker.sent == 1 && (recipient.viewed == 0)
							}"
                @click="recipient.is_platform_user == 1 ? goToPage('/users/' + recipient.id) : {}"
              >{{recipient.name}}</button>
            </div>
            <div class="display-flex justify-content-end" v-if="sendMarker.recipients.length > 2">
              <span
                v-if="sendMarker.id != showMoreId"
                @click="showMore(sendMarker.id)"
                style="cursor: pointer; color: #27DEBF; margin-right: 1rem; font-size: 0.7rem;"
              >More</span>
              <span
                v-else
                @click="showMore(undefined)"
                style="cursor: pointer; color: #27DEBF; margin-right: 1rem; font-size: 0.7rem;"
              >Less</span>
            </div>
          </td>
          <td class="reminder-column">
            <div
              v-for="recipient in (sendMarker.id == showMoreId ? sendMarker.recipients : sendMarker.recipients.slice(0, 2))"
              :key="recipient.email"
            >
              <button
                class="reminder"
                :class="{
								muted: sendMarker.sent == 0 || (sendMarker.sent == 1 && recipient.signed == 1), 
								active: sendMarker.sent == 1 && recipient.signed == 0
							}"
                @click="sendMarker.sent == 0 || (sendMarker.sent == 1 && recipient.signed == 1) ? {} : sendReminderToUser({signature_send_marker_id: sendMarker.id, recipient: recipient.id})"
              >Remind</button>
            </div>
          </td>
          <td class="status-column">
            <div v-if="sendMarker.sent != 0">
              <span v-if="sendMarker.executed == 1">Completed</span>
              <span v-else>Pending</span>
            </div>
            <div v-else>
              <button
                class="send-now-button"
                @click="sendSignatureRequest(sendMarker.embedded_signing_url)"
              >Send Now</button>
            </div>
          </td>
          <td class="download-column">
            <div>
              <button
                @click="sendMarker.executed != 1 ? {} : downloadDocument(sendMarker.id)"
                :class="{muted: sendMarker.executed != 1}"
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
      <MobileSendSignatureRow
        ref="mobileSignatureRows"
        @sendSignatureRequest="sendSignatureRequest($event)"
        @downloadDocument="downloadDocument($event)"
        @expanded="mobileExpandedEvent($event)"
        :sendMarker="sendMarker"
        v-for="sendMarker in sendMarkers"
        :key="sendMarker.id"
      />
    </div>
    <FolderTreeModal ref="folderTreeModal" @submitted="downloadToFolder" />
  </div>
</template>

<script>
import DocumentSignatureNav from "~/components/DocumentSignatureNav.vue";
import FolderTreeModal from "~/components/FolderTreeModal";
import MobileSendSignatureRow from "~/components/MobileSendSignatureRow";

export default {
  layout: "document",
  components: {
    DocumentSignatureNav,
    FolderTreeModal,
    MobileSendSignatureRow
  },
  async fetch({ store, $axios, params, redirect }) {
    if (!store.state.signature_documents.signatureSendMarkersLoaded) {
      await store
        .dispatch("signature_documents/fetchSignatureSendMarkers")
        .catch(() => {
          return redirect("/documents");
        });
    }
  },
  data() {
    return {
      downloadSendMarkerId: undefined,
      showMoreId: undefined
    };
  },
  computed: {
    sendMarkers() {
      return this.$store.getters["signature_documents/signatureSendMarkers"];
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
    sendSignatureRequest(embeddedUrl) {
      window.open(embeddedUrl, "_blank");
    },
    sendReminderToUser(data) {
      // console.log(data)
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
    showMore(id) {
      this.showMoreId = id;
    },
    mobileExpandedEvent(id) {
      let mobileSignatureRows = this.$refs.mobileSignatureRows
        .slice()
        .filter(el => el.sendMarker.id != id);
      for (let mobileSignatureRow of mobileSignatureRows) {
        mobileSignatureRow.collapse();
      }
    }
  },
  mounted() {
    document.addEventListener("click", this.documentMonitor);

    for (let sendMarker of this.sendMarkers) {
      try {
        this.$store.dispatch("signature_documents/addEventListenerToASendMarker", { sendMarker });
      } catch (e) {
        console.log(e);
      }
    }
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

table tbody > tr > td:first-child div.pdf {
  background-image: url("/png/pdf-icon.png");
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

table tbody tr > td.reminder-column button.active {
  background: #27debf;
  color: white;
}

table tbody tr > td.reminder-column button.muted {
  cursor: unset;
  background: #c8c8c8;
  color: #ffffff;
  cursor: not-allowed;
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

table tbody tr > td.status-column > div > button {
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

.table-document-name {
  font-size: 0.8rem;
  /*margin-top: 0.5rem;*/
  margin-top: 0;
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