<template>
    <nord-modal
        ref="modal"
        :open="isOpen"
        size="m"
        @close="onClose($event)"
    >
        <h2 slot="header">{{ t('newsletters.register') }}</h2>

        <nord-stack gap="m">
            <nord-combobox
                :label="t('newsletters.pick')"
                :placeholder="t('newsletters.pickPlaceholder')"
                :loading="isCatalogLoading"
                :no-results-text="t('newsletters.noResult')"
                :disabled="isLoading"
                :value="selectedId"
                @change="onSelect($event)"
                @clear="onClear()"
            >
                <nord-combobox-option
                    v-for="item in catalog"
                    :key="item.id"
                    :value="String(item.id)"
                    :label="item.name"
                >
                    {{ item.name }}
                </nord-combobox-option>
            </nord-combobox>

            <nord-card
                padding="m"
                v-if="selected"
            >
                <nord-stack
                    direction="horizontal"
                    gap="m"
                    align-items="center"
                >
                    <img
                        class="n-size-icon-xl"
                        v-if="selected.icon && !iconFailed"
                        :src="selected.icon"
                        :alt="selected.name"
                        @error="onIconError()"
                    />
                    <nord-stack gap="xs">
                        <span class="n-typescale-m">{{ selected.name }}</span>
                        <span
                            class="n-typescale-s n-color-text-weaker"
                            v-if="selected.description"
                        >
                            {{ selected.description }}
                        </span>
                        <a
                            class="n-typescale-s n-color-text-link n-truncate"
                            :href="selected.url"
                            target="_blank"
                        >
                            {{ selected.url }}
                        </a>
                    </nord-stack>
                </nord-stack>

                <nord-stack
                    slot="footer"
                    direction="horizontal"
                    gap="s"
                    align-items="center"
                >
                    <nord-badge v-if="selected.category">
                        {{ selected.category }}
                    </nord-badge>
                    <span
                        class="n-typescale-s n-color-text-weaker"
                        v-if="selected.author"
                    >
                        {{ t('newsletters.by', { author: selected.author }) }}
                    </span>
                </nord-stack>
            </nord-card>

            <nord-input
                :label="t('newsletters.feedUrl')"
                :placeholder="t('newsletters.urlPlaceholder')"
                :hint="isFeedLoading ? t('newsletters.feedSearch') : t('newsletters.feedHint')"
                required
                expand
                :value="newNewsletter.url"
                :disabled="isLoading || isFeedLoading"
                :error="urlError"
                @input="onUrlInput($event)"
            ></nord-input>
        </nord-stack>

        <nord-button
            slot="footer"
            @click="close()"
        >
            {{ t('common.cancel') }}
        </nord-button>
        <nord-button
            slot="footer"
            variant="primary"
            :loading="isLoading"
            :disabled="!canSave"
            @click="canSave && store.saveRss()"
        >
            {{ t('newsletters.registerAction') }}
        </nord-button>
    </nord-modal>

    <nord-button
        variant="primary"
        @click="open()"
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
import { computed, onMounted, ref, watch } from "vue"
import { useI18n } from "vue-i18n"
import { NewsletterError } from "@/modules/domain/Types"
import { useNewsletter } from "../stores/AddNewsletterStore"

const isOpen = ref<boolean>(false)
const iconFailed = ref<boolean>(false)
const modal = ref<HTMLElement | null>(null)

const { store, newNewsletter, isLoading, isCatalogLoading, isFeedLoading, status, catalog, selected, selectedId, canSave } = useNewsletter()
const { t, te } = useI18n()

const urlError = computed<string>(() => {
    const error = newNewsletter.errors.url

    if (!error) {
        return ""
    }

    return te(`errors.${error}`) ? t(`errors.${error}`) : t(`errors.${NewsletterError.SaveFailed}`)
})

// closing the modal empties the combobox, the feed input and the newsletter details
watch(isOpen, (value: boolean) => {
    if (value) {
        return
    }

    iconFailed.value = false

    store.resetState()
})

function open() {
    isOpen.value = true
    iconFailed.value = false

    store.prepare()
}

function close() {
    isOpen.value = false
}

/**
 * The combobox closes its option list with a close event too, and it bubbles up to the modal.
 */
function onClose(event: Event) {
    if (event.target !== modal.value) {
        return
    }

    close()
}

function onSelect(event: Event) {
    iconFailed.value = false

    store.selectNewsletter((event.target as HTMLInputElement).value)
}

function onClear() {
    iconFailed.value = false

    store.resetState()
}

function onIconError() {
    iconFailed.value = true
}

function onUrlInput(event: Event) {
    store.setFeedUrl((event.target as HTMLInputElement).value)
}

onMounted(() => {
    const overlay = document.querySelector("nord-toast-group")

    watch(status, (e: string | null) => {
        if (e === "failed") {
            overlay?.addToast(t("newsletters.failed"), {
                variant: "danger",
                autoDismiss: 4000,
            })
        }
        if (e === "success") {
            overlay?.addToast(t("newsletters.saved"), {
                autoDismiss: 4000,
            })
            isOpen.value = false
        }
    })
})
</script>
