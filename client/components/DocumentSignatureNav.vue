<template>
  <ul class="document-signature-nav">
    <li
      @click="goToPage('/documents/signatures/documents/sent')"
      :class="{active: $nuxt.$route.fullPath == '/documents/signatures/documents/sent'}"
    >Sent Requests</li>
    <li
      @click="goToPage('/documents/signatures/documents/received')"
      :class="{active: $nuxt.$route.fullPath == '/documents/signatures/documents/received'}"
    >Received Requests</li>
  </ul>
</template>

<script >
export default {
  methods: {
    goToPage(path) {
      if (this.$nuxt.$route.fullPath == path) return;
      this.$nuxt.$router.push(path);
    }
  }
};
</script>

<style scoped>
.document-signature-nav * {
  font-family: Montserrat;
}
.document-signature-nav {
  display: flex;
  align-items: center;
  padding: 0;
  list-style: none;
  margin-bottom: 0.7rem;
  margin-top: 0.1rem;
  background: white;
  padding-left: 1rem;
  padding-right: 1rem;
  border-radius: 5px;
}

.document-signature-nav li {
  margin-right: 1rem;
  white-space: nowrap;
  color: #9b9b9b;
  font-size: 0.8rem;
  cursor: pointer;
  padding-top: 0.5rem;
  padding-bottom: 0.5rem;
}

.document-signature-nav li:last-child {
  margin-right: 0;
}

.document-signature-nav li:hover {
  border-bottom: 1px solid #9b9b9b;
}

.document-signature-nav li.active {
  border: none;
  font-size: 1rem;
  color: #0084ff;
  position: relative;
}

.document-signature-nav li.active::after {
  content: "";
  position: absolute;
  width: 70px;
  max-width: 80%;
  height: 0;
  border-top: 1.5px solid #0084ff;
  bottom: 0;
  left: 0;
}
</style>