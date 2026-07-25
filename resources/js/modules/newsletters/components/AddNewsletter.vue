<template>
    <nord-modal
        :open="isOpen"
        size="s"
        @close="isOpen = false"
    >
        <h2 slot="header">{{ t('newsletters.register') }}</h2>
        <nord-input
            :label="t('pages.url')"
            :placeholder="t('newsletters.urlPlaceholder')"
            required
            expand
            :value="newNewsletter.url"
            :disabled="isLoading"
            :error="newNewsletter.errors.url"
            @input="onUrlInput($event)"
        ></nord-input>
        <nord-button
            slot="footer"
            @click="isOpen = false"
        >
            {{ t('common.cancel') }}
        </nord-button>
        <nord-button
            slot="footer"
            variant="primary"
            :loading="isLoading"
            @click="!isLoading && store.saveRss()"
        >
            {{ t('newsletters.registerAction') }}
        </nord-button>
    </nord-modal>
    <nord-button
        variant="primary"
        @click="isOpen = true"
    >
        <nord-icon
            slot="start"
            name="interface-add"
        ></nord-icon>
        {{ t('newsletters.add') }}
    </nord-button>
</template>

<script
    lang="ts"
    setup
>
import { onMounted, ref, watch } from "vue"
import { useI18n } from "vue-i18n"
import { useNewsletter } from "../stores/AddNewsletterStore"

const isOpen = ref<boolean>(false)
const { store, newNewsletter, isLoading, status } = useNewsletter()
const { t } = useI18n()

function onUrlInput(event: Event) {
    const value = (event.target as HTMLInputElement).value

    store.resetState(value)
}

onMounted(() => {
    const overlay = document.querySelector("nord-toast-group")

    watch(status, (e: string) => {
        if (e === "failed") {
            overlay?.addToast({
                variant: "danger",
                message: t("newsletters.failed"),
                autoDismiss: 4000,
            })
        }
        if (e === "success") {
            overlay?.addToast({
                variant: "success",
                message: t("newsletters.saved"),
                autoDismiss: 4000,
            })
            isOpen.value = false
        }
    })
})
</script>
