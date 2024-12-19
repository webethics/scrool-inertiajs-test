<template>
    <div class="loading-indicator">Loading...</div>
</template>

<script>
export default {};
</script>

<style scoped>
.loading-indicator {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 20px;
    color: #333;
    z-index: 10;
}
</style>
