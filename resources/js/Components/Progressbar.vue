<script setup>
import { ref, computed } from 'vue';

defineProps({
    progress: {
        type: Number,
        default: 0,
    }
})

</script>
<template>
    <div class="shell absolute z-50">
        <div class="bar" :style="{ width: progress + '%' }">
            <span>{{ progress }}%</span>
        </div>
    </div>
</template>

<style>
.shell {
    height: 28px;
    width: 250px;
    border: 1px solid #aaa;
    border-radius: 13px;
    padding: 3px;
}

.bar {
    background: linear-gradient(to right, #b993d6, #8ca6db);
    height: 20px;
    width: 15px;
    border-radius: 9px;
}

.bar span {
    float: right;
    padding: 0px 5px;
    color: #fff;
    font-size: 0.9em;
}
</style>
