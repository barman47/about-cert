<template>
  <form :id="id" @click="modalClicked()" class="modal form" @submit.prevent="$emit('submitted')">
    <div class="modal-container" :class="{sm: size == 'sm'}">
      <div class="header">
        <slot name="header">Modal header</slot>
        <span class="close-modal" @click="closeModal()">
          <slot name="closeTimes">&times;</slot>
        </span>
      </div>
      <div class="body">
        <slot>...</slot>
      </div>
      <div class="footer">
        <slot name="footer">
          <div class="footer-button-container">
            <button class="footer-button cancel" @click="closeModal()">Close</button>
          </div>
        </slot>
      </div>
    </div>
  </form>
</template>

<script>
export default {
  props: ["id", "size"], // size=''|sm
  methods: {
    $(id) {
      return document.getElementById(id);
    },
    modalClicked() {
      if (event.target == this.$(this.id)) this.closeModal();
    },
    closeModal() {
      this.$(this.id).style.display = "none";
    },
    openModal() {
      this.$(this.id).style.display = "block";
    }
  }
};
</script>

<style scoped>
.modal {
  display: none;
  position: fixed;
  z-index: 999;
  top: 0;
  right: 0;
  min-height: 100%;
  width: 100%;
  background: transparent;
  background: rgba(0, 0, 0, 0.4);
  overflow-y: auto;
  margin: 0;
}
.modal-container {
  --this-width: 50%;
  top: 70px;
  right: calc(50% - var(--this-width) / 2);
  width: var(--this-width);
  background: white;
  position: absolute;
  border-radius: 10px;
  overflow: hidden;
  margin-bottom: 150px;
}

.modal-container.sm {
  --this-width: 25%;
}

.header {
  height: 50px;
  background: #ecf6ff;
  padding: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 1.1rem;
  padding-left: 1rem;
  padding-right: 1rem;
}

.close-modal {
  cursor: pointer;
  font-size: 1.5rem;
  padding-left: 1rem;
  padding-right: 1rem;
  transform: translateX(1rem);
}

.body {
  padding: 1rem;
}

.footer {
  /* height: 100px; */
  display: flex;
  justify-content: flex-end;
  padding: 0.5rem;
  margin-top: 1rem;
  margin-bottom: 1rem;
  padding-right: 1rem;
  padding-left: 1rem;
  align-items: center;
}

.footer-button {
  height: 37px;
  border-radius: 5px;
  padding-left: 1rem;
  padding-right: 1rem;
  border: none;
  font-size: 1.1rem;
  cursor: pointer;
}

.footer-button-container {
  padding-right: 1rem;
  padding-left: 1rem;
}

.footer-button.cancel {
  background: transparent;
  color: black;
}

@media (max-width: 991.98px) {
  .modal-container {
    --this-width: 70%;
  }

  .modal-container.sm {
    --this-width: 40%;
  }
}

@media (max-width: 767.98px) {
  .modal-container {
    --this-width: 80%;
  }

  .modal-container.sm {
    --this-width: 50%;
  }
}

@media (max-width: 575.98px) {
  .modal-container {
    --this-width: 93%;
  }

  .modal-container.sm {
    --this-width: 70%;
  }
}
</style>
