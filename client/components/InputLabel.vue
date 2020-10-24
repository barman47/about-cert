<template>
  <label :for="inputId"> <slot></slot> </label>
</template>

<script>
export default {
    props: ["inputId"]
};
</script>

<style scoped>
  label {
    color: #9b9b9b;
    font-size: 14px;
    font-style: normal;
  }
</style>