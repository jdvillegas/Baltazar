<template>
    <div class="relative" ref="dropdown">
        <button
            @click="open = !open"
            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150"
        >
            <slot name="trigger" />
        </button>
        
        <div
            v-if="open"
            class="absolute right-0 w-48 mt-2 origin-top-right rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
            role="menu"
            aria-orientation="vertical"
            aria-labelledby="user-menu"
        >
            <div class="py-1" role="none">
                <slot name="content" />
            </div>
        </div>
    </div>
</template>

<script setup>
    import { onMounted, onUnmounted, ref } from 'vue';

    const open = ref(false);
    const dropdown = ref(null);

    const close = () => {
        open.value = false;
    };

    onMounted(() => {
        const clickHandler = ({ target }) => {
            if (!dropdown.value.contains(target)) {
                close();
            }
        };

        document.addEventListener('click', clickHandler);

        onUnmounted(() => {
            document.removeEventListener('click', clickHandler);
        });
    });

    defineExpose({ open, dropdown, close });
</script>
