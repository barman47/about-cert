<template>
  <div class="mobile-signature-row">
    <div class="top">
      <div class="collapse-icon" @click="collapse()" v-show="expanded"></div>
      <div class="expand-icon" @click="expand()" v-show="!expanded"></div>
      <div class="file-icon" :class="{pdf: receiveMarker.document_name.endsWith('.pdf')}"></div>
      <div class="document-main-details">
        <div class="name">{{receiveMarker.document_name}}</div>
        <div>
          <span class="document-sender-container">
            from &nbsp;
            <span
              class="senders-name"
              @click="goToPage('/users/' + receiveMarker.send_marker.user.id)"
            >{{receiveMarker.send_marker.user.name}}</span>
            &nbsp;
            <span>[ {{receiveMarker.time}} ]</span>
          </span>
        </div>
      </div>
    </div>
    <div class="bottom" v-show="expanded">
      <div class="signatries">
        <div class="row-header">
          SIGNATRIES
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
        <div>
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
        </div>
        <div>
          <div>
            <button
              class="reminder active"
              @click="$emit('viewAndSignDocument' , receiveMarker.embedded_signing_url)"
              v-if="receiveMarker.signed == 1"
            >View</button>
            <button
              class="reminder active"
              @click="$emit('viewAndSignDocument' , receiveMarker.embedded_signing_url)"
              v-else
            >View & Sign</button>
          </div>
        </div>
      </div>
      <div class="status">
        <div class="row-header">
          <span>STATUS</span>
        </div>
        <div class="status-specific">
          <span v-if="receiveMarker.send_marker.executed == 1">Completed</span>
          <span v-else>Pending</span>
        </div>
      </div>
      <div>
        <div class="row-header">
          <span>DOWNLOAD</span>
        </div>
        <div>
          <div>
            <button
            @click="receiveMarker.send_marker.executed != 1 ? {} : $emit('downloadDocument', receiveMarker.send_marker.id)"
              class="download-button"
              :class="{muted: receiveMarker.send_marker.executed != 1}"
            >
              Save
              <div class="icon"></div>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    receiveMarker: {
      required: true,
      type: Object
    }
  },
  data() {
    return {
      expanded: false
    };
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
    expand() {
      this.expanded = true;
      this.$emit("expanded", this.receiveMarker.id);
    },
    collapse() {
      this.expanded = false;
    },
    goToPage(path) {
      if (this.$nuxt.$route.fullPath == path) return;
      this.$nuxt.$router.push(path);
    }
  },
  mounted() {
    document.addEventListener("click", this.documentMonitor);
  }
};
</script>

<style scoped>
.mobile-signature-row {
  background: white;
  border-radius: 10px;
  padding: 0.5rem;
}

.top {
  display: grid;
  grid-template-columns: 30px 50px 1fr;
  gap: 0.5rem;
  height: 2rem;
  margin: 0.5rem;
}

.expand-icon,
.collapse-icon {
  background-origin: content-box;
  background-position: top center;
  background-repeat: no-repeat;
  background-size: contain;
  cursor: pointer;
}

.collapse-icon {
  background-image: url("/png/collapse-icon.png");
}

.expand-icon {
  background-image: url("/png/expand-icon.png");
}

.file-icon {
  background-image: url("/png/doc-icon.png");
  background-origin: content-box;
  background-position: top center;
  background-repeat: no-repeat;
  background-size: contain;
}

.file-icon.pdf {
  background-image: url("/png/pdf-icon.png");
}

.document-main-details {
  display: flex;
  flex-direction: column;
  overflow: hidden;
  white-space: nowrap;
}

.document-main-details .name {
  color: #8d8698;
  font-size: 0.9rem;
}

.document-sender-container {
  color: #9d9d9d;
  flex: 1;
  display: flex;
  align-items: flex-end;
}

.document-sender-container .senders-name {
  color: #27debf;
}

/*Question icon*/
.question-icon {
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

.question-icon.active {
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

/* Bottom Section */
.bottom {
  display: flex;
  flex-direction: column;
  margin-top: 1rem;
  padding: 0.5rem;
  padding-bottom: 1rem;
}

.bottom > div {
  display: grid;
  grid-template-columns: 6rem 1fr;
  margin-bottom: 1rem;
}

.bottom > div:last-child {
  margin-bottom: 0;
}

.bottom .signatries {
  grid-template-columns: 6rem 2fr 1fr;
  gap: 0.5rem;
}

.bottom .row-header {
  color: #b5b5b5;
}

/* signatries buttons */
button {
  font-size: 0.6rem;
  border: 0;
  background: #27debf;
  border-radius: 5px;
  color: white;
  padding: 0.3rem;
  padding-left: 0.5rem;
  padding-right: 0.5rem;
  margin-bottom: 0.3rem;
  display: inline-block;
  background: #c8c8c8;
  color: white;
  cursor: not-allowed;
}

button.rejected,
button.signed,
button.not-viewed {
  cursor: pointer;
}

button.rejected {
  color: #ffffff;
  background: rgba(255, 69, 44, 0.8);
}

button.signed {
  background: rgba(92, 192, 92, 0.9);
  white-space: nowrap;
}

button.not-viewed {
  background: rgb(253, 241, 135);
  color: #521661;
}
button.active {
  background: #27debf;
  color: white;
}

button.reminder {
  border-radius: 5px;
  cursor: pointer;
  background: white;
  color: #3a2c51;
  border: 1px solid #27de70;
}

button.muted {
  cursor: unset;
  background: #c8c8c8;
  color: #ffffff;
  cursor: not-allowed;
}

/* Status */
.status-specific {
  font-size: 0.7rem;
  color: #8d8698;
}

/* Download Button */
.download-button {
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
  cursor: pointer;
}

.download-button.muted {
  background: #c8c8c8;
  cursor: not-allowed;
}

.download-button .icon {
  background-image: url(/png/download-icon-sm.png);
  height: 0.9rem;
  width: 0.9rem;
  background-size: contain;
  background-repeat: no-repeat;
  background-position: center center;
  margin-left: 0.7rem;
}

/* // Small devices (landscape phones, less than 768px) */
@media (max-width: 767.98px) {
}

/* // Extra small devices (portrait phones, less than 576px) */
@media (max-width: 575.98px) {
}
</style>