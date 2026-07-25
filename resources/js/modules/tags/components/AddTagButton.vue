<template>
    <nord-modal
        :open="isOpen"
        size="s"
        scrollable
        @close="isOpen = false"
    >
        <h2 slot="header">{{ t('tags.add') }}</h2>
        <nord-stack gap="m">
            <nord-input
                :label="t('tags.name')"
                :placeholder="t('tags.namePlaceholder')"
                required
                expand
                :value="newTagForm.name"
                :disabled="inProgress"
                :error="errorMessage"
                @input="onNameInput($event)"
            ></nord-input>

            <div
                v-if="searDone && !inProgress"
                class="n-grid-3 n-gap-s"
            >
                <nord-button
                    v-for="image in allImages"
                    :key="image"
                    :variant="newTagForm.icon === image ? 'primary' : 'default'"
                    @click="tagStore.setTagIcon(image)"
                >
                    <img
                        class="n-size-icon-l"
                        :src="image"
                    />
                </nord-button>
            </div>
        </nord-stack>
        <nord-button
            slot="footer"
            @click="isOpen = false"
        >
            {{ t('common.cancel') }}
        </nord-button>
        <nord-button
            slot="footer"
            variant="primary"
            @click="!inProgress && tagStore.searchIcon()"
            :loading="inProgress"
            v-if="!searDone"
        >
            {{ t('common.next') }}
        </nord-button>
        <nord-button
            slot="footer"
            variant="primary"
            v-else
            @click="tagStore.saveTag()"
            :disabled="!newTagForm.icon"
        >
            {{ t('common.save') }}
        </nord-button>
    </nord-modal>
    <nord-button
        :variant="props.light ? 'default' : 'primary'"
        @click="openModal()"
    >
        <nord-icon
            slot="start"
            name="interface-add"
        ></nord-icon>
        {{ t('tags.add') }}
    </nord-button>
</template>

<script
    lang="ts"
    setup
>
import { computed, onMounted, ref, watch } from "vue"
import { useI18n } from "vue-i18n"
import { TagError } from "@/modules/domain/Types"
import { useTag } from "../composables/useTag"

const isOpen = ref<boolean>(false)
const { newTagForm, tagStore, inProgress, searDone, allImages, status } = useTag()
const { t } = useI18n()

const errorCode = computed<TagError | null>(() => (newTagForm.errors.name ?? newTagForm.errors.icon ?? null) as TagError | null)

const errorMessage = computed<string>(() => (errorCode.value ? t(`errors.${errorCode.value}`) : ""))

const props = defineProps<{ light?: boolean }>()

function openModal() {
    tagStore.resetState()
    isOpen.value = true
}

function onNameInput(event: Event) {
    const value = (event.target as HTMLInputElement).value

    newTagForm.name = value
    tagStore.resetState(value)
}

onMounted(() => {
    const overlay = document.querySelector("nord-toast-group")

    watch(status, (e: string) => {
        if (e === "failed") {
            overlay?.addToast({
                variant: "danger",
                message: errorMessage.value || t(`errors.${TagError.SaveFailed}`),
                autoDismiss: 4000,
            })
        }
        if (e === "success") {
            overlay?.addToast({
                variant: "success",
                message: t("tags.created"),
                autoDismiss: 4000,
            })
            isOpen.value = false
        }
    })
})
</script>
