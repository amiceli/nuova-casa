<template>
    <nord-card padding="l">
        <nord-stack
            direction="horizontal"
            align-items="center"
            gap="m"
        >
            <img
                class="n-size-icon-xl"
                v-if="props.rss.icon && !iconFailed"
                :src="props.rss.icon"
                :alt="props.rss.title"
                @error="onIconError()"
            />
            <nord-stack gap="xs">
                <span class="n-truncate n-typescale-s n-color-text-weaker">
                    {{ props.rss.title }}
                </span>
                <a
                    class="n-typescale-m n-color-text-link"
                    v-if="props.rss.lastLink"
                    :href="props.rss.lastLink.link"
                    target="_blank"
                    @click="markAsRead()"
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
        </nord-stack>

        <nord-stack
            slot="footer"
            direction="horizontal"
            align-items="center"
            gap="s"
            v-if="props.rss.lastLink"
        >
            <span class="n-typescale-s n-color-text-weaker">
                {{ lastDate }}
            </span>
            <nord-badge :variant="isRead ? 'success' : 'highlight'">
                {{ isRead ? t('newsletters.read') : t('newsletters.unread') }}
            </nord-badge>
        </nord-stack>
    </nord-card>
</template>

<script
    lang="ts"
    setup
>
import { computed, ref, watch } from "vue"
import { useI18n } from "vue-i18n"
import { route } from "ziggy-js"
import { useDateFormat } from "@/composables/useDateFormat"
import { postJson } from "@/lib/http"
import { Newsletter } from "@/modules/domain/Types"

const props = defineProps<{
    rss: Newsletter
}>()

const { t } = useI18n()
const { formatDate } = useDateFormat()

const isRead = ref<boolean>(props.rss.isRead)
const iconFailed = ref<boolean>(false)

const lastDate = computed<string>(() => formatDate({ value: props.rss.lastLink?.date, withTime: true }))

watch(
    () => props.rss.isRead,
    (value: boolean) => {
        isRead.value = value
    },
)

function onIconError() {
    iconFailed.value = true
}

async function markAsRead() {
    const lastLink = props.rss.lastLink

    if (!lastLink || isRead.value) {
        return
    }

    isRead.value = true

    try {
        const response = await postJson({
            url: route("read-newsletter", { newsletter: props.rss.id }),
            body: {
                link: lastLink.link,
                title: lastLink.title,
            },
        })

        if (!response.ok) {
            throw new Error(`status=${response.status}`)
        }
    } catch (e) {
        console.error(`action=mark_newsletter_as_read, status=failed, reason=${e}`)
        isRead.value = false
    }
}
</script>
