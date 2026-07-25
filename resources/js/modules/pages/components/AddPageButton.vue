<template>
    <nord-modal
        :open="isOpen"
        size="s"
        scrollable
        @close="isOpen = false"
    >
        <h2 slot="header">{{ t('pages.addFor', { tag: props.tag.name }) }}</h2>

        <nord-stack gap="m">
            <nord-input
                :label="t('pages.url')"
                :placeholder="t('pages.urlPlaceholder')"
                required
                expand
                :value="newPageForm.url"
                :disabled="inProgress || requiredInputs"
                :error="newPageForm.errors.url"
                @input="onUrlInput($event)"
            ></nord-input>

            <nord-banner
                variant="warning"
                v-if="requiredInputs"
            >
                {{ t('pages.openGraphFailed') }}
            </nord-banner>

            <nord-stack
                v-if="requiredInputs"
                direction="horizontal"
                gap="s"
                align-items="end"
            >
                <nord-input
                    :label="t('pages.title')"
                    :placeholder="t('pages.titlePlaceholder')"
                    required
                    expand
                    :value="newPageForm.title"
                    :disabled="inProgress"
                    :error="newPageForm.errors.title"
                    @input="onTitleInput($event)"
                ></nord-input>
                <nord-button
                    square
                    :disabled="loadingImages || newPageForm.title.length === 0"
                    @click="store.requiredInputs(newPageForm.title)"
                >
                    <nord-icon
                        name="arrow-refresh"
                        :label="t('pages.scrap')"
                    ></nord-icon>
                </nord-button>
            </nord-stack>

            <div
                class="n-align-center"
                v-if="loadingImages"
            >
                <nord-spinner size="l"></nord-spinner>
            </div>

            <div
                v-if="requiredInputs && !loadingImages"
                class="n-grid-3 n-gap-s"
            >
                <nord-button
                    v-for="image in allImages"
                    :key="image"
                    :variant="newPageForm.icon === image ? 'primary' : 'default'"
                    @click="store.setPageIcon(image)"
                >
                    <img
                        class="n-size-icon-l"
                        :src="image"
                    />
                </nord-button>
            </div>

            <nord-stack
                gap="m"
                align-items="start"
                v-if="graphDone && !inProgress"
            >
                <img
                    class="n-size-icon-xxl"
                    :src="newPageForm.icon || retroDefault"
                />
                <nord-input
                    :label="t('pages.title')"
                    expand
                    :value="newPageForm.title"
                    :error="newPageForm.errors.title"
                    @input="onTitleInput($event)"
                ></nord-input>
            </nord-stack>
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
            v-if="!graphDone && !requiredInputs"
            :loading="inProgress"
            @click="store.openGraph()"
        >
            {{ t('pages.scrap') }}
        </nord-button>
        <nord-button
            slot="footer"
            variant="primary"
            v-else
            @click="store.savePage()"
        >
            {{ t('common.save') }}
        </nord-button>
    </nord-modal>

    <nord-button
        variant="primary"
        @click="openModal()"
    >
        <nord-icon
            slot="start"
            name="interface-add"
        ></nord-icon>
        {{ t('pages.addNew') }}
    </nord-button>
</template>

<script
    lang="ts"
    setup
>
import { onMounted, ref, watch } from "vue"
import { useI18n } from "vue-i18n"
import { type Tag } from "@/modules/domain/Types"
import retroDefault from "../../../../assets/404_retro.png"
import { usePage } from "../stores/PageStore"

const { store, inProgress, graphDone, newPageForm, status, requiredInputs, allImages, loadingImages } = usePage()
const { t } = useI18n()

const isOpen = ref<boolean>(false)
const props = defineProps<{
    tag: Tag
}>()

onMounted(() => {
    const overlay = document.querySelector("nord-toast-group")
    store.setTage(props.tag.id)

    watch(status, (e: string) => {
        if (e === "failed") {
            overlay?.addToast({
                variant: "danger",
                message: t("pages.saveFailed"),
                autoDismiss: 4000,
            })
        }
        if (e === "success") {
            overlay?.addToast({
                variant: "success",
                message: t("pages.saved"),
                autoDismiss: 4000,
            })
            isOpen.value = false
        }
    })
})

function onUrlInput(event: Event) {
    const value = (event.target as HTMLInputElement).value

    store.resetState(value)
}

function onTitleInput(event: Event) {
    newPageForm.title = (event.target as HTMLInputElement).value
}

function openModal() {
    store.resetState()
    isOpen.value = true
}
</script>
