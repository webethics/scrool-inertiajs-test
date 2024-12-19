<template>
  <div class="alphabet-bar">
    <button
      v-for="letter in alphabet"
      :key="letter"
      class="alphabet-button"
      @click="handleLetterClick(letter)"
      :class="{ 'active-letter': letter === activeLetter }"
    >
      {{ letter }}
    </button>
  </div>
</template>

<script>
export default {
  props: {
    alphabet: Array,
    activeLetter: String,
  },
  emits: ['letter-click'],
  methods: {
    handleLetterClick(letter) {
      this.$emit('letter-click', letter);
    },
  },
};
</script>

<style scoped>
.alphabet-bar {
  position: fixed;
  right: 0;
  top: 0;
  width: 50px;
  height: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  background-color: #f4f4f4;
}

.alphabet-button {
  margin: 5px 0;
  background: #fff;
  border: 1px solid #ccc;
  padding: 5px;
  cursor: pointer;
  font-size: 14px;
}

.active-letter {
  background-color: #ff0000;
  border-color: #ff0000;
  color: #fff;
  font-weight: 600;
}
</style>
