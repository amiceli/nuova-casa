<template>
    <nord-button
        variant="danger"
        @click="isOpen = true"
    >
        <nord-icon
            slot="start"
            name="interface-delete"
        ></nord-icon>
        {{ t('tags.remove') }}
    </nord-button>
    <nord-modal
        :open="isOpen"
        size="s"
        @close="isOpen = false"
    >
        <h2 slot="header">{{ t('tags.deleteTitle', { name: props.tag.name }) }}</h2>
        <nord-stack gap="s">
            <p>
                {{ t('tags.deleteWarn', { name: props.tag.name, count: props.tag.children.length }) }}
            </p>
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
            @click="removeTag()"
        >
            {{ t('tags.deleteConfirm') }}
        </nord-button>
    </nord-modal>
</template>

<script lang="ts" setup>
import { router } from "@inertiajs/vue3"
import { ref } from "vue"
import { useI18n } from "vue-i18n"
import { route } from "ziggy-js"
import { Tag } from "@/modules/domain/Types"

const isOpen = ref<boolean>(false)
const isRemoving = ref<boolean>(false)
const props = defineProps<{
    tag: Tag
}>()
const { t } = useI18n()

function removeTag() {
    const handler = document.querySelector("nord-toast-group")

    isRemoving.value = true

    router.delete(route("delete-tag", { id: props.tag.id }), {
        onSuccess: () => {
            isOpen.value = false
            handler?.addToast({
                variant: "success",
                message: t("tags.removed", { name: props.tag.name }),
                autoDismiss: 4000,
            })
        },
        onError: () => {
            handler?.addToast({
                variant: "danger",
                message: t("tags.removeFailed"),
                autoDismiss: 4000,
            })
        },
        onFinish: () => {
            isRemoving.value = false
        },
    })
}
</script>
