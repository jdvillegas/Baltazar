<template>
    <a
        :href="href"
        class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"
        :class="{ 'text-red-700': method === 'delete' }"
        v-on="as === 'button' ? { click: handleClick } : {}"
    >
        <slot />
    </a>
</template>

<script setup>
    import { Link } from '@inertiajs/vue3';

    const props = defineProps({
        href: String,
        as: {
            type: String,
            default: 'a',
        },
        method: {
            type: String,
            default: 'get',
        },
    });

    const handleClick = (e) => {
        if (props.method !== 'get') {
            e.preventDefault();

            if (props.$page.props.csrf_token) {
                const formData = new FormData();
                formData.append('_token', props.$page.props.csrf_token);
                formData.append('_method', props.method);

                Link.post(props.href, formData);
            }
        }
    };
</script>
