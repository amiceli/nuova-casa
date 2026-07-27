<template>
    <nord-card padding="l">
        <nord-stack
            direction="horizontal"
            align-items="center"
            gap="m"
        >
            <img
                class="n-size-icon-xl"
                :src="cardImage"
            />
            <nord-stack gap="xs">
                <span class="n-truncate n-typescale-m">
                    {{ props.tag.name }}
                </span>
                <span class="n-color-text-weaker n-typescale-s">
                    {{ t('tags.pageCount', { count: props.tag.children.length }) }}
                </span>
            </nord-stack>
        </nord-stack>

        <Link
            slot="footer"
            :href="route('tag', {id: props.tag.id})"
        >
            <nord-button>
                {{ t('common.edit') }}
            </nord-button>
        </Link>
    </nord-card>
</template>

<script lang="ts" setup>
import retroDefault from "@assets/404_retro.png"
import { Link } from "@inertiajs/vue3"
import { computed } from "vue"
import { useI18n } from "vue-i18n"
import { route } from "ziggy-js"
import type { Tag } from "@/modules/domain/Types"

const props = defineProps<{ tag: Tag }>()
const { t } = useI18n()

const cardImage = computed(() => (props.tag.icon.includes("404") ? retroDefault : props.tag.icon))
</script>
