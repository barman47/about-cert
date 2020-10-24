<template>
  <div class="folder-tree-modal" v-if="display">
    <div class="modal-content">
      <div class="header">
        <span>
          <slot name="headerText">Document Tree</slot>
        </span>
        <span @click="closeModal()" class="close-modal">&times;</span>
      </div>
      <div class="content">
        <folder-tree :checkObj="folderTreeReference" :isRoot="true"></folder-tree>

        <div class="submit-button-container">
          <button @click="$emit('submitted', folderTreeReference.id)">
            <slot name="submitButtonText">Submit</slot>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import FolderTree from "~/components/FolderTree";

export default {
  name: "FolderTreeModal",
  components: {
    FolderTree
  },
  props: {},
  data() {
    return {
      display: false,
      folderTreeReference: {
        id: undefined
      }
    };
  },
  methods: {
    openModal() {
      this.display = true;
    },
    closeModal() {
      this.display = false;
    }
  }
};
</script>

<style scoped>
.folder-tree-modal {
  position: fixed;
  top: 0;
  bottom: 0;
  right: 0;
  left: 0;
  right: 0;
  min-height: 100vh;
  min-height: var(--viewport-height, 100vh);
  z-index: 999;

  display: flex;
  justify-content: center;
  align-items: flex-start;

  background: rgba(0, 0, 0, 0.25);

  padding-top: 2rem;
  padding-bottom: 2rem;
  padding-right: 0.5rem;
  padding-left: 0.5rem;
  font-size: 0.8rem;

  --folder-tree-modal-padding-side: 0.5rem;

  /* height: 100vh; */

  /* overflow: hidden; */
  overflow-y: auto;
}

.modal-content {
  background: #555286;
  width: 30%;
  position: relative;
}

.header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  color: #3a2c51;
  background: white;
  padding-left: var(--folder-tree-modal-padding-side);
  padding-right: var(--folder-tree-modal-padding-side);
}

.content {
  padding-left: var(--folder-tree-modal-padding-side);
  padding-right: var(--folder-tree-modal-padding-side);
}

.close-modal {
  min-width: 2rem;
  height: 100%;
  font-size: 1.5rem;
  cursor: pointer;

  display: flex;
  align-items: center;
  justify-content: flex-end;
}

.submit-button-container {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  padding: var(--folder-tree-modal-padding-side);
  /* padding-top: 1rem;
        padding-bottom: 1rem; */
}

.submit-button-container button {
  border: none;
  background: #27debf;
  color: white;
  font-size: 0.7rem;
  height: 1.8rem;
  border-radius: 3px;
}

/* // Large devices (desktops, less than 1200px) */
@media (max-width: 1199.98px) {
  .modal-content {
    width: 50%;
  }
}

/* // Medium devices (tablets, less than 992px) */
@media (max-width: 991.98px) {
  .modal-content {
    width: 70%;
  }
}

/* // Small devices (landscape phones, less than 768px) */
@media (max-width: 767.98px) {
  .modal-content {
    width: 80%;
  }
}

/* // Extra small devices (portrait phones, less than 576px) */
@media (max-width: 575.98px) {
  .modal-content {
    width: 90%;
  }
}
</style>