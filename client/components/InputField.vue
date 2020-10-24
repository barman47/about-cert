<template>
  <div
    class="input-container"
    :class="{'with-right-image': !!rightImage, 'with-left-image': !!leftImage}"
  >
    <div
      class="input-image"
      :style="{backgroundImage: 'url(\''+ leftImage +'\')'}"
      v-if="!!leftImage"
    ></div>
    <input
      :autocomplete="autocomplete"
      ref="input"
      :type="type"
      :placeholder="placeholder"
      :name="name"
      :id="id"
    />
    <div
      class="input-image"
      :style="{backgroundImage: 'url(\''+ rightImage +'\')'}"
      v-if="!!rightImage"
    ></div>
  </div>
</template>

<script>
export default {
  props: [
    "type",
    "placeholder",
    "name",
    "id",
    "leftImage",
    "rightImage",
    "customStyle",
    "required",
    "autocomplete"
  ],
  computed: {},
  methods: {
    value() {
      return this.getInputFieldElement().value;
    },
    getInputFieldElement(){
      return this.$refs.input
    }
  },
  mounted() {
    if (this.required == true)
      this.getInputFieldElement().setAttribute("required", "true");
  }
};
</script>

<style scoped>
.input-container {
  width: 100%;
  font-size: 0.8rem;
  height: 37px;
  background-color: rgba(226, 226, 226, 0.25);
  border: 2px solid #e6e6e6;
  border-radius: 3px;
  display: grid;
  grid-template-columns: 1fr;
  padding-left: 3px;
  padding-right: 3px;
}

.input-container.with-left-image {
  grid-template-columns: 2rem 1fr;
}

.input-container.with-right-image {
  grid-template-columns: 2rem 1fr;
}

.input-container.with-left-image.with-right-image {
  grid-template-columns: 2rem 1fr 2rem;
}

.input-container * {
  height: 100%;
}

input {
  background: none;
  border: none;
  width: 100%;
  padding-left: 0.5rem;
  padding-right: 0.5rem;
}

.input-image {
  padding: 5px;
  background-repeat: no-repeat;
  background-size: contain;
  background-origin: content-box;
  background-color: transparent;
}
</style>
