<template>
    <nord-card padding="l">
        <nord-stack gap="xs">
            <span class="n-truncate n-typescale-s n-color-text-weaker">
                {{ props.rss.title }}
            </span>
            <a
                class="n-typescale-m n-color-text-link"
                v-if="props.rss.lastLink"
                :href="props.rss.lastLink.link"
                target="_blank"
            >
                {{ props.rss.lastLink.title }}
            </a>
            <span
                class="n-color-text-weaker"
                v-else
            >
                {{ t('newsletters.noLink') }}
            </span>
        </nord-stack>
        <span
            slot="footer"
            class="n-typescale-s n-color-text-weaker"
            v-if="props.rss.lastLink"
        >
            {{ lastDate }}
        </span>
    </nord-card>
</template>

<script
    lang="ts"
    setup
>
import { computed } from "vue"
import { useI18n } from "vue-i18n"
import { useDateFormat } from "@/composables/useDateFormat"
import { Newsletter } from "@/modules/domain/Types"

const props = defineProps<{
    rss: Newsletter
}>()

const { t } = useI18n()
const { formatDate } = useDateFormat()

const lastDate = computed<string>(() => formatDate({ value: props.rss.lastLink?.date, withTime: true }))
</script>
