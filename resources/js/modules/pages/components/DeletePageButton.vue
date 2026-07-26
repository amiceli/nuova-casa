<template>
    <nord-button
        variant="danger"
        square
        @click="openModal()"
    >
        <nord-icon
            name="interface-delete"
            :label="t('pages.deleteConfirm')"
        ></nord-icon>
    </nord-button>
    <nord-modal
        :open="isOpen"
        size="s"
        @close="isOpen = false"
    >
        <h2 slot="header">{{ t('pages.deleteTitle') }}</h2>
        <nord-stack gap="s">
            <p>{{ t('pages.deleteWarn', { title: props.page.title, tag: props.page.parent.name }) }}</p>
            <p class="n-color-text-weaker n-typescale-s">
                {{ t('common.irreversible') }}
            </p>
        </nord-stack>
        <nord-button
            slot="footer"
            :disabled="isRemoving"
            @click="isOpen = false"
        >
            {{ t('common.cancel') }}
        </nord-button>
        <nord-button
            slot="footer"
            variant="danger"
            :loading="isRemoving"
            @click="removePage()"
        >
            {{ t('pages.deleteConfirm') }}
        </nord-button>
    </nord-modal>
</template>

<script
    lang="ts"
    setup
>
import { router } from "@inertiajs/vue3"
import { ref } from "vue"
import { useI18n } from "vue-i18n"
import { route } from "ziggy-js"
import { Page } from "@/modules/domain/Types"

const props = defineProps<{ page: Page }>()
const isOpen = ref<boolean>(false)
const isRemoving = ref<boolean>(false)
const { t } = useI18n()

function openModal() {
    isOpen.value = true
}

function removePage() {
    const handler = document.querySelector("nord-toast-group")

    isRemoving.value = true

    router.delete(route("delete-page", { id: props.page.id }), {
        onSuccess: () => {
            isOpen.value = false
            handler?.addToast({
                variant: "success",
                message: t("pages.removed"),
                autoDismiss: 4000,
            })
        },
        onError: () => {
            handler?.addToast({
                variant: "danger",
                message: t("pages.removeFailed"),
                autoDismiss: 4000,
            })
        },
        onFinish: () => {
            isRemoving.value = false
        },
    })
}
</script>
